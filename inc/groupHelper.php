<?php


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


?>