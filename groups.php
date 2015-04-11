<?php 
define("CURRENT_PAGE_STYLE","css/group-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="content">  
      <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>
    </div>

    <script>
      $(document).ready(function(){
        console.log("beginning group data request");
        $.getJSON('inc/posts.php',{action:"getAllGroupData"},function(response){
            console.log("REPSONSE: ");
            console.log(response);
            var newHTML = '<div class="row">';

            $.each(response, function(index, group){

              newHTML+='<div class="large-12 columns"><div class="panel"><p> Posts for '+group[0].groupName+' </p></div><ul class="large-block-grid-3">';

                $.each(group[1], function(index, post){
                  newHTML +='<li><a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a></li>';

                });
                newHTML += '</ul></div>';

              }); //end first each
             
              newHTML +='</div';
              console.log(newHTML);
              $('#content').html(newHTML);
            });//end getJSON

            
      });//end ready
    </script>
  <!--End Feature Content-->

  <!--Footer-->
      <footer id="footer">
          <p> &copy; 2015 Clique </p>
          <p> Current User: <?php echo $_SESSION['username']; ?></p>
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