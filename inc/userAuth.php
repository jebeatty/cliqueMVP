<?php

// User Authentification Protocols - Login, password recovery, etc.
// We'll start by taking in posted data on user, then calling the appropriate function


session_start();
$action = $_POST['action'];


if ($action=="FBLogin"){
    FacebookSession::setDefaultApplication('432912816865715', '8e7e5fc1b821813c0e341b9385d9f3b9');
    $helper = new FacebookRedirectLoginHelper('');
    try {
        $session = $helper->getSessionFromRedirect();
       
        } catch( FacebookRequestException $ex ) {
          // When Facebook returns an error
        } catch( Exception $ex ) {
          // When validation fails or other local issues
        }
        if ($session) {
             $json = json_encode("Initial FB success");
        echo $json;
        }
    
} else{
    $userName=$_POST['username']; 
    $userName=stripcslashes($userName);
    $userName=mysql_real_escape_string($userName);


    $password=$_POST['password']; 
    $password=stripcslashes($password);
    $password=mysql_real_escape_string($password);

    if ($action==="login") {
        logInUser($userName, $password);
       

    } else if ($action==="signup") {
        $email=$_POST['email']; 
        $email=stripcslashes($email);
        $email=mysql_real_escape_string($email);
        signUpUser($userName, $password, $email);

    } else if ($action==="logout") {
        session_unset();
        if(isset($_COOKIE[session_name()])) {
            setcookie(session_name(),'',time()-3600); # Unset the session id
        }
        session_destroy();
        $json = json_encode("Log out complete");
        echo $json;
    }  else{
        $json = json_encode("Action Code Error");
        echo $json;
    }


}





//simple user login function - returns true if the login is successful, false otherwise
function logInUser($username, $password){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");

 	try {
    $results = $db->prepare("SELECT userName, userId, firstName, lastName
                              FROM users
                              WHERE userName=? AND password=?
                              ");
    $results->execute(array($username,$password));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $user = $results->fetchAll(PDO::FETCH_ASSOC);

    if (count($user)>0) {
    	$json = json_encode(true);
    	$_SESSION['username'] = $username;
    	$_SESSION['userId'] = $user[0]['userId'];
        session_regenerate_id();
    }
    else{
    	$json = json_encode(false);

    }
    echo $json;
    
}

//simple user signup function - inserts the user into the DB then logs them in.
function signUpUser($username, $password, $email){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");

    try {
    $results = $db->prepare("INSERT INTO users 
                              (userName, password,email)
                              VALUES(?,?,?)
                              ");
    $results->execute(array($username,$password,$email));
    $insertId = $db->lastInsertId();

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }
    $userId = $insertId;
    $userEmail = $email;

    updateInvites($userId, $userEmail);

    logInUser($username,$password);
}

function updateInvites($userId, $userEmail){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");

  try {
    $results = $db->prepare("SELECT groupId, inviterName
                              FROM pendingInvites
                              WHERE userEmail=?
                              ");
    $results->execute(array($userEmail));
  } catch(Exception $e){
      echo "Invite update loading error!";
      exit;

  }
  $groupList = $results->fetchAll(PDO::FETCH_ASSOC);
  if (count($groupList)>0) {
    foreach ($groupList as $group) {
      //for each pending invite, move it to the group invites then delete
      try {
        $results = $db->prepare("INSERT INTO `groupInvites` (`groupId`, `userId`, `inviterName`,`accepted`) VALUES (?,?,?,0)");
        $results->execute(array($group['groupId'], $userId, $group['inviterName']));

      } catch(Exception $e){
          echo "User invite data insertion error!";
          exit;
      }

      try {
        $results = $db->prepare("DELETE FROM pendingInvites WHERE groupId=? AND userEmail=?");
        $results->execute(array($group['groupId'], $userEmail));

      } catch(Exception $e){
          echo "User invite data deletion error!";
          exit;
      }
    } 
  }
}



?>