<?php

//manage groups, invites, etc.
// start with action selector + session management


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

function actionSelector($action){
	if ($action=="createGroup") {
		$groupName = $_POST['groupName'];
		$groupDesc = $_POST['groupDesc'];
		$public = $_POST['public'];
		$invitedMembers = $_POST['members'];
		
		createGroup($groupName,$groupDesc,$public,$invitedMembers);
	}
	else if ($action=="inviteFriends") {
		$invitedMembers = $_POST['members'];
		$groupId = $_POST['groupId'];
		$inviterName=$_SESSION['username'];

		echo $groupId;
		echo var_dump($invitedMembers);
		sendInvites($groupId, $inviterName, $invitedMembers);	
		
	}
  else if($action=="getGroupInfo"){
    //gets information on members & invites
    $groupId = $_GET['groupId'];

    $groupInfo=getGroupData($groupId);
    $memberInfo=getAllMembersForGroup($groupId);
    array_push($groupInfo, $memberInfo);
    $json = json_encode($groupInfo);
    echo $json;


  }
	else if ($action=="getGroupInvites"){
		queryInvites($_SESSION['userId']);


	} else if ($action=="acceptInvite") {
		$groupId=$_GET['acceptedGroupId'];
		$userId = $_SESSION['userId'];
		acceptInvite($groupId,$userId);
		echo json_encode('success');
	}
	else if ($action=="rejectInvite"){
		$groupId=$_GET['rejectedGroupId'];
		$userId = $_SESSION['userId'];
		deleteInvite($groupId,$userId);
		echo json_encode('success');

	} else if ($action=="leaveGroup"){
		$groupId=$_GET['rejectedGroupId'];
		$userId = $_SESSION['userId'];
		removeUserFromGroup($groupId,$userId);
		echo json_encode('success');

	}

	else{
		echo "invalid action selector:";
		echo $action;

	}

}



//create groups
function createGroup($name,$desc,$public,$invites){
	require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");
  	
  	if (!$public) {
  		$public=false;
  	}

	try {
    $results = $db->prepare("INSERT INTO `groups` (`groupName`, `groupDesc`,`public`) VALUES (?,?,?)");
    $results->execute(array($name,$desc,$public));
    $insertId = $db->lastInsertId();
    } catch(Exception $e){
        echo "Group creation data insertion error!";
        exit;
    }
 	addUserToGroup($_SESSION['userId'],$insertId, $name);
 	$inviterName=$_SESSION['username'];
 	sendInvites($insertId, $inviterName, $invites);	
 	
 }


//send invites
function sendInvites($groupId, $inviterName, $invites){
	echo "groupId:";
	echo $groupId;
	echo "inviter:";
	echo $inviterName;
	foreach ($invites as $userInvite) {
		$userId=getUserIdForEmail($userInvite);
    if ($userId) {
      inviteUserToGroup($userId,$groupId, $inviterName);
    }
		else{
      createInviteForNonuser($userInvite,$groupId, $inviterName);
    }
	}

	echo "success";

}

function createInviteForNonuser($userInvite,$groupId, $inviterName){

    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

      try {
      $results = $db->prepare("INSERT INTO `pendingInvites` (`groupId`, `userEmail`, `inviterName`) VALUES (?,?,?)");
      $results->execute(array($groupId, $userInvite, $inviterName));

      } catch(Exception $e){
          echo "User invite data insertion error!";
          exit;
      }
    }

}

//accept invites
function acceptInvite($groupId,$userId){
	$groupName=getGroupNameForId($groupId);
	addUserToGroup($userId,$groupId, $groupName);
	deleteInvite($groupId, $userId);
	

}

//reject invites
function deleteInvite($groupId,$userId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");
  	
	try {
    $results = $db->prepare("DELETE FROM groupInvites WHERE groupId=? AND userId=?");
    $results->execute(array($groupId, $userId));

    } catch(Exception $e){
        echo "Data deletion error!";
        exit;
    }
   
}

//query invites, json return
function queryInvites($userId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

	try {
    $results = $db->prepare("SELECT groupId, inviterName FROM groupInvites WHERE userId=?");
    $results->execute(array($userId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

    foreach ($groupData as &$groupInvite) {
    	$groupName = getGroupNameForId($groupInvite['groupId']);
    	if (!$groupName) {
    		$groupName="Unnamed Group";
    	}
    	$groupInvite["groupName"]=$groupName;

    }

  
    echo json_encode($groupData);

}

function inviteUserToGroup($userId,$groupId, $inviterName){
	
  	$alreadyMember = checkUserGroupMembership($userId,$groupId);
  	$alreadyInvited = checkUserGroupInviteStatus($userId,$groupId);

  	if (!$alreadyMember && !$alreadyInvited) {

  	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

  		try {
    	$results = $db->prepare("INSERT INTO `groupInvites` (`groupId`, `userId`, `inviterName`,`accepted`) VALUES (?,?,?,0)");
    	$results->execute(array($groupId, $userId, $inviterName));

    	} catch(Exception $e){
	        echo "User invite data insertion error!";
	        exit;
    	}
  	}
	

}


function checkUserGroupMembership($userId, $groupId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

  	try {
    	$results = $db->prepare("SELECT relationId FROM userGroupRelations WHERE userId=? AND groupId=? ");
    	$results->execute(array($userId, $groupId));

    } catch(Exception $e){
        echo "User membership data  error!";
        exit;
    }

    $resultCount = $results->rowCount();
    if ($resultCount>0) {
    	return true;
    }
    else{
    	return false;
    }


}

function checkUserGroupInviteStatus($userId, $groupId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

  	try {
    	$results = $db->prepare("SELECT groupId FROM groupInvites WHERE userId=? AND groupId=? ");
    	$results->execute(array($userId, $groupId));

    } catch(Exception $e){
        echo "User membership data  error!";
        exit;
    }

    $resultCount = $results->rowCount();
    if ($resultCount>0) {
    	return true;
    }
    else{
    	return false;
    }


}

function addUserToGroup($userId,$groupId, $groupName){
	$alreadyMember = checkUserGroupMembership($userId,$groupId);

  	if (!$alreadyMember) {

		require_once("../inc/config.php");
	  require(ROOT_PATH."inc/database.php");

		try {
	    $results = $db->prepare("INSERT INTO `userGroupRelations` (`groupId`, `groupName`,`userId`) VALUES (?,?,?)");
	    $results->execute(array($groupId, $groupName, $userId));

	    } catch(Exception $e){
	        echo "User data insertion error!";
	        exit;
	    }
	}

}

function removeUserFromGroup($groupId, $userId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");
  	
	try {
    $results = $db->prepare("DELETE FROM `userGroupRelations` 
    						WHERE userId=? 
    						AND groupId=?");

    $results->execute(array($userId, $groupId));

    } catch(Exception $e){
        echo "User data insertion error!";
        exit;
    }

}

//these two functions could be consolidated into 1 getValueForKeyInTable() function
function getGroupNameForId($groupId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");


	try {
    $results = $db->prepare("SELECT groupName FROM groups WHERE groupId = ?");
    $results->execute(array($groupId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

    return $groupData[0]["groupName"];
}


function getUserIdForEmail($email){
	require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");


	try {
    $results = $db->prepare("SELECT userId FROM users WHERE email = ?");
    $results->execute(array($email));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $userData = $results->fetchAll(PDO::FETCH_ASSOC);

    if (count($userData)>0) {
      return $userData[0]['userId'];
    }
    else{
      return false;
    }
    
}


function getGroupData($groupId){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");


  try {
    $results = $db->prepare("SELECT groupName, groupDesc FROM groups WHERE groupId=?");
    $results->execute(array($groupId));

  } catch(Exception $e){
    echo "Data selection error!";
    exit;
  }

  $results = $results->fetchAll(PDO::FETCH_ASSOC);
  return $results;

}

function getAllMembersForGroup($groupId){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");


  try {
    $results = $db->prepare("SELECT userId FROM userGroupRelations WHERE groupId = ?");
    $results->execute(array($groupId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $results = $results->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as &$memberInfo) {
      $userName = getUserNameForId($memberInfo['userId']);
      $memberInfo['userName']=$userName;
    }

    return $results;
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