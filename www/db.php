<?php

require_once("php/db/GrapesDB.php");
require_once("php/util/log.php");

define("HOST", "localhost");
define("USER_NAME", "happymuslim_f");
define("USER_PASS", "happymuslim");
define("DB_NAME", "happymuslim");

$result = array();
$result["_id"] = $_REQUEST['_id'];

$grapesDB = new GrapesDB(HOST, USER_NAME, USER_PASS, DB_NAME);

$api = $_REQUEST['api'];

switch ($api){
    case "check_user":
        Logger::log('check_user');
        $code = $grapesDB->checkUser($_REQUEST['user'], $_REQUEST['password']);
        $data = array("code"=>$code);
        break;
    case "add_user":
        Logger::log('add_user');
        $success = $grapesDB->addUser(
            $_REQUEST['user'],
            $_REQUEST['password'],
            $_REQUEST['nickname'],
            $_REQUEST['sex'],
            $_REQUEST['phone'],
            $_REQUEST['mail'],
            $_REQUEST['address'],
            $_REQUEST['headicon']
        );
        $data = array("success"=>$success);
        break;
    case "user":
        Logger::log('user');
        $data = $grapesDB->getUser($_REQUEST['user']);
        break;
    case "users":
        Logger::log('users');
        $data = $grapesDB->getUsers($_REQUEST['count']);
        break;
    case "act":
        Logger::log('act');
        $data = $grapesDB->getActivity($_REQUEST['act']);
        break;
    case "act_detail":
        Logger::log('act_detail');
        $data = $grapesDB->getActivityDetail($_REQUEST['act']);
        break;
    case "acts":
        Logger::log('acts');
        $data = $grapesDB->getAllActivities($_REQUEST['count']);
        break;
    case "public_acts":
        Logger::log('public_acts');
        $data = $grapesDB->getPublicActivities($_REQUEST['count']);
        break;
    case "private_acts":
        Logger::log('private_acts');
        $data = $grapesDB->getPrivateActivities($_REQUEST['user'], $_REQUEST['count']);
        break;
    case "user_acts":
        Logger::log('user_acts');
        $data = $grapesDB->getUserActivities($_REQUEST['user']);
        break;
    case "act_members":
        Logger::log('act_members');
        $data = $grapesDB->getActivityMembers($_REQUEST['act']);
        break;
    case "join_act":
        Logger::log('join_act');
        $success = $grapesDB->joinActivity($_REQUEST['act'], $_REQUEST['user']);
        $data = array("success"=>$success);
        break;
    case "add_act":
        Logger::log('add_act');
        $success = $grapesDB->addActivity(
            $_REQUEST['title'],
            $_REQUEST['category'],
            $_REQUEST['planner'],
            $_REQUEST['time'],
            $_REQUEST['address'],
            $_REQUEST['fee'],
            $_REQUEST['content'],
            $_REQUEST['comment'],
            $_REQUEST['image_urls'],
            $_REQUEST['scope']
        );
        $data = array("act_id"=>$success);
        break;
    case "add_act_item":
        Logger::log('add_act_item');
        $success = $grapesDB->addActivityItem(
            $_REQUEST['act'],
            $_REQUEST['title'],
            $_REQUEST['category'],
            $_REQUEST['place'],
            $_REQUEST['lasting'],
            $_REQUEST['content'],
            $_REQUEST['image_urls']
        );
        $data = array("success"=>$success);
        break;
    case "add_act_items":
        Logger::log('add_act_items');
        $success = $grapesDB->addActivityItems(
            $_REQUEST['act'],
            json_decode($_REQUEST['items'])
        );
        $data = array("success"=>$success);
        break;
    default:
        $data = array();
}

$result["data"] = $data;

$res_type = $_REQUEST['res'];

if($res_type == 'jsonp'){
    echo $_REQUEST['_callback']."(".json_encode($result).")";
} else {
    echo json_encode($result);
}

?>