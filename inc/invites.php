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
	echo 'sending invites';
	echo $groupId;
	echo $inviterName;
	foreach ($invites as $userInvite) {
		$userId=getUserIdForEmail($userInvite);
		inviteUserToGroup($userId,$groupId, $inviterName);
	}

	echo "success";

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
  	echo 'alreadyMember returns:';
  	echo $alreadyMember;

  	if (!$alreadyMember) {

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
    return $userData[0]['userId'];
}






?>