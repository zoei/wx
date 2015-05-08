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

    public function checkUser($userId, $pass){
        $result = mysql_query("SELECT * FROM `grapes_users` WHERE user_id = '".$userId."'");

        if($row = mysql_fetch_array($result)){
            if($row['user_pass'] == $pass){
                return 'S00';
            } else {
                return 'E02';
            }
        }
        return 'E01';
    }

    public function addUser($userId, $pass, $nickname, $sex, $phone, $mail, $address, $headicon){
        $sql = "INSERT INTO grapes_users (".
            " user_id,".
            " user_pass,".
            " user_nickname,".
            " user_sex,".
            " user_phone,".
            " user_mail,".
            " user_address,".
            " user_headicon,".
            " user_status,".
            " user_registered".
            " ) VALUES (".
            "'".$userId."',".
            "'".$pass."',".
            "'".$nickname."',".
            "'".$sex."',".
            "'".$phone."',".
            "'".$mail."',".
            "'".$address."',".
            "'".$headicon."',".
            "1,".
            "now()".
            ")";
        Logger::log($sql);
        $result = mysql_query($sql);

        if(!$result){
            return false;
        } else {
            return true;
        }
    }

    public function getUsers($count){
        $result = mysql_query("SELECT * FROM `grapes_users` WHERE 1 LIMIT 0 , ".$count);
        $users = array();
        while($row = mysql_fetch_array($result)){
            array_push($users, array(
                'id'=>$row['user_id'], 
                'nickname'=>$row['user_nickname'],
                'sex'=>$row['user_sex'],
                'email'=>$row['user_mail'],
                'phone'=>$row['user_phone'],
                'address'=>$row['user_address'],
                'headicon'=>$row['user_headicon'],
                'status'=>$row['user_status'],
                'registered'=>$row['user_registered']
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
                'sex'=>$row['user_sex'],
                'email'=>$row['user_mail'],
                'phone'=>$row['user_phone'],
                'address'=>$row['user_address'],
                'headicon'=>$row['user_headicon'],
                'status'=>$row['user_status'],
                'registered'=>$row['user_registered']
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
                'category'=>$row['activity_category'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'fee'=>$row['activity_fee'],
                'planner'=>$row['activity_planner'], 
                'status'=>$row['activity_status']
            );

            Logger::log($activity['activity_planner']);
            $activity['items'] = $this->getActivityItems($activityId);
            Logger::log($activity['items']);

            return $activity;
        }
    }

    public function getActivity($activityId){
        $result = mysql_query("SELECT * FROM `grapes_activities` WHERE activity_id = ".$activityId);

        if($row = mysql_fetch_array($result)){
            return array(
                'id'=>$row['activity_id'],
                'title'=>$row['activity_title'],
                'category'=>$row['activity_category'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'fee'=>$row['activity_fee'],
                'planner'=>$row['activity_planner'], 
                'status'=>$row['activity_status']
            );
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

    public function getPrivateActivities($userId, $count){
        $result = mysql_query("SELECT * FROM `grapes_activities` WHERE activity_scope = 1 AND planner = '".$userId."'");
        $activities = array();

        while($row = mysql_fetch_array($result)){
            array_push($activities, array(
                'id'=>$row['activity_id'],
                'title'=>$row['activity_title'],
                'category'=>$row['activity_category'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'fee'=>$row['activity_fee'],
                'planner'=>$row['activity_planner'], 
                'status'=>$row['activity_status']
            ));
        }

        return $activities;
    }

    public function getPublicActivities($count){
        $result = mysql_query("SELECT * FROM `grapes_activities` WHERE activity_scope = 0 LIMIT 0 , ".$count);
        $activities = array();

        while($row = mysql_fetch_array($result)){
            array_push($activities, array(
                'id'=>$row['activity_id'],
                'title'=>$row['activity_title'],
                'category'=>$row['activity_category'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'fee'=>$row['activity_fee'],
                'planner'=>$row['activity_planner'], 
                'status'=>$row['activity_status']
            ));
        }

        return $activities;
    }

    public function getAllActivities($count){
        $result = mysql_query("SELECT * FROM `grapes_activities` WHERE 1 LIMIT 0 , ".$count);
        $activities = array();

        while($row = mysql_fetch_array($result)){
            array_push($activities, array(
                'id'=>$row['activity_id'],
                'title'=>$row['activity_title'],
                'category'=>$row['activity_category'],
                'address'=>$row['activity_address'], 
                'time'=>$row['activity_time'],
                'fee'=>$row['activity_fee'],
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
                'category'=>$row['item_category'],
                'place'=>$row['item_place'],
                'lasting'=>$row['item_lasting'],
                'content'=>$row['item_content'],
                'images'=>$row['item_image_urls'],
                'status'=>$row['item_status']
            ));
        }

        return $items;
    }

    public function joinActivity($activity, $userId, $reference){
        if(!$reference){
            $reference = 1;
        }
        $sql = "INSERT INTO grapes_user_activities (".
            " user,".
            " activity,".
            " reference,".
            " join_time".
            " ) VALUES (".
            "'".$userId."',".
            "".$activity.",".
            "".$reference.",".
            "now()".
            ")";
        Logger::log($sql);
        $result = mysql_query($sql);

        if(!$result){
            return false;
        } else {
            return true;
        }
    }

    public function addActivity($title, $category, $planner, $time, $address, $fee, $content, $comment, $image_urls, $scope){
        // Lock Table
        // $sql = "LOCK TABLE grapes_activities write";
        // if(!mysql_query($sql)){
        //     Logger::log("lock table error: ".$sql);
        //     mysql_query("UNLOCK TABLE");
        //     return false;
        // };

        // Insert
        $sql = "INSERT INTO grapes_activities (".
            " activity_title,".
            " activity_category,".
            " activity_planner,".
            " activity_time,".
            " activity_address,".
            " activity_fee,".
            " activity_content,".
            " activity_image_urls,".
            " activity_comment,".
            " activity_scope,".
            " activity_status,".
            " activity_create".
            " ) VALUES (".
            "'".$title."',".
            $category.",".
            "'".$planner."',".
            "'".$time."',".
            "'".$address."',".
            "'".$fee."',".
            "'".$content."',".
            "'".$comment."',".
            "'".$image_urls."',".
            $scope.",".
            "1,".
            "now()".
            ")";
        if(!mysql_query($sql)){
            Logger::log("insert error: ".$sql);
            // mysql_query("UNLOCK TABLE");
            return false;
        }

        // get last data
        $result = mysql_query("SELECT MAX(activity_id) AS max_id FROM `grapes_activities`");
        if($row = mysql_fetch_array($result)){
            $activity_id = $row['max_id'];
        }

        // mysql_query("UNLOCK TABLE");

        return $activity_id;
    }

    public function addActivityItem($activity, $title, $category, $place, $lasting, $content, $image_urls) {
        $sql = "INSERT INTO grapes_activity_items (".
            " activity,".
            " item_title,".
            " item_category,".
            " item_place,".
            " item_lasting,".
            " item_content,".
            " item_image_urls,".
            " item_status".
            " ) VALUES (".
            $activity.",".
            "'".$title."',".
            $category.",".
            "'".$place."',".
            "'".$lasting."',".
            "'".$content."',".
            "'".$image_urls."',".
            "0".
            ")";
        Logger::log($sql);
        $result = mysql_query($sql);

        if(!$result){
            return false;
        } else {
            return true;
        }
    }

    public function addActivityItems($activity, $items) {
        if(count($items) <= 0){
            return false;
        }
        $sql = "INSERT INTO grapes_activity_items (".
            " activity,".
            " item_title,".
            " item_category,".
            " item_place,".
            " item_lasting,".
            " item_content,".
            " item_image_urls,".
            " item_status".
            " ) VALUES ";

        $i = 0;
        foreach( $items as $item ){
            if($i == 0){
                $sql .= "(";
            } else {
                $sql .= ",(";
            }

            $sql .= 
                $activity.",".
                "'".$item->title."',".
                $item->category.",".
                "'".$item->place."',".
                "'".$item->lasting."',".
                "'".$item->content."',".
                "'".$item->image_urls."',".
                "0";

            $sql .= ")";

            $i++;
        }

        Logger::log($sql);
        $result = mysql_query($sql);

        if(!$result){
            return false;
        } else {
            return true;
        }
    }
}

?>