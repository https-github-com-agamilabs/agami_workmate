import os
import time
import threading
import pyautogui
import requests
from datetime import datetime
from pynput import keyboard
from threading import Lock
from PIL import Image
from pystray import Icon, MenuItem as item

# === CONFIGURATION ===
INTERVAL = 60  # seconds
EMPLOYEE_ID = "123"  # Use unique employee ID per user
UPLOAD_URL = "http://yourserver.com/upload.php"  # <- change this to your PHP endpoint
SAVE_FOLDER = "screenshots"

# === GLOBAL VARIABLES ===
char_count = 0
char_lock = Lock()
is_running = True

# === SETUP FOLDERS ===
os.makedirs(SAVE_FOLDER, exist_ok=True)

# === KEYSTROKE MONITORING ===
def on_press(key):
    global char_count
    try:
        if hasattr(key, 'char') and key.char:
            with char_lock:
                char_count += 1
    except:
        pass  # Skip special keys

def start_key_listener():
    listener = keyboard.Listener(on_press=on_press)
    listener.daemon = True
    listener.start()

# === SCREENSHOT + UPLOAD ===
def take_and_upload_screenshot():
    global char_count

    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = f"{EMPLOYEE_ID}_{timestamp}.png"
    filepath = os.path.join(SAVE_FOLDER, filename)

    # Take screenshot
    screenshot = pyautogui.screenshot()
    screenshot.save(filepath)

    # Get and reset char count
    with char_lock:
        typed = char_count
        char_count = 0

    print(f"[{timestamp}] Screenshot taken. Keystrokes: {typed}")

    try:
        with open(filepath, 'rb') as img_file:
            response = requests.post(
                UPLOAD_URL,
                files={'screenshot': (filename, img_file)},
                data={'keystrokes': str(typed), 'employee_id': EMPLOYEE_ID}
            )
        print(f"[{timestamp}] Uploaded. Server responded: {response.status_code}")
    except Exception as e:
        print(f"Upload failed: {e}")

# === SCHEDULER THREAD ===
def scheduler_loop():
    while is_running:
        take_and_upload_screenshot()
        time.sleep(INTERVAL)

# === TRAY APP ===
def quit_app(icon, item):
    global is_running
    is_running = False
    icon.stop()

def setup_tray():
    image = Image.new('RGB', (64, 64), color=(255, 255, 255))
    menu = (item('Quit', quit_app),)
    icon = Icon("RemoteMonitor", image, "Remote Monitor", menu)
    threading.Thread(target=icon.run).start()

# === MAIN ===
def main():
    print("Remote Monitoring App started.")
    start_key_listener()
    setup_tray()

    scheduler_thread = threading.Thread(target=scheduler_loop)
    scheduler_thread.daemon = True
    scheduler_thread.start()

    try:
        while is_running:
            time.sleep(1)
    except KeyboardInterrupt:
        print("Exiting...")

if __name__ == "__main__":
    main()
