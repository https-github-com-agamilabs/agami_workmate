## Required Python Packages
pip install pyautogui cryptography requests pystray pillow schedule
pip install pynput

## Run Program
python remote_monitor.py

## Make Executable
pip install pyinstaller

pyinstaller --noconfirm --onefile --windowed --icon=icon.ico remote_monitor.py
