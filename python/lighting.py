import time
import signal
import peewee
#import MySQLdb
import datetime
from peewee import *
from miranda import upnp
import xml.etree.ElementTree as ET
from contextlib import contextmanager
from six.moves import urllib
import json

class TimeoutException(Exception): pass

@contextmanager
def time_limit(seconds):
    def signal_handler(signum, frame):
        raise (TimeoutException, "Timed out!")
    signal.signal(signal.SIGALRM, signal_handler)
    signal.alarm(seconds)
    try:
        yield
    finally:
        signal.alarm(0)



##################
# Objects/Classes
# based on class from: https://github.com/ericpulvino/wemocontrol/blob/master/wemo_backend.py
# i haven't really finished clearing out the stuff i don't need
##################
class wemo_device():
    wemos = []
    def __init__(self,data):
        
        self.parseLoadData(data)
        self.timeout_val = 8
    # parse the json data
    def parseLoadData(self,data):
        #server info
        self.mac_address = data["mac_address"]
        self.name = data['name']
        self.ip_address = data['url']
        self.port = data['port']
        #light info
        #self.cur.execute("SELECT `room_id`, `state`, `target_state`, `last_on`, `last_off`, `modified` FROM `WeMoLights` WHERE `mac_address` = '{}' LIMIT 1".format(self.mac_address))        #    self.room_id = row[0]
        self.state = data['state']
        self.target_state = data['target_state']
        #self.last_on = data['last_on']
        #self.last_off = data['last_off']
        self.modified = data['modified']
    #
    # load data from the databases
    #
    def findPort(self):
        return False
        resp = urllib.request.urlopen('http://localhost/plugins/NullLights/api/light/python?mac_address='+self.mac_address+'&find_port='+1)
        data = json.loads(resp.read())
        if('light' in data):
            if('error' in data):
                return False
            self.parseLoadData(data['light'])
            return True
        return False
    #
    # load data from the databases
    #
    def load(self):
        resp = urllib.request.urlopen('http://localhost/plugins/NullLights/api/light/python?mac_address='+self.mac_address)
        data = json.loads(resp.read())
        self.parseLoadData(data)
        # grab light info

        # grab server info
        #self.cur.execute("SELECT `name`, `url`, `port` FROM `servers` WHERE `mac_address` = '{}' LIMIT 1".format(self.mac_address))
        #results = self.cur.fetchall()
        #for row in results:
        #    self.name = row[0]
        #    self.ip_address = row[1]
        #    self.port = "{}".format(row[2])
        
        # grab light info
        #self.cur.execute("SELECT `room_id`, `state`, `target_state`, `last_on`, `last_off`, `modified` FROM `WeMoLights` WHERE `mac_address` = '{}' LIMIT 1".format(self.mac_address))
        #results = self.cur.fetchall()
        #for row in results:
        #    self.room_id = row[0]
        #    self.state = row[1]
        #    self.target_state = row[2]
        #    self.last_on = row[3]
        #    self.last_off = row[4]
        #    modified = row[5]
        # if 'modified' not in locals():
            #self.state = self.actual_state()
            #print("need to add a new WeMoLights: {}".format(self.state))
            #self.cur.execute("INSERT INTO `WeMoLights` (`mac_address`, `state`) VALUES ('{}','{}')".format(self.mac_address,self.state))
            #self.db.commit()
            #self.cur.execute("SELECT `room_id`, `state`, `target_state`, `last_on`, `last_off`, `modified` FROM `WeMoLights` WHERE `mac_address` = '{}' LIMIT 1".format(self.mac_address))
            #results = self.cur.fetchall()
            #for row in results:
            #    self.room_id = row[0]
            #    self.state = row[1]
            #    self.target_state = row[2]
            #    self.last_on = row[3]
            #    self.last_off = row[4]
            #    modified = row[5]
        #return self.state, modified
    #
    # save data to the database/local api
    #
    def save(self):
        if(self.state == -1):
            self.state = self.actual_state()
        #if(self.state == 2):
            #self.cur.execute("UPDATE `WeMoLights` SET `error` = 1 WHERE `mac_address` = '{}'".format(self.mac_address))
            #r = self.http.request('GET','http://localhost/api/light/python',fields={'mac_address': self.mac_address,'state':'2','target_state':self.target_state})
        #else:
            #self.cur.execute("UPDATE `WeMoLights` SET `error` = 0, `state` = {}, `target_state` = {} WHERE `mac_address` = '{}'".format(self.state,self.target_state,self.mac_address))
        resp = urllib.request.urlopen('http://localhost/plugins/NullLights/api/light/python?mac_address='+self.mac_address+'&state='+str(self.state)+'&target_state='+str(self.target_state))
        data = json.loads(resp.read())
        print(data['sql'])
        print(data['error'])
        print(data['row']['state'])
        #self.db.commit()
    #
    # apply the expected state to the actual state of the wemo outlet
    #
    def apply(self):
        current_state = self.actual_state()
        print("Apply {} ({} == {})".format(self.name,self.state,current_state))
        
        #self.cur.execute("SELECT `room_id`, `state`, `target_state`, `last_on`, `last_off`, `modified` FROM `WeMoLights` WHERE `mac_address` = '{}' LIMIT 1".format(self.mac_address))
        #results = self.cur.fetchall()
        #for row in results:
        #    self.room_id = row[0]
        #    self.state = row[1]
        #    self.target_state = row[2]
        #    self.last_on = row[3]
        #    self.last_off = row[4]
        
        if(current_state != self.state or self.state != self.target_state):
            if(self.target_state > -1):
                self.state = self.target_state
            if(self.state == "1" or self.state == 1):
                self.on()
            elif(self.state == "0" or self.state == 0):
                self.off()
            if(self.state == self.target_state):
                self.target_state = -1
                self.save()
    #
    # observe the actual state of the wemo and save it locally
    #
    def observe(self):
        self.state = self.actual_state()
        # if error state find port and try again...
        if(self.state == "2"):
            if(self.findPort()):
                self.state = self.actual_state()
        # if target state != actual state try to set state to target state
        if(int(self.target_state) > -1):
            print("target state not -1 do something about it")
            if(self.state != self.target_state):
                self.state = self.target_state
                self.apply()
            else:
                print("states already match")
                self.target_state = -1
            #self.save()
        self.save()
    #
    # simply gets the actual state
    #
    def actual_state(self):
        print ("check actual state")
        current_state = "3"
        conn = upnp()
        try:
            with time_limit(self.timeout_val):
                resp = conn.sendSOAP(str(self.ip_address) +':'+self.port, 'urn:Belkin:service:basicevent:1','http://'+ str(self.ip_address) + ':'+self.port+'/upnp/control/basicevent1', 'GetBinaryState', {})
            tree = ET.fromstring(resp)            
            current_state = tree.find('.//BinaryState').text
            print(current_state)
            print("response: {}".format(current_state))
            if str(current_state) != "1" and str(current_state) != "0": current_state = "2"
        except:
            print ("ERROR: Update: Timed out!")
            current_state = "2"
        print("returning: {}".format(current_state))
        return current_state
    #
    # turn on the wemo
    #
    def on(self,):
        #print("-Turn on {}".format(self.name))
        #collects current state
        current_state = self.actual_state()
        #if needed, change state to on
        if current_state == "0" or current_state == "2":
            conn = upnp()
            try:
                with time_limit(self.timeout_val):
                    resp = conn.sendSOAP(str(self.ip_address) +':'+self.port, 'urn:Belkin:service:basicevent:1', 'http://' + str(self.ip_address) + ':'+self.port+'/upnp/control/basicevent1', 'SetBinaryState', {'BinaryState': (1, 'Boolean')})
                #new state is returned in the response...checks current state again to confirm success
                tree = ET.fromstring(resp)    
                new_state = tree.find('.//BinaryState').text
                if new_state != "1" and new_state != "0": new_state = "2"
            except:
                print ("ERROR: ON Operation: Timed out!")   
                new_state = "2"
            
            #self.cur.execute("UPDATE `WeMoLights` SET `last_on` = CURRENT_TIMESTAMP()")
            #self.db.commit()
            #self.save()
            
            #confirm state change
            if new_state == "1": return "1"
            else: return "0"
        #returns success or failure
        elif current_state=="1": return "1"
        else: return "0"


    def off(self,):
        print("-Turn off {}".format(self.name))
        #collects current state
        current_state = self.actual_state()
        #if needed, change state to off
        if current_state == "1" or current_state == "2":
            conn = upnp()
            try:
                with time_limit(self.timeout_val):
                    resp = conn.sendSOAP(str(self.ip_address) +':'+self.port, 'urn:Belkin:service:basicevent:1', 'http://' + str(self.ip_address) + ':'+self.port+'/upnp/control/basicevent1', 'SetBinaryState', {'BinaryState': (0, 'Boolean')})
                #new state is returned in the response...checks current state again to confirm success
                tree = ET.fromstring(resp)    
                new_state = tree.find('.//BinaryState').text
                if new_state != "1" and new_state != "0": new_state = "2"
            except:
                print ("ERROR: OFF Operation: Timed out!")
                new_state = "2"
            #self.cur.execute("UPDATE `WeMoLights` SET `last_off` = CURRENT_TIMESTAMP()")
            #self.db.commit()
            #self.save()

            #confirm state change
            if new_state == "0": return "1"
            else: return "0"
        #returns success or failure
        elif current_state=="0": return "1"
        else: return "0"
