<?php 

  session_start();

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
    <link href=<?php echo CURRENT_PAGE_STYLE?> rel="stylesheet" media="screen">
    <!-- sajflasjdh -->
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script>
      $(document).ready(function(){
        $('#loginForm').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          formData+='&action=login';

          $.post(url, formData, function(response){
            console.log(response);

            if (response=="true") {
              //$('#loginForm').html("<p> Login succeeded. Welcome <?php echo $_SESSION['username'] ?> </p>");
              location.href="http://localhost//recent.php";
            } else{
              $('#loginForm').html("<p> Login failed. Thanks for trying!</p>");
            };
          
          }); //end post - login
        }); //end submit - login

         $('#signupForm').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          formData+='&action=signup';

          $.post(url, formData, function(response){
            console.log(response);

            if (response=="true") {
             // $('#signupForm').html("<p> Signup succeeded! Welcome to Clique, <?php echo $_SESSION['username'] ?> </p>");
              location.href="http://localhost//recent.php";
            } else{
              $('#signupForm').html("<p> Signup failed. Please try again later</p>");
            };
          
          }); //end post - signup
        }); //end submit - signup
      }); //end ready

    </script>
  </head>
  <body>
    <div id="wrapper">

    <!-- Navigation -->

    <div id="navigationArea">
      <nav class="top-bar clearBar">  
    
          <ul class="title-area">
            <li class = "name"> 
              <h1>
                <a href="index.php">Clique</a>
              </h1> 
            </li>

            <li class = "toggle-topbar menu-icon">
              <a href=""> <span>Menu</span></a>
            </li> 
          </ul>
    
  
    <a class="button radius right" data-reveal-id="loginModal"> Login </a>
    <a class="button radius right" data-reveal-id="signupModal"> Sign Up </a>
        </nav>
    </div>

    <div id="loginModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="modalTitle">Welcome! Please Login</h2>
      <div>
        <form method="post" action='/inc/userAuth.php' id="loginForm">
          Username: <input name="username"> <br>
          Password: <input name="password"> <br>
          <input type="submit" value="Login">
        </form>
      </div>
      OR <br>
      <?php
      require_once("vendor/autoload.php");
      use Facebook\FacebookSession;
      use Facebook\FacebookRedirectLoginHelper;

      FacebookSession::setDefaultApplication('432912816865715', '8e7e5fc1b821813c0e341b9385d9f3b9');

      $helper = new FacebookRedirectLoginHelper('http://localhost/inc/fbLogin.php');
      $params = array('email','public_profile', 'user_status', 'user_friends');
      echo '<a href="' . $helper->getLoginUrl($params) . '">Login with Facebook</a>'

      ?>  
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="signupModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="modalTitle">Welcome! Please Sign Up</h2>
      <div>
        <form method="post" action='/inc/userAuth.php' id="signupForm">
          Username: <input name="username"> <br>
          Password: <input name="password"> <br>
          Email: <input name="email"> <br>
          <input type="submit" value="Sign up">
        </form>
      </div>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
   