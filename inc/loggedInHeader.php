<?php 
  //session_start();
  if (isset($_SESSION['username'])) {
  } else {
    header('Location: index.php');
  }  
?>

<html>
  <head>
    <title>Clique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/normalize.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link href="css/foundation.css" rel="stylesheet" media="screen">
    <link href="fonts/foundation-icons.css" rel="stylesheet" media="screen">
    <link href="css/my-styles.css" rel="stylesheet" media="screen">
    <link href=<?php echo CURRENT_PAGE_STYLE ?> rel="stylesheet" media="screen">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="http://cdn.embed.ly/jquery.embedly-3.1.1.min.js" type="text/javascript"></script>
    <script src='//cdn.goroost.com/roostjs/pvneqf9i8wdjrh7yj0pxw000xy3ex3me' async></script>
    <script>
      $(document).ready(function(){

        $('#logoutForm').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData ='action=logout';
          $.post(url, formData, function(response){

            $('#logout').html("<p> Logging out... </p>");
            location.href="index.php";
          
          }); //end post - logout
        }); //end submit - logout

        $('#addPosts').submit(function(evt){
          console.log("addPosts event detected!");
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          formData+='&action=newPost';
          console.log(url);
          console.log(formData)
          $.post(url, formData, function(response){

            response=response.substring(1,8);

            if (response=="success") {
              console.log("Response Successful");
              $('#addPosts').html("<p> Post Successful</p>");
              var evt = new CustomEvent('itemUpdated');
              window.dispatchEvent(evt);
              $('#newPostModal').foundation('reveal', 'close');
              resetPostModalHTML();

            } else{
              console.log("Response unsuccessful");
              $('#addPosts').html("<p> Uh-oh, something seems to have gone wrong. Please try again later!</p>");
            }
          });
        });

        $("#autocomplete").autocomplete({
          source: "inc/search.php",
          appendTo: "#newGroupModal",
          delay: 600,
          minLength: 1//search after two characters
         
        });

        
        $('#addGroup').submit(function(evt){
          console.log("create group event detected!");
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          formData+='&action=createGroup';
          console.log(formData)
          $.post(url, formData, function(response){
             console.log("Invite response:");
             console.log(response);
              if (response="success") {
                $('#addGroup').html("<p> Group Created! </p>");
                //wait
                //close
                
                $('#newGroupModal').foundation('reveal', 'close');
                var evt = new CustomEvent('groupAdded');
                window.dispatchEvent(evt);
                resetGroupModalHTML();
              }
              else{
                $('#addGroup').html("<p> Something seems to have gone wrong! Please try again later </p>");
              }
            
          
          }); //end post
        }); //end submit

        $.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){
          if (response.length>0) {
            $('#groupInviteAlert').html('<span class="alert round label"> '+response.length+' </span>');
          } else{
            $('#groupInviteAlert').html('');
          }
          

        });
          

      }); //end ready

    function resetPostModalHTML(){
      postModalHTML= '<h2 id="newPostTitle">New Post</h2><form method="post" action="inc/posts.php" id="addPosts">URL: <input name="url"> <br><br><br>';
      postModalHTML+= 'Comment: <textarea name="message" rows="5" cols="3"></textarea><br><fieldset><legend> Select Groups to Share With:</legend>';
      postModalHTML+= '<input type="checkbox" name="group[]" value="library"> Post to My Library<br>';
      postModalHTML+= '<div id="modalGroups"></div></fieldset>';
      postModalHTML+='<input type="submit" value="Post!"></form><a class="close-reveal-modal" aria-label="Close">&#215;</a>';

      $('#newPostModal').html(postModalHTML);
    }

    function resetGroupModalHTML(){
      groupModalHTML = '';
      groupModalHTML+='<h2 id="newGroupTitle">New Group</h2>';
      groupModalHTML+='<form method="post" action="inc/invites.php" id="addGroup"> Group Name: <input name="groupName"> <br><br><br>';
      groupModalHTML+='Group Description: <textarea name="groupDesc" rows="4" cols="3"> </textarea><br>';
      groupModalHTML+='<fieldset><legend> Select Friends to Invite:</legend>';
      groupModalHTML+='<p> Enter a friend&#39;s email to send an invite. If they are not yet a Clique user, ask them to join and they will see the group invite when they signup with the matching email! </p>';
      groupModalHTML+='<div class="ui-widget"><input placeholder="Enter friend&#39;s email" id="autocomplete" size="30"><p id="warningArea"></p> <button onclick="addFriendToTable(); return false;"> Add Friend to Invite List</button></div>';
      groupModalHTML+='<div>Invited Friends: <br><ul id="friendZone"></ul></div>';
      groupModalHTML+='</fieldset><input type="submit" value="Create Group!"></form>';
      groupModalHTML+='<a class="close-reveal-modal" aria-label="Close">&#215;</a></div>';


      $('#newGroupModal').html(groupModalHTML);
    }

    function addFriendToTable(){
      var friendEmail = $('#autocomplete').val();
      if (friendEmail.indexOf('@')>0) {
        var existingFriends = $('#friendZone').html();
        var newFriend = '<input type="checkbox" name="members[]" value="'+friendEmail;
        if (existingFriends.indexOf(newFriend)==-1) {
          $('#warningArea').html('');
          $('#friendZone').append(newFriend+'" checked> '+friendEmail+'<br>');
        }
        else{
          $('#warningArea').html('Friend already selected');
        }
      } 
      else{
        $('#warningArea').html('Invalid Email');
      }
    }

    </script>
  </head>
  <body>
    <div id="wrapper">

    <!-- Navigation -->

    <div id="navigationArea">
      <nav class="top-bar" data-topbar role="navigation">  
    
          <ul class="title-area">
            <li class = "name"> 
              <h1>
                <a href="recent.php">Clique</a>
              </h1> 
            </li>
            
            <li class = "toggle-topbar menu-icon">
              <a href=""> <span>Menu</span></a>
            </li> 
    
          </ul>
    
    
         <section class = "top-bar-section"> 

              <ul class = "left">
                 <li><a href="recent.php">Home </a></li>
                 <li><a href="library.php">Posts</a></li>
                 <li class="has-dropdown">
                    <a href="groups.php">Groups <span id="groupInviteAlert"> </span></a>
                    
                    <script>
                      $.getJSON('inc/posts.php',{action:"getGroupList"},function(response){
                        groupListHTML ='';
                        modalListHTML ='';
                        $.each(response, function(index, group){
                          cleanName = group.groupName.replace("#","");
                          groupListHTML += '<li><a href="groupLibrary.php?groupName='+cleanName+'&amp;groupId='+group.groupId+'"> '+group.groupName+'</a></li>';
                          modalListHTML += '<input type="checkbox" name="group[]" value="'+group.groupId+'"> '+group.groupName+'<br>';
                        });//end each

                      
                        $('#modalGroups').html(modalListHTML);
                        $('#groupMenu').html(groupListHTML);
                      }); //end getJSON
                    </script>
                    <ul class="dropdown" id='groupMenu'>
                      <li> Test <li>
                    </ul>
                 </li>
                 <li><a href="discover.php">Discover</a></li>
               </ul>

        </section>

        <a class="button radius right logout" data-reveal-id="myModal"> Logout </a>
   
        
        </nav>

        
      </div>
        <a class="button radius left" data-reveal-id="newPostModal"> New Post </a>
        <a class="button radius left" data-reveal-id="newGroupModal"> New Group </a>

    <div id="myModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="modalTitle">Are you sure?</h2>
      <form method="post" action='inc/userAuth.php' id="logoutForm">
      <input class="button radius alert" type="submit" value="Yes, Do it">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="newPostModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="newPostTitle">New Post</h2>
      <form method="post" action='inc/posts.php' id="addPosts">
      URL: <input name="url"> <br>
      <br>
      <br>
      Comment:
      <textarea name="message" rows="6" cols="3">
      </textarea><br>
      <fieldset>
        <legend> Select Groups to Share With:</legend>
        <input type="checkbox" name="group[]" value="library"> Save to My Library
        <br>
          <div id="modalGroups">
          </div>
      </fieldset>
     
      <input class="button radius" type="submit" value="Post!">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="newGroupModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="newGroupTitle">New Group</h2>
      <form method="post" action='inc/invites.php' id="addGroup">
      Group Name: <input name="groupName"> <br>
      <br>
      <br>
      Group Description:
      <textarea name="groupDesc" rows="4" cols="3">
      </textarea><br>

      <fieldset>
        <legend> Select Friends to Invite:</legend>
        <p> Enter a friend's email to send an invite. If they are not yet a Clique user, ask them to join and they will see the group invite when they signup with the matching email! </p>

        <div class="ui-widget">
          <input placeholder="Enter friend's email" id="autocomplete" size="30"><p id="warningArea"></p> <button onclick="addFriendToTable(); return false;"> Add Friend to Invite List</button>
        </div>
        <div>
          Invited Friends: <br>
          <ul id="friendZone">

          </ul>
        </div>
      </fieldset>
     
      <input class="button radius" type="submit" value="Create Group!">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>






