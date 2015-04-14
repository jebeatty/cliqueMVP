<?php

//manage groups, invites, etc.
// start with action selector + session management

session_start();
if (isset($_SESSION['username'])) {
  $action = $_GET["action"];
  actionSelector($action);

  } else {
    echo "Invalid session data";
  } 

function actionSelector($action){
	if ($action=="createGroup") {
		echo "creating group";
		$groupName = $_GET['groupName'];
		$groupDesc = $_GET['groupDesc'];
		$public = $_GET['public'];
		$invitedMembers = $_GET['members'];
		echo "public:";
		var_dump($_GET['public']);
		createGroup($groupName,$groupDesc,$public,$invitedMembers);
	}
	else{
		echo "invalid action selector";
	}

}



//create groups
function createGroup($name,$desc,$public,$invites){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");
  	echo "creating query";
  	
  	if (!$public) {
  		$public=false;
  	}

	try {
    $results = $db->prepare("INSERT INTO `groups` (`groupName`, `groupDesc`,`public`) VALUES (?,?,?)");
    $results->execute(array($name,$desc,$public));
    $insertId = $db->lastInsertId();
    } catch(Exception $e){
        echo "Data insertion error!";
        exit;
    }
 	
 	sendInvites($insertId,$invites);	
 }


//send invites
function sendInvites($groupId,$invites){
	echo "invites sent";

}

//accept invites
function acceptInvites($groupId,$userId){


}

//reject invites
function rejectInvites($groupId,$userId){


}

//query invites, json return
function queryInvites($groupId){


}






?>