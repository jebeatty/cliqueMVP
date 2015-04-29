<?php

session_start();
if (isset($_SESSION['username'])) {
  if (isset($_POST["action"])) {
    $action = $_POST["action"];
  } else{
    $action = $_GET["action"];
  }

  actionSelector($action);
  } else {
    echo "Invalid session data";
  } 

define("GROUP_POSTS_LIMIT", '3');

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
    //gets data for display
    $json = json_encode(getGroupData($_GET['groupId'],'25'));
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
    $insertId = $db->lastInsertId();
    } catch(Exception $e){
        echo "Data loading error!";
        exit;
    }
    addUserPostRelation($insertId,$userId, 'likes');

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
  $SQLQuery = "SELECT posterName, groupId, url, postDate, postId, comment, ehs, likes, loves FROM posts WHERE ";

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
    //need to overlay userPostRelation & like data
    foreach ($recent as &$recentPost) {
      $postLiked = checkIfUserLikedPost($recentPost['postId'], $userId);
      $recentPost['postLiked']=$postLiked;
    }

    foreach ($recent as &$recentPost) {
        $commentData = getCommentsForPost($recentPost['postId']);
        $recentPost['commentData']=$commentData;
    }

    $json = json_encode($recent);
    echo $json;

 }
  
//Library Functions  
function getLibrary($userId){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");

  try {
    $results = $db->prepare("SELECT groupId, url, postDate, postId, ehs, likes, loves, comment
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

    foreach ($library as &$libraryPost) {
        $commentData = getCommentsForPost($libraryPost['postId']);
        $libraryPost['commentData']=$commentData;
    }

    $json = json_encode($library);
    echo $json;

 }

//Group Data Functions
function getGroupDataForUser($userId){

  
    $groups = getGroupListForUser($userId);

    $groupDataArray = array();

    foreach ($groups as $value) {
    $newGroupData = array($value,getGroupData($value["groupId"],'3'));
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

function getGroupData($groupId, $limit){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");
  
  if (!$limit) {
    $limit='15';
  }

  $query = "SELECT posterName, url, postDate, postId, ehs, likes, loves, comment FROM posts WHERE groupId=".$groupId." ORDER BY postId DESC LIMIT ".$limit;

  try {
    $results = $db->prepare($query);
    $results->execute(array());

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $groupPosts = $results->fetchAll(PDO::FETCH_ASSOC);

    //check for likes
    $userId=$_SESSION['userId'];

    foreach ($groupPosts as &$groupItem) {
      $postLiked = checkIfUserLikedPost($groupItem['postId'], $userId);
      $groupItem['postLiked']=$postLiked;

    }

    //add comments
    foreach ($groupPosts as &$groupItem) {
        $commentData = getCommentsForPost($groupItem['postId']);
        $groupItem['commentData']=$commentData;
    }

    return $groupPosts;

}

function addUserPostRelation($postId, $userId, $likeType){
    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    try{
      $results = $db->prepare("INSERT INTO `userPostRelations` (`postId`, `userId`, `responseType`) VALUES (?,?,?)");
      $results->execute(array($postId, $userId, $likeType));

    } catch(Exception $e){
       echo "User-post insertion data error!";
        exit;
    }

}

function getLikesForPost($postId){
  require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    try{
      $results = $db->prepare("SELECT ehs, likes, loves FROM posts WHERE postId=? LIMIT 1");
      $results->execute(array($postId));

    } catch(Exception $e){
       echo "Like tabulation data error!";
        exit;
    }

    $likeData = $results->fetchAll(PDO::FETCH_ASSOC);
    return $likeData;
}

function getCommentsForPost($postId){
    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    try{
      $results = $db->prepare("SELECT comment, userId FROM comments WHERE postId=?");
      $results->execute(array($postId));

    } catch(Exception $e){
       echo "Comment tabulation data error!";
       exit;
    }

    $commentData = $results->fetchAll(PDO::FETCH_ASSOC);
    return $commentData;

}

function checkIfUserLikedPost($postId, $userId){
  require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    try{
      $results = $db->prepare("SELECT postId FROM userPostRelations WHERE postId=? AND userId=?");
      $results->execute(array($postId, $userId));

    } catch(Exception $e){
       echo "Like tabulation data error!";
        exit;
    }

    $results = $results->fetchAll(PDO::FETCH_ASSOC);
    if (count($results)>0) {
      return true;
    } else{
    return false;
    }


}

function getUserNameForId($userId){
    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    try{
      $results = $db->prepare("SELECT userName FROM users WHERE userId=?");
      $results->execute(array($userId));

    } catch(Exception $e){
       echo "Like tabulation data error!";
        exit;
    }

    $results = $results->fetchAll(PDO::FETCH_ASSOC);
    if (count($results)>0) {
      return $results[0]['userName'];
    }
    else{
      return 'Anonymous';
    }

    
}

?>



