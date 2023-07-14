import tinytuya
from six.moves import urllib
import json

# Get Tuyas
result = urllib.request.urlopen('http://localhost/plugins/NullLights/api/tuya')
tuyas = json.loads(result.read())

error_tuyas = []

for tuya in tuyas['tuyas']:
    print("---------")
    print(f"Name: {tuya['name']}")
    print(f"ID: {tuya['id']}")
    print(f"IP: {tuya['url']}")
    print(f"Key: {tuya['local_key']}")
    print(f"Key: {tuya['product_type']}")
    if "bulb" in tuya['product_type']:
        d = tinytuya.BulbDevice(tuya['id'],tuya['url'],tuya['local_key'])
        d.set_version(3.3)  # IMPORTANT to set this regardless of version
        data = d.status()
        # Show status of first controlled switch on device
        #print('Dictionary %r' % data)
        state = -1
        if 'dps' in data:
            state = 0
            if data['dps']['20']:
                state = 1
            print(f"State: {state}")
        else:
            print('Error getting status')
        if state != -1:
            result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/?id={tuya['id']}&state={state}&error=0")
        else:
            #result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/?id={tuya['id']}&error=1")
            error_tuyas.append(tuya)
    else:
        d = tinytuya.OutletDevice(tuya['id'],tuya['url'],tuya['local_key'])
        d.set_version(3.3)  # IMPORTANT to set this regardless of version
        data = d.status()
        # Show status of first controlled switch on device
        #print('Dictionary %r' % data)
        state = -1
        if 'dps' in data:
            state = 0
            if data['dps']['1']:
                state = 1
            print(f"State: {state}")
        else:
            print('Error getting status')
        if state != -1:
            result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/?id={tuya['id']}&state={state}&error=0")
        else:
            #result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/?id={tuya['id']}&error=1")
            error_tuyas.append(tuya)

    print("---===---")

if error_tuyas:
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
    for tuya in error_tuyas:
        print("----=----")
        print(f"Name: {tuya['name']}")
        print(f"ID: {tuya['id']}")
        # Display Status of Device
        result = c.getstatus(tuya['id'])
        print("Status of device:")
        get_args = f"?id={tuya['id']}"
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
        result = urllib.request.urlopen(f"http://localhost/plugins/NullLights/api/tuya/save/{get_args}&error={error}")
        print(f"GET ARGs: {get_args}")
        print(f"Error: {error}")
        print("-=-----=-")
