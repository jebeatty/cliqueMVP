<?php 
define("CURRENT_PAGE_STYLE","css/discover-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="content">  
      <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>

      <div class="row">
        <div class="large-12 columns" id="discoveryGroups">
          <div class="panel radius">
            <h4>Join a Discovery Group!</h4>
          </div>
              <h2> <i class="fi-web"></i> + <i class="fi-torsos-all"></i> = <i class="fi-lightbulb"></i> </h2> 
          <p> Become an internet explorer by joining a Clique discovery group. You'll be placed in a small group along with 6 other random users, with the goal of sharing 
              the most interesting things you read on the web. It turns out the internet is a big place, and seeing what other folks are reading is a great way to step out of your bubble
              and see parts of the world, both on and offline, that you never knew existed. There's a big world out there - join a Discovery Group and check it out!
            </p>
            <a class="button" data-reveal-id="discoveryModal" onclick="addToDiscovery();"> Sign Me Up, Scotty! </a>
        </div>
      </div>

       <div class="row">
        <div class="large-6 columns">
          <div class="panel radius">
            <h4>Browse Popular Content</h4>
          </div>
          <div id = "popularContent">
            Nothing Here Yet!
          </div>
        </div>
        <div class="large-6 columns">
          <div class="panel radius">
            <h4> Browse Popular Public Groups</h4>
          </div>
          <div id = "popularGroups">
            Nothing Here Yet!
          </div>
        </div>
      </div>







    </div>

    <script>
      function addToDiscovery(){

        $.getJSON('inc/discovery.php',{action:"joinDiscovery"},function(response){
                console.log("RESPONSE:");
                console.log(response);
                
                //get back the groupid and groupname - that way we can add a "go there now" button

              }); //end getJSON


      }
      $(document).ready(function(){
      
            
      });//end ready
    </script>
  <!--End Feature Content-->
  <!-- Modal Content -->
  <div id="discoveryModal" class="reveal-modal small" data-reveal>

      <h2 id="discoveryModalTitle">Loading...</h2>
  
  </div>





  <!--Footer-->
      <footer id="footer">
          <p> &copy; 2015 Clique </p>
      </footer>
  
  
    </div>
  <script>
    document.write('<script src=' +
      ('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +
      '.js><\/script>')

  </script>
  <script src="js/foundation.min.js"></script>
  <script src="js/foundation/foundation.equalizer.js"></script>
  <script>
    $(document).foundation();
    $(document).foundation('equalizer','reflow');
  </script>
  </body>
  
</html>