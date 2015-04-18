<?php

//SOP Action Selector 

session_start();
if (isset($_SESSION['username'])) {
	if (isset($_POST["action"])) {
		$action = $_POST["action"];
	} else{
		$action = $_GET["action"];
	}

  socialActionSelector($action);

  } else {
    echo "Invalid session data";
  } 

 
function socialActionSelector($action){
  include('groupHelper.php');

}

//social dynamics handling
//likes, comments, and responses!!!!

function checkForUserPostRelation($postId, $userId){




}

//likes
function getLikesForPost($postId){

}

function addLikeToPost($postId, $userId, $likeType){


}

//comments
function getCommentsForPost($postId){


}

function addCommentToPost($postId, $userId, $comment){


}

function flagComment($commentId){


}

//responses
function getResponsesForPost($postId){



}

function addResponseToPost($postId, $userId, $comment, $url){


	
}


?>