<?php 
define("CURRENT_PAGE_STYLE","css/recent-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); 

?>
    

    <!--Feature Content-->
    <div id="content">
      <div class="row" id="mainColumn">
        <div class="large-12 columns" id="headline">
          <div class="panel radius" id="headerPanel">
            <h4> Recommendations for You </p>
          </div>
        </div>
        <script>
              (function(w, d){
              var id='embedly-platform', n = 'script';
              if (!d.getElementById(id)){
              w.embedly = w.embedly || function() {(w.embedly.q = w.embedly.q || []).push(arguments);};
              var e = d.createElement(n); e.id = id; e.async=1;
              e.src = ('https:' === document.location.protocol ? 'https' : 'http') + '://cdn.embedly.com/widgets/platform.js';
              var s = d.getElementsByTagName(n)[0];
              s.parentNode.insertBefore(e, s);
              }
              })(window, document);

            $(document).ready(function(){
              
              $.getJSON('inc/posts.php',{action:"recent"},function(response){
                var column1HTML = '<p>';
                var column2HTML = '<p>';

                $.each(response, function(index, post){
                  var mod = index%2;

                  if (mod===1) {
                    column1HTML += writeItemHTML(post);
                  }
                  else{
                    column2HTML += writeItemHTML(post);
                  }
                });

                column1HTML += '</p>';
                column2HTML += '</p>';
                $('#leftFeedColumn').html(column1HTML);
                $('#rightFeedColumn').html(column2HTML);
              }); //end getJSON
                
            });//end ready

          function writeItemHTML(post){
              var itemHTML = '';
              var cleanURL = encodeURI(post.url);
              itemHTML += '<div class="panel radius">';
              itemHTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';
              
              if (post.comment!='') {
                itemHTML += '<p class="posterComment"> "'+post.comment+'"" </p>'
              }
              
              //TO BE REPLACED by oEmbed/embedly api direct call
              //itemHTML += '<a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a>';
              var itemIdTag = "itemId_"+post.postId;
              itemHTML+='<div id="'+itemIdTag+'"> </div>';
              callEmbedlyAPIForDiv(itemIdTag,post.url);


              //Comments & Social
              itemHTML += '<ul class="button-group round even-3">';

              if (post.postLiked) {
                itemHTML += '<li><a id="okay'+post.postId+'" class="button secondary" onclick="submitLike(&#39;ehs&#39;,&#39;'+post.postId+'&#39;);">'+post.likeData[0]['ehs']+' Okays</a></li>';
                itemHTML += '<li><a id="like'+post.postId+'" class="button" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">'+post.likeData[0]['likes']+' Likes</a></li>';
                itemHTML += '<li><a id="love'+post.postId+'" class="button success" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">'+post.likeData[0]['loves']+' Loves</a></li>';

              } else{
                itemHTML += '<li><a id="okay'+post.postId+'" class="button secondary" onclick="submitLike(&#39;ehs&#39;,&#39;'+post.postId+'&#39;);">Okay</a></li>';
                itemHTML += '<li><a id="like'+post.postId+'" class="button" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">Like It</a></li>';
                itemHTML += '<li><a id="love'+post.postId+'" class="button success" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">Love It</a></li>';
              }

              itemHTML += '</ul>';
              itemHTML += '<p class="discussionStats">'+post.commentData.length+' Comments </p>';
              itemHTML += '<p class="discussionStats"><a data-reveal-id="detailModal" onclick="fillModal(&#39;'+post.postId+'&#39;,&#39;'+cleanURL+'&#39;,&#39;'+post.posterName+'&#39;);"> <i class="fi-comments"></i> See Discussion</a></p> </div>';

              return itemHTML;
          }

          function submitLike(likeType, postId){
            if (likeType=='ehs'||likeType=='likes'||likeType=='loves') {
              
              $.getJSON('inc/social.php',{action:"submitLike",likeType:likeType, postId:postId},function(response){
                console.log(response)

                if (response) {
                  $('#okay'+postId).html(response[0]['ehs']);
                  $('#like'+postId).html(response[0]['likes']);
                  $('#love'+postId).html(response[0]['loves']);
                }

              });

            }
          }
         
         function callEmbedlyAPIForDiv(itemIdTag, postURL){
          postURL = postURL.replace(/[\n\r]/g, '');
          $.embedly.defaults.key = '45fd51c22ca84b899138d08c845884d1';
          
          $.embedly.oembed(postURL).done(function(results){
            console.log(postURL);
            console.log("Data Response:");
            console.log(results);
            obj=results[0];
            var customEmbedHTML = '';
            customEmbedHTML +='<div class="panel customEmbedCard"><h5 class="itemHeadline"> '+obj.title+' </h5>';

            if (obj.html) {
              customEmbedHTML +='<div class="flex-video">';
              customEmbedHTML +=obj.html;
              customEmbedHTML +='</div>';

            } else if (obj.thumbnail_url) {
              customEmbedHTML +='<img src="'+obj.thumbnail_url+'">';

            }

            if (obj.description) {
               customEmbedHTML +='<p class="objectDesc">'+obj.description+'</p>';
            } else{
               customEmbedHTML +='<p class="objectDesc">No description! How mysterious...check out the link below to see more</p>';
            }
           
            customEmbedHTML +=' <a href="'+obj.original_url+'" target="_blank"> See more at '+obj.provider_name+' > </a></div>';
            $('#'+itemIdTag).html(customEmbedHTML);
          });
         }

          function fillModal(postId, postURL, posterName){
          
            postURL = decodeURI(postURL);
            var modalHTML = '<p id="modalTitle">Post #'+postId+' was posted by '+posterName+'</p><br>';
            modalHTML += '<a class="embedly-card" href="'+postURL+'" target="_blank"> '+postURL+'</a>';
            $('#detailModalContent').html(modalHTML);

            //comments
            getCommentsForPost(postId);
            

            //comment button
            var commentButtonHTML = '<a class="button postfix" onclick="postComment(&#39;'+postId+'&#39;);"> Post </a>';
            $('#postCommentButton').html(commentButtonHTML);

          }

          function getCommentsForPost(postId){
            var commentsHTML = '';
            $.getJSON('/inc/social.php', {action:"getComments", postId:postId}, function(response){
                console.log(response);

                
                $.each(response, function(index, comment){
                  commentsHTML +='<p class="commenterName"> '+comment.userName+': </p><p class="comment">'+comment.comment+'</p><p class="timeStamp">Timestamp</p>';
                });

                console.log(commentsHTML);
                $('#commentSection').html(commentsHTML);
                
            });

          }

          </script>


        <div class="large-6 columns" id="leftFeedColumn">
          
        </div>
        <div class="large-6 columns" id="rightFeedColumn">
          
        </div>
      </div>
      
    </div>

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

  <!--End Feature Content <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>-->

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
  <script src="js/vendor/modernizr.js"></script>
  <script src="js/foundation.min.js"></script>
  <script>
    $(document).foundation();
    $(document).foundation('tab', 'reflow');
  </script>
  </body>
  
</html>