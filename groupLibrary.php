

<?php 
define("CURRENT_PAGE_STYLE","css/library-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <a class="button radius left" data-reveal-id=""> Invite Friends to Group</a>
    <a class="button radius left" data-reveal-id=""> Leave Group </a>
    <div id="content">
      
        <script>
    
            $(document).ready(function(){
              
              function getParameterByName(name) {
    			name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
        		results = regex.exec(location.search);
    			return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
				}
				var groupId = getParameterByName('groupId');
				var groupName = getParameterByName('groupName');

				console.log(groupId);
              $.getJSON('inc/posts.php',{action:"getGroupData",groupId:groupId},function(response){
              	console.log("RESPONSE:");
              	console.log(response);
                var blockgridHTML = '';

                $.each(response, function(index, post){
                  blockgridHTML += '<li class="panel radius">';
                  blockgridHTML += '<a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a>';
                  blockgridHTML += '</li>';
                  
                });//end each

                $('#itemGrid').html(blockgridHTML);

              }); //end getJSON
            });//end ready

          </script>
       <ul class="large-block-grid-4" id="itemGrid"> 
        <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>
      </ul>
      
    </div>

  <!--End Feature Content-->

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