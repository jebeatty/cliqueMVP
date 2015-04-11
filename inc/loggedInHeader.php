<?php 
  session_start();
  if (isset($_SESSION['username'])) {
  } else {
    header('Location: index.php');
  }  
?>

<html>
  <head>
    <title>Clique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="css/normalize.css" rel="stylesheet" media="screen">
    <link href="css/foundation.css" rel="stylesheet" media="screen">
    <link href="fonts/foundation-icons.css" rel="stylesheet" media="screen">
    <link href="css/my-styles.css" rel="stylesheet" media="screen">
    <link href=<?php echo CURRENT_PAGE_STYLE ?> rel="stylesheet" media="screen">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script>
      $(document).ready(function(){
        $('form').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData ='action=logout';
          $.post(url, formData, function(response){

            console.log(response);

            $('#logout').html("<p> Logging out... </p>");
            location.href="http://localhost//index.php";
          
          }); //end post
        }); //end submit
      }); //end ready

    </script>
  </head>
  <body>
    <div id="wrapper">

    <!-- Navigation -->

    <div id="navigationArea">
      <nav class="top-bar">  
    
          <ul class="title-area">
            <li class = "name"> 
              <h1>
                <a href="#">CLIQUE</a>
              </h1> 
            </li>
            
            <li class = "toggle-topbar menu-icon">
              <a href=""> <span>Menu</span></a>
            </li> 
    
          </ul>
    
    
         <section class = "top-bar-section"> 
              <ul class = "right">
                 <li><a href="recent.php">RECENT   <span class="alert round label">2</span></a></li>
                 <li><a href="library.php">Library</a></li>
                 <li class="has-dropdown">
                    <a href="groups.php">Groups </a>
                    <script>
                      $.getJSON('inc/posts.php',{action:"getGroupList"},function(response){
                        groupListHTML ='';
                        $.each(response, function(index, group){
                          groupListHTML += '<li><a href="groupLibrary.php?groupName='+group.groupName+'&amp;groupId='+group.groupId+'"> '+group.groupName+'</a></li>';
                        });//end each
                        console.log("MENU HTML:"+groupListHTML);
                        $('#groupMenu').html(groupListHTML);
                      }); //end getJSON
                    </script>
                    <ul class="dropdown" id='groupMenu'>
                    </ul>
                 </li>
                 <li><a href="#">DISCOVER</a></li>
               </ul>
        </section>
      <a class="button radius right" data-reveal-id="myModal"> Logout </a>
        </nav>
    </div>

    <div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="modalTitle">Are you sure?</h2>
      <form orm method="post" action='/inc/userAuth.php' id="logout">
      <input type="submit" value="Yes, Do it">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>