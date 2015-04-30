<?php

include_once '../util/log.php';

class GrapesDB {

    private $host;

    private $name;

    private $pass;

    private $db;

    private static $con = null;

    public function __construct($host, $name, $pass, $db){
        $this->host = $host;
        $this->name = $name;
        $this->pass = $pass;
        $this->db = $db;

        $this->connect();
    }

    public function connect(){
        if(self::$con == null){
            $con = mysql_connect($this->host, $this->name, $this->pass);
            if (!$con) {
                die('Could not connect: ' . mysql_error());
            }
            mysql_select_db($this->db, $con);
            mysql_query("SET NAMES UTF8");
            self::$con = $con;
        }
        return self::$con;
    }

    public function disConnect(){
        if(self::$con){
            mysql_close(self::$con);
            self::$con = null;
        }
    }

    public function getUsers($count){
        $result = mysql_query("SELECT * FROM `grapes_users` WHERE 1 LIMIT 0 , ".$count);
        $users = array();
        while($row = mysql_fetch_array($result)){
            array_push($users, array(
                'id'=>$row['user_id'], 
                'nickname'=>$row['user_nickname'], 
                'email'=>$row['user_mail'], 
                'phone'=>$row['user_phone']
            ));
        }
        return $users;
    }

    public function getUser($userId){
        $result = mysql_query("SELECT * FROM `grapes_users` WHERE user_id = '".$userId."'");

        if($row = mysql_fetch_array($result)){
            return array(
                'id'=>$row['user_id'], 
                'nickname'=>$row['user_nickname'], 
                'email'=>$row['user_mail'], 
                'phone'=>$row['user_phone']
            );
        }
    }

    public function getActivityMembers($activityId){
        $result = mysql_query("SELECT * FROM `grapes_user_activities` WHERE activity = ".$activityId);
        $members = array();
        while($row = mysql_fetch_array($result)){
            $user = $this->getUser($row['user']);
            if($user){
                array_push($members, $user);
            }
        }
        return $members;
    }

    public function getActivityDetail($activityId){
        $result = mysql_query("SELECT * FROM `grapes_activities` WHERE activity_id = ".$activityId);

            Logger::log(0);
        if($row = mysql_fetch_array($result)){
            Logger::log(1);
            $activity = array(
                'id'=>$row['activity_id'],
                'title'=>$row['activity_title'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'planner'=>$row['activity_planner'], 
                'status'=>$row['activity_status']
            );

            Logger::log($activity['activity_planner']);
            $activity['items'] = $this->getActivityItems($activityId);
            Logger::log($activity['items']);

            return $activity;
        }
    }

    public function getUserActivities($userId){
        $result = mysql_query("SELECT * FROM `grapes_user_activities` WHERE user = '".$userId."'");
        $activities = array();

        while($row = mysql_fetch_array($result)){
            $activityId = $row['activity'];
            $activity = $this->getActivity($activityId);
            if($activity){
                array_push($activities, $activity);
            }
        }

        return $activities;
    }

    public function getActivity($activityId){
        $result = mysql_query("SELECT * FROM `grapes_activities` WHERE activity_id = ".$activityId);

        if($row = mysql_fetch_array($result)){
            return array(
                'id'=>$row['activity_id'],
                'title'=>$row['activity_title'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'planner'=>$row['activity_planner'], 
                'status'=>$row['activity_status']
            );
        }
    }

    public function getActivities($count){
        $result = mysql_query("SELECT * FROM `grapes_activities` WHERE 1 LIMIT 0 , ".$count);
        $activities = array();

        while($row = mysql_fetch_array($result)){
            array_push($activities, array(
                'id'=>$row['activity_id'],
                'title'=>$row['activity_title'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'planner'=>$row['activity_planner'], 
                'status'=>$row['activity_status']
            ));
        }

        return $activities;
    }

    public function getActivityItems($activity){
        $result = mysql_query("SELECT * FROM `grapes_activity_items` WHERE activity=".$activity);

        $items = array();
        while($row = mysql_fetch_array($result)){
            array_push($items, array(
                'id'=>$row['item_id'],
                'title'=>$row['item_title'],
                'type'=>$row['item_type'],
                'place'=>$row['item_place'],
                'lasting'=>$row['item_lasting'],
                'content'=>$row['item_content'],
                'images'=>$row['item_image_urls'],
                'status'=>$row['item_status']
            ));
        }

        return $items;
    }
}

?>