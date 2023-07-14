import tinytuya
from six.moves import urllib
import json

# Get Tuya API Settings
result = urllib.request.urlopen('http://localhost/plugins/NullLights/api/tuya/settings')
settings = json.loads(result.read())
# Connect to Tuya Cloud
c = tinytuya.Cloud(
    apiRegion   =   settings['tuya_api_region'],
    apiKey      =   settings['tuya_api_key'],
    apiSecret   =   settings['tuya_api_secret'],
    apiDeviceID =   settings['tuya_api_device_id'])

# Display list of devices
devices = c.getdevices()
# Iterate through the devices
for device in devices:
    # Retrieve the device ID, IP address, and local key
    device_id = device['id']
    device_name = device['name']
    local_key = device['key']
    product_name = device['product_name']
    get_args = f"?id={device_id}&name={urllib.parse.quote(device_name)}&local_key={urllib.parse.quote(local_key)}&product_name={urllib.parse.quote(product_name)}"
    
    device_state = -1
    device_brightness = -1
    device_color = -1
    # Print the device information
    print("----")
    print(f"Device Name: {device_name}")
    print(f"Device ID: {device_id}")
    #print(f"IP Address: {ip_address}")
    print(f"Local Key: {local_key}")
    print(f"Product Name: {product_name}")
    print("----")
    
    # Display Properties of Device
    #result = c.getproperties(device_id)
    #print("Properties of device:\n", result['functions'])

    # Display Status of Device
    result = c.getstatus(device_id)
    print("Status of device:")
    for status in result['result']:
        if 'switch' in status['code']:
            if status['value']:
                device_state = 1 
            else:
                device_state = 0
            get_args += f"&state={device_state}"
        elif 'bright' in status['code']:
            device_brightness = status['value']
            get_args += f"&percent={device_brightness/1000.0}"
    print(f"Brightness: {device_brightness}")
    print(f"State: {device_state}")
    print(f"Get: {get_args}")
    save_res = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save{get_args}")
    #save_json = json.loads(save_res.read())
    #print(save_json)
    print("----")

devices = tinytuya.deviceScan()
for device in devices:
    device_id = devices[device]['id']
    device_url = devices[device]['ip']
    print(f"Device ID: {device_id}")
    print(f"IP Address: {device_url}")
    save_res = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save?id={device_id}&url={device_url}")
    save_json = json.loads(save_res.read())
    #print(save_json)
