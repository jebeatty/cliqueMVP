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
    getRecent();
  }
  elseif ($_GET["action"]=="library") {
    getLibrary();
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
  else {
    $json = json_encode("Action Code Not Recognized");
    echo $json;
  }

}

 
 //Recent Functions
 function getRecent(){
  

  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");
  
  try {
    $results = $db->query("SELECT poster, groupId, url, postDate
                              FROM posts
                              ORDER BY postId DESC
                              LIMIT 15
                              ");
    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $recent = $results->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($recent);
    echo $json;

 }
  
//Library Functions  
function getLibrary(){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");
  
  try {
    $results = $db->query("SELECT groupId, url, postDate
                              FROM posts
                              ORDER BY postId DESC
                              LIMIT 15
                              ");
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
    $results = $db->prepare("SELECT poster, url, postDate
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



/* NOT JSON COMPLIANT YET

function get_posts_recent() {
    require(ROOT_PATH."inc/database.php");
   
    try {

        $results = $db->query("SELECT poster, groupId, url, postDate
                              FROM posts
                              ORDER BY postId DESC
                              LIMIT 4
                              ");
    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $recent = $results->fetchAll(PDO::FETCH_ASSOC);

    return $recent;
}

function get_posts_for_user($userName){
    require(ROOT_PATH."inc/database.php");
   
    try {

        $results = $db->query("SELECT poster, groupId, url, postDate
                              FROM posts
                              WHERE poster==$userName
                              ");
    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $userPosts = $results->fetchAll(PDO::FETCH_ASSOC);
    $userPosts = array_reverse($userPosts);

    return $userPosts;

}

function get_posts_for_group($groupId){
    require(ROOT_PATH."inc/database.php");
   
    try {

        $results = $db->query("SELECT poster, url, postDate
                              FROM posts
                              WHERE group==$groupId
                              ");
    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $groupPosts = $results->fetchAll(PDO::FETCH_ASSOC);
    $groupPosts = array_reverse($groupPosts);

    return $groupPosts;

}*/
?>



