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
from PIL import ImageDraw, ImageFont

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

    now = datetime.now()
    timestamp = now.strftime("%Y%m%d_%H%M%S")               # for filename
    time_display = now.strftime("%Y-%m-%d %H:%M:%S")        # for text on image
    filename = f"{EMPLOYEE_ID}_{timestamp}.png"
    filepath = os.path.join(SAVE_FOLDER, filename)

    # Take screenshot
    screenshot = pyautogui.screenshot()

    # Get and reset character count
    with char_lock:
        typed = char_count
        char_count = 0

    # Create draw object
    draw = ImageDraw.Draw(screenshot)
    text = f"{time_display} | Keystrokes: {typed}"

    # Load font
    try:
        font = ImageFont.truetype("arial.ttf", 20)
    except:
        font = ImageFont.load_default()

    # Calculate text size
    try:
        bbox = draw.textbbox((0, 0), text, font=font)
        text_width = bbox[2] - bbox[0]
        text_height = bbox[3] - bbox[1]
    except AttributeError:
        text_width, text_height = font.getsize(text)

    # Bottom-right positioning
    x = screenshot.width - text_width - 20
    y = screenshot.height - text_height - 20

    # Draw white background behind text
    margin = 5
    draw.rectangle(
        [x - margin, y - margin, x + text_width + margin, y + text_height + margin],
        fill="white"
    )

    # Draw red text
    draw.text((x, y), text, fill="red", font=font)

    # Save image
    screenshot.save(filepath)
    print(f"[{timestamp}] Screenshot saved | {text}")

    # Upload to server
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

# def take_and_upload_screenshot():
#     global char_count

#     timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
#     filename = f"{EMPLOYEE_ID}_{timestamp}.png"
#     filepath = os.path.join(SAVE_FOLDER, filename)

#     # Take screenshot
#     screenshot = pyautogui.screenshot()
#     screenshot.save(filepath)

#     # Get and reset char count
#     with char_lock:
#         typed = char_count
#         char_count = 0

#     print(f"[{timestamp}] Screenshot taken. Keystrokes: {typed}")

#     try:
#         with open(filepath, 'rb') as img_file:
#             response = requests.post(
#                 UPLOAD_URL,
#                 files={'screenshot': (filename, img_file)},
#                 data={'keystrokes': str(typed), 'employee_id': EMPLOYEE_ID}
#             )
#         print(f"[{timestamp}] Uploaded. Server responded: {response.status_code}")
#     except Exception as e:
#         print(f"Upload failed: {e}")

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
