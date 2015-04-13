<?php

// User Authentification Protocols - Login, password recovery, etc.
// We'll start by taking in posted data on user, then calling the appropriate function

require_once("../vendor/autoload.php");
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;

session_start();
$action = $_POST['action'];


if ($action=="FBLogin"){
    FacebookSession::setDefaultApplication('432912816865715', '8e7e5fc1b821813c0e341b9385d9f3b9');
    $helper = new FacebookRedirectLoginHelper('http://www.krizna.com/fbconfig.php' );
    try {
        $session = $helper->getSessionFromRedirect();
        $json = json_encode("Initial FB success");
        echo $json;
        } catch( FacebookRequestException $ex ) {
          // When Facebook returns an error
        } catch( Exception $ex ) {
          // When validation fails or other local issues
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

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    logInUser($username,$password);
}



?>