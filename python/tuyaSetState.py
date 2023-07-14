import tinytuya
from six.moves import urllib
import json
import sys

if(len(sys.argv) == 6):
    # load a specific light
    device_id = sys.argv[1]
    ip_address = sys.argv[2]
    local_key = sys.argv[3]
    device_type = sys.argv[4]
    target_state = (sys.argv[5] == "1")
    print(f"Device ID: {device_id}")
    print(f"IP Address: {ip_address}")
    print(f"Local Key: {local_key}")
    print(f"Device Type: {device_type}")
    print(f"Target State: {target_state}")
elif(len(sys.argv) == 7):
    # load a specific light
    device_id = sys.argv[1]
    ip_address = sys.argv[2]
    local_key = sys.argv[3]
    device_type = sys.argv[4]
    target_state = (sys.argv[5] == "1")
    brightness = int(float(sys.argv[6]) * 1000)
    print(f"Device ID: {device_id}")
    print(f"IP Address: {ip_address}")
    print(f"Local Key: {local_key}")
    print(f"Device Type: {device_type}")
    print(f"Target State: {target_state}")
    print(f"Brightness: {brightness}")
if(len(sys.argv) == 3):
    device_id = sys.argv[1]
    target_state = (sys.argv[2] == "1")
    print(f"Device ID: {device_id}")
    print(f"Target State: {target_state}")
    # Get Tuya
    result = urllib.request.urlopen(f'http://localhost/plugins/NullLights/api/tuya?id={device_id}')
    tuyas = json.loads(result.read())
    ip_address = tuyas['tuya']['url']
    local_key = tuyas['tuya']['local_key']
    device_type = tuyas['tuya']['product_type']
    print(f"IP Address: {ip_address}")
    print(f"Local Key: {local_key}")
    print(f"Device Type: {device_type}")
if(len(sys.argv) == 4):
    device_id = sys.argv[1]
    target_state = (sys.argv[2] == "1")
    brightness = int(float(sys.argv[3]) * 1000)
    print(f"Device ID: {device_id}")
    print(f"Target State: {target_state}")
    print(f"Brightness: {brightness}")
    # Get Tuya
    result = urllib.request.urlopen(f'http://localhost/plugins/NullLights/api/tuya?id={device_id}')
    tuyas = json.loads(result.read())
    ip_address = tuyas['tuya']['url']
    local_key = tuyas['tuya']['local_key']
    device_type = tuyas['tuya']['product_type']
    print(f"IP Address: {ip_address}")
    print(f"Local Key: {local_key}")
    print(f"Device Type: {device_type}")

if device_type == "bulb":
    #bulb
    d = tinytuya.BulbDevice(device_id,ip_address,local_key)
else:
    d = tinytuya.OutletDevice(device_id,ip_address,local_key)
d.set_version(3.3)  # IMPORTANT to set this regardless of version
if target_state:
    data = d.turn_on()
else:
    data = d.turn_off()
data = d.status()
# Show status of first controlled switch on device
#print('Dictionary %r' % data)
state = -1
if 'dps' in data:
    state = 0
    if device_type == "bulb" and data['dps']['20']:
        state = 1
    if device_type == "outlet" and data['dps']['1']:
        state = 1
    print(f"State: {state}")
else:
    print('Error getting status')
if state != -1:
    result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/?id={device_id}&state={state}&error=0&target_state=-1")
else:
    print("Need Cloud Fallback")
    # Get Tuya API Settings
    result = urllib.request.urlopen('http://localhost/plugins/NullLights/api/tuya/settings')
    settings = json.loads(result.read())
    # Connect to Tuya Cloud
    c = tinytuya.Cloud(
        apiRegion   =   settings['tuya_api_region'],
        apiKey      =   settings['tuya_api_key'],
        apiSecret   =   settings['tuya_api_secret'],
        apiDeviceID =   settings['tuya_api_device_id'])
    # Send Command - Turn on switch
    switch_code = "switch_led"
    if device_type == "outlet":
        switch_code = "switch_1"
    commands = {
        "commands": [
            {"code": switch_code, "value": target_state},
            {"code": "countdown_1", "value": 0},
        ]
    }
    print("Sending command...")
    result = c.sendcommand(device_id,commands)
    print("Results\n:", result)
    result = c.getstatus(device_id)
    print("Status of device:")
    get_args = f"?id={device_id}"
    error = 1
    for status in result['result']:
        if 'switch' in status['code']:
            device_state = 0
            if status['value']:
                device_state = 1 
            get_args += f"&state={device_state}"
            error = 0
        elif 'bright' in status['code']:
            device_brightness = status['value']
            get_args += f"&percent={device_brightness/1000.0}"
    result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/{get_args}&error={error}&target_state=-1")
    print(f"GET ARGs: {get_args}")
    print(f"Error: {error}")
    #result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/?id={tuya['id']}&error=1")

