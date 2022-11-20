<?php
function CreateLightsTestData(){
    // create fake rooms
    Faker::FakeData("Rooms",['id'=>101,'name'=>'room 1']);
    Faker::FakeData("Rooms",['id'=>102,'name'=>'room 2']);
    Faker::FakeData("RoomNeighbors",['room_id'=>102,'neighbor_id'=>101]);

    Faker::FakeData("Rooms",['id'=>103,'name'=>'room 3']);
    Faker::FakeData("Rooms",['id'=>104,'name'=>'room 4']);
    Faker::FakeData("RoomNeighbors",['room_id'=>104,'neighbor_id'=>103]);

    $room1 = Rooms::RoomId(101);
    $room2 = Rooms::RoomId(102);
    $neighbors = RoomNeighbors::Neighbors(102);
    $room3 = Rooms::RoomId(103);
    $room4 = Rooms::RoomId(104);
    $neighbors2 = RoomNeighbors::Neighbors(104);
    //Debug::Log("room 1",$room1);
    //Debug::Log("room 2",$room2);
    //Debug::Log("room 2 neighbors",$neighbors);
    //Debug::Log("room 3",$room3);
    //Debug::Log("room 4",$room4);
    //Debug::Log("room 4 neighbors",$neighbors2);
    if(!is_null($room1) && !is_null($room2) && count($neighbors) == 1 && !is_null($room3) && !is_null($room4) && count($neighbors2) == 1){
        // create fake lights
        Faker::FakeData("WeMoLights",[
            'mac_address'=>'room1:lamp',
            'name'=>'light 1',
            'url'=>'localhost',
            'port'=>'5000',
            'room_id'=>101,
            'type'=>'light',
            'subtype'=>'lamp',
            'state'=>1
        ]);
        Faker::FakeData("WeMoLights",[
            'mac_address'=>'room1:mood',
            'name'=>'light 2',
            'url'=>'localhost',
            'port'=>'5000',
            'room_id'=>101,
            'type'=>'light',
            'subtype'=>'mood',
            'state'=>0
        ]);
        Faker::FakeData("WeMoLights",[
            'mac_address'=>'room2:ambient',
            'name'=>'light 3',
            'url'=>'localhost',
            'port'=>'5000',
            'room_id'=>102,
            'type'=>'light',
            'subtype'=>'ambient',
            'state'=>0
        ]);
        Faker::FakeData("WeMoLights",[
            'mac_address'=>'room3:lamp',
            'name'=>'light 4',
            'url'=>'localhost',
            'port'=>'5000',
            'room_id'=>103,
            'type'=>'light',
            'subtype'=>'lamp',
            'state'=>1
        ]);
        Faker::FakeData("WeMoLights",[
            'mac_address'=>'room4:lamp',
            'name'=>'light 5',
            'url'=>'localhost',
            'port'=>'5000',
            'room_id'=>104,
            'type'=>'light',
            'subtype'=>'lamp',
            'state'=>1
        ]);
        Faker::FakeData("WeMoLights",[
            'mac_address'=>'room4:mood',
            'name'=>'light 6',
            'url'=>'localhost',
            'port'=>'5000',
            'room_id'=>104,
            'type'=>'light',
            'subtype'=>'mood',
            'state'=>0
        ]);
        $light1 = WeMoLights::MacAddress('room1:lamp');
        $light2 = WeMoLights::MacAddress('room1:mood');
        $light3 = WeMoLights::MacAddress('room2:ambient');
        $light4 = WeMoLights::MacAddress('room3:lamp');
        $light5 = WeMoLights::MacAddress('room4:lamp');
        $light6 = WeMoLights::MacAddress('room4:mood');
        //Debug::Log("Light 1",$light1);
        //Debug::Log("Light 2",$light2);
        //Debug::Log("Light 3",$light3);
        //Debug::Log("Light 4",$light4);
        //Debug::Log("Light 5",$light5);
        //Debug::Log("Light 6",$light6);
        if(!is_null($light1) && !is_null($light2) && !is_null($light3) && !is_null($light4) && !is_null($light5) && !is_null($light6)){
            // create fake logs
            /**
             * light 1 on 5 minutes after being off 2 minute
             */
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s"),date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),[
                'mac_address'=>'room1:lamp',
                'state'=>1
            ],true);
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),date("Y-m-d H:i:s",time()-MinutesToSeconds(7)),[
                'mac_address'=>'room1:lamp',
                'state'=>0
            ],true);
            
            /**
             * light 2 off 5 minutes after being on 2 minute
             */
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s"),date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),[
                'mac_address'=>'room1:mood',
                'state'=>0
            ],true);
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),date("Y-m-d H:i:s",time()-MinutesToSeconds(7)),[
                'mac_address'=>'room1:mood',
                'state'=>1
            ],true);

            /**
             * light 3 off 7 minutes
             */
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s"),date("Y-m-d H:i:s",time()-MinutesToSeconds(7)),[
                'mac_address'=>'room2:ambient',
                'state'=>0
            ],true);

            /**
             * light 4 off 5 minutes after being on 2 minute
             */
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s"),date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),[
                'mac_address'=>'room3:lamp',
                'state'=>0
            ],true);
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),date("Y-m-d H:i:s",time()-MinutesToSeconds(7)),[
                'mac_address'=>'room3:lamp',
                'state'=>1
            ],true);

            /**
             * light 5 off 5 minutes after being on 2 minute
             */
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s"),date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),[
                'mac_address'=>'room4:lamp',
                'state'=>0
            ],true);
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),date("Y-m-d H:i:s",time()-MinutesToSeconds(7)),[
                'mac_address'=>'room4:lamp',
                'state'=>1
            ],true);

            /**
             * light 6 off 5 minutes after being on 2 minute
             */
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s"),date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),[
                'mac_address'=>'room4:mood',
                'state'=>1
            ],true);
            Faker::FakeLogData("WeMoLogs","created",60,date("Y-m-d H:i:s",time()-MinutesToSeconds(5)),date("Y-m-d H:i:s",time()-MinutesToSeconds(7)),[
                'mac_address'=>'room4:mood',
                'state'=>0
            ],true);

            // check our work
            $logs1 = WeMoLogs::MacAddress('room1:lamp');
            $logs2 = WeMoLogs::MacAddress('room1:mood');
            $logs3 = WeMoLogs::MacAddress('room2:ambient');
            $logs4 = WeMoLogs::MacAddress('room3:lamp');
            $logs5 = WeMoLogs::MacAddress('room4:lamp');
            $logs6 = WeMoLogs::MacAddress('room4:mood');
            //Debug::Log("FakeLogs1",$logs1);
            //Debug::Log("FakeLogs2",$logs2);
            //Debug::Log("FakeLogs3",$logs3);
            //Debug::Log("FakeLogs4",$logs4);
            //Debug::Log("FakeLogs5",$logs5);
            //Debug::Log("FakeLogs6",$logs6);
            if(count($logs1) == 7 && count($logs2) == 7 && count($logs3) == 7 && count($logs4) == 7 && count($logs5) == 7 && count($logs6) == 7)
                define("lights_fake_data",true);
        }
    }
    if(!defined("lights_fake_data")) define("lights_fake_data",false);
}
?>