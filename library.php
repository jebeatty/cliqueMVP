<?php 
define("CURRENT_PAGE_STYLE","css/library-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="content">
      
        <script>
            $(document).ready(function(){
              refreshLibrary();

              window.addEventListener('itemUpdated', function (e) {
                refreshLibrary();
              });
            });//end ready

            function refreshLibrary(){
              $.getJSON('inc/posts.php',{action:"library"},function(response){
                console.log(response);
                var blockgridHTML = '';

                $.each(response, function(index, post){
                  blockgridHTML += '<li>';
                  blockgridHTML += writeItemHTMLForLibrary(post);
                  blockgridHTML += '</li>';
                  
                });//end each

                $('#itemGrid').html(blockgridHTML);

              }); //end getJSON
            }
          </script>
       <ul class="large-block-grid-3" id="itemGrid"> 
        <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>
      </ul>
      
    </div>

  <!--End Feature Content-->
  <!-- Modal Content -->
  <div id="detailModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <div id="detailModalContent">
      <h2 id="modalTitle">Loading...</h2>
      
      </div>

      <div id="commentSection">
        <p> COMMENTS </p>
        <p> No comments yet! </p>
      </div>

      <div id="addCommentSection">
          <div class="row">
            <div class="large-12 columns">
              <div class="row collapse">
                <div class="small-10 columns">
                  <input type="text" id="commentBox" placeholder="Your comment...">
                </div>
                <div class="small-2 columns" id="postCommentButton">
                  
                </div>
                <script>
                function postComment(postId){
                  var url= '/inc/social.php';
                  var formData = 'postId='+postId+'&comment='+$('#commentBox').val();
                  formData+='&action=postComment';
                  console.log(formData);

                  
                  $.post(url,formData,function(response){
                    console.log('Response:' + response);
                    getCommentsForPost(postId);
                  });

                }
              
              </script>
              </div>
            </div>
          </div>
      </div>
      
            
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
      

    </div>

  <!-- End Modal Content -->
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
  <script src="js/embedDetail.js"></script>
  <script src="js/foundation.min.js"></script>
  <script src="js/foundation/foundation.equalizer.js"></script>
  <script>
    $(document).foundation();
    $(document).foundation('equalizer','reflow');
  </script>
  </body>
  
</html>