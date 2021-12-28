#import MySQLdb
import time
import sys
from lighting import wemo_device
from six.moves import urllib
import json

#db = MySQLdb.connect(host="localhost", user="php", passwd="pinkstarsarefalling", db="grow_log")
#cur = db.cursor()

#cur.execute("SELECT `name`, `mac_address`, `url` FROM `servers` WHERE `type` = 'WeMo'")
#results = cur.fetchall()
#for row in results:
#    name = row[0]
#    mac = row[1]
#    ip = row[2]
#    wemo_device.wemos.append(wemo_device(mac,db,cur))


if(len(sys.argv) > 1 and "obs" in sys.argv[1]):
    # observe light states
    result = urllib.request.urlopen('http://localhost/plugins/NullLights/api/light')
    data = json.loads(result.read())
    for light in data['lights']:
        print (light['name'])
        wemo = wemo_device(light)
        wemo.observe()
        print (wemo.name)
        print (wemo.mac_address)
        print (wemo.state)
        print (wemo.target_state)
        print ('---------------------------------')
    #for wemo in wemo_device.wemos:
    #    wemo.observe()
elif(len(sys.argv)):
    mac_address = sys.argv[1]
    #resp = http.request('GET', 'http://localhost/plugins/NullLights/api/light',fields={'mac_address': mac_address})
    result = urllib.request.urlopen('http://localhost/plugins/NullLights/api/light?mac_address='+mac_address)
    data = json.loads(result.read())
    wemo = wemo_device(data['light'])
    wemo.apply()
else:
    print ("missing paramaters")
    # apply light states
    #for wemo in wemo_device.wemos:
    #    wemo.apply()
    result = urllib.request.urlopen('http://localhost/plugins/NullLights/api/light')
    data = json.loads(result.read())

    for light in data['lights']:
        print (light['name'])
        wemo = wemo_device(light)
        wemo.apply()

