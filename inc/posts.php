<?php

session_start();
if (isset($_SESSION['username'])) {
  $action = $_GET["action"];
  actionSelector($action);

  } else {
    echo "Invalid session data";
  } 

function actionSelector($action){

  if ($action=="recent") {
    getRecent($_SESSION['userId']);
  }
  else if ($action=="library") {
    getLibrary($_SESSION['userId']);
  }
  else if ($action=="getAllGroupData"){
    getGroupDataForUser($_SESSION['userId']);
  } 
  else if ($action=="getGroupList"){
    $json = json_encode(getGroupListForUser($_SESSION['userId']));
    echo $json;
  } 
  else if ($action=="getGroupData"){
    $json = json_encode(getGroupData($_GET['groupId']));
    echo $json;
  }
  else if($action="newPost"){
      addNewPost($_POST['group'],$_SESSION['userId'],$_POST['url'],$_POST['message']);
      
  }
  else {
    $json = json_encode("Action Code Not Recognized");
    echo $json;
  }

}


function addNewPost($groups, $userId, $url, $comment){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");

  if ($comment=="++++++") {
    $comment ='';
  }

  foreach ($groups as $group) {
    $groupId = $group;
    if ($groupId=="library") {
      $groupId="0";
    }

    try {
    $results = $db->prepare("INSERT INTO `posts` (`posterName`, `posterId`, `groupId`, `url`, `comment`)
                              VALUES (?,?,?,?,?)
                              ");
    $results->execute(array($_SESSION['username'], $userId, $groupId, $url, $comment));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;
    }
  
  } //end each*/

  $json=json_encode("success");
  echo $json;

}


//Recent Functions
function getRecent($userId){
  

  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");
  
  //get the group list
  $groups = getGroupListForUser($userId);

  //create an array of groupIds
  $groupIdList = array();
  foreach ($groups as $value) {
    array_push($groupIdList, $value["groupId"]);
  }

  //create a SQL query with WHERE groupId=? or groupId=?
  $SQLQuery = "SELECT posterName, groupId, url, postDate FROM posts WHERE ";

  //a For loop that concatenates groupId=? onto the SQL query, with ORs included except for the last iteration of the loop

  for ($i=0; $i < count($groupIdList); $i++) { 
    $SQLQuery .= "groupId=? ";
    $remainingLoops = (count($groupIdList)-$i)-1;
    if ($remainingLoops!==0) {
      $SQLQuery .= "OR ";
    }
  }

   
  $SQLQuery .= "ORDER BY postId DESC LIMIT 15";

  //prepare $db call
  //execute with array of groupIds
  try {
    $results = $db->prepare($SQLQuery);
    $results->execute($groupIdList);

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $recent = $results->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($recent);
    echo $json;

 }
  
//Library Functions  
function getLibrary($userId){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");

  try {
    $results = $db->prepare("SELECT groupId, url, postDate
                              FROM posts
                              WHERE posterId=?
                              ORDER BY postId DESC
                              ");
    $results->execute(array($userId));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $library = $results->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($library);
    echo $json;

 }

//Group Data Functions
function getGroupDataForUser($userId){

  
    $groups = getGroupListForUser($userId);

    $groupDataArray = array();

    foreach ($groups as $value) {
    $newGroupData = array($value,getGroupData($value["groupId"]));
    array_push($groupDataArray, $newGroupData); 
    }
    
    $json = json_encode($groupDataArray);
    echo $json;

}


function getGroupListForUser($userId){
  
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");
  
  try {
    $results = $db->prepare("SELECT groupId, groupName
                              FROM userGroupRelations
                              WHERE userId=? 
                              ");
    $results->execute(array($userId));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $groupList = $results->fetchAll(PDO::FETCH_ASSOC);
    return $groupList;
}

function getGroupData($groupId){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");
  
  try {
    $results = $db->prepare("SELECT posterName, url, postDate
                              FROM posts
                              WHERE groupId=? 
                              ORDER BY postId DESC
                              ");
    $results->execute(array($groupId));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $groupPosts = $results->fetchAll(PDO::FETCH_ASSOC);

    return $groupPosts;

}

?>



