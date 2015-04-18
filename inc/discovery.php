<?php


define("DISCOVERY_GROUP_LIMIT", 7);

session_start();
if (isset($_SESSION['username'])) {
	if (isset($_POST["action"])) {
		$action = $_POST["action"];
	} else{
		$action = $_GET["action"];
	}

  discoverSelector($action);

  } else {
    echo "Invalid session data";
  } 

 
function discoverSelector($action){
  if ($action=="joinDiscovery") {
  	//find the smallest discovery group with room && one in which the user is not already a member 
  	$openGroupId = getSmallestAvailableDiscoveryGroup($_SESSION['userId']);
  	echo "groupId:";
  	echo $openGroupId;
  	if ($openGroupId>0) {
  		//add the user, increment the # of members
  		//sendBackGroupDataJSON();
  	}
  	else{
  		// if not, create a group
  		//sendBackGroupDataJSON();
  	}
  	
  	//hand back the groupId, groupName, and # of members


  } else{
	echo "invalid action code";
  }
}

function getSmallestAvailableDiscoveryGroup($userId){
	include('groupHelper.php');
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

	try {
	    $results = $db->prepare("SELECT groupId FROM groups WHERE discovery=1 AND numberOfMembers<? ORDER BY numberOfMembers");
	    $results->execute(array(DISCOVERY_GROUP_LIMIT));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

    foreach ($groupData as $discoveryGroup) {
    	$alreadyMember = checkUserGroupMembership($userId,$discoveryGroup['groupId']);
    	if (!$alreadyMember) {
    		return $discoveryGroup['groupId'];
    	}
    }

    return 0;

}

function sendBackGroupDataJSON($groupId){
	//pull the group data
	//$json=json_encode(group data);
	//echo $json;

}


?>