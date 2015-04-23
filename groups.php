<?php 
define("CURRENT_PAGE_STYLE","css/group-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="invitations">

      
    </div>
    <div id="content"> 
     
      
    </div>

    <script>
      function getInvites(){
        console.log("getting invites");
        $.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){

          
          if (response.length>0){
          inviteHTML = ' <div class="row"> <div class="large-12 columns"> <div class="panel"> <h2> Pending Invitations </h2> </div> </div> </div> <div class="row"><div class="large-10 large-offset-1 columns">';
          
          $.each(response, function(index, groupInfo){
          if (groupInfo['inviterName']=='') {
            groupInfo['inviterName']=='Anonymous';

          }
            
          inviteHTML+= '<div class="panel"> <h4> '+groupInfo['inviterName']+' invited you to join '+groupInfo['groupName']+'</h4>';
          inviteHTML+='<ul class="button-group round"><li> <a onclick="acceptInvite('+groupInfo['groupId']+'); return false;" class="button success"> Accept</a></li><li> <a onclick="rejectInvite('+groupInfo['groupId']+'); return false;" class="button alert"> Reject</a></li> </ul> </div>';

          });// end each
          
          inviteHTML +='</div> </div> ';
          console.log(inviteHTML);
          $('#invitations').html(inviteHTML);
          } 
        });

      }

      function getGroups(){
        $.getJSON('inc/posts.php',{action:"getAllGroupData"},function(response){

            if (response.length>0){
               var newHTML = ' <div class="row"><div class="large-12 columns"><div class="panel"><h2> My Groups </h2></div></div></div>';
               newHTML += '<div class="row">';
               $.each(response, function(index, group){

              newHTML+='<div class="large-12 columns"><div class="title panel"><p>'+group[0].groupName+' </p></div><ul class="large-block-grid-3">';

                $.each(group[1], function(index, post){
                  console.log(post);
                  newHTML +='<li>';
                  newHTML += writeItemHTML(post);
                  newHTML +='</li>';

                });
                newHTML += '</ul><a class="button radius left" href="groupLibrary.php?groupName='+ encodeURI(group[0].groupName)+'&groupId='+group[0].groupId+'"> See More Posts in this Group </a><a class="button right" data-reveal-id="leaveGroupModal" onclick="setModalTitle(&#39;'+group[0].groupName+'&#39;,&#39;'+group[0].groupId+'&#39;);">Leave Group</a></div>';

              }); //end first each
             
              newHTML +='</div>';
              
              $('#content').html(newHTML);
            }
            else{
              $('#content').append('<div class="row"><div class="large-6-columns large-offset-3"> No groups found - Join a discovery group or create your own!</div></div></div>');

            }
           
            });//end getJSON

      }

      function acceptInvite(groupId){
        $.getJSON('inc/invites.php',{action:"acceptInvite", acceptedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            getInvites();
            getGroups();
            location.reload();

          } else{
            alert("Something went wrong in accepting the invite!");
          }

        });

      }

      function rejectInvite(groupId){
        $.getJSON('inc/invites.php',{action:"rejectInvite", rejectedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            getInvites();
            getGroups();
            location.reload();

          } else{
            alert("Something went wrong!");
          }

        });

      }

      function setModalTitle(titleInput, groupId){
      $('#leaveGroupModalTitle').html("Are you sure you'd like to leave "+titleInput+"?");

       $('#modalButtons').html('<a class="button left" onclick="leaveGroup(&#39;'+groupId+'&#39;); return false;"> Yes, Leave Group </a><a class="button right" onclick="customModalClose();"> No, Never Mind </a>');


      }

      function leaveGroup(groupId){
        $.getJSON('inc/invites.php',{action:"leaveGroup", rejectedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            customModalClose();
            getInvites();
            getGroups();
            //location.reload();

          } else{
            alert("Something went wrong!");
          }

        });

      }

      function writeItemHTML(post){
              var itemHTML = '';
              var cleanURL = encodeURI(post.url);
              itemHTML += '<div class="panel radius">';
              itemHTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';
              
              if (post.comment!=null) {
                itemHTML += '<p class="posterComment"> "'+post.comment+'"" </p>'
              }
              
              //oEmbed/embedly api direct call
              var itemIdTag = "itemId_"+post.postId;
              itemHTML+='<div id="'+itemIdTag+'"> </div>';
              callEmbedlyAPIForDiv(itemIdTag,post.url);


              //Comments & Social
              itemHTML += '<ul class="button-group round even-3">';

              if (post.postLiked) {
                itemHTML += '<li><a id="okay'+post.postId+'" class="button secondary socialButton" onclick="submitLike(&#39;ehs&#39;,&#39;'+post.postId+'&#39;);">'+post.ehs+' Okays</a></li>';
                itemHTML += '<li><a id="like'+post.postId+'" class="button socialButton" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">'+post.likes+' Likes</a></li>';
                itemHTML += '<li><a id="love'+post.postId+'" class="button success socialButton" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">'+post.loves+' Loves</a></li>';

              } else{
                itemHTML += '<li><a id="okay'+post.postId+'" class="button secondary socialButton" onclick="submitLike(&#39;ehs&#39;,&#39;'+post.postId+'&#39;);">Okay</a></li>';
                itemHTML += '<li><a id="like'+post.postId+'" class="button socialButton" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">Like It</a></li>';
                itemHTML += '<li><a id="love'+post.postId+'" class="button success socialButton" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">Love It</a></li>';
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
                  $('#okay'+postId).html(response[0]['ehs']+" Okays");
                  $('#like'+postId).html(response[0]['likes']+" Likes");
                  $('#love'+postId).html(response[0]['loves']+" Loves");
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
              //customEmbedHTML += '<iframe src="'+obj.original_url+'" width="640" height="480" scrolling="no" frameborder="0" allowfullscreen></iframe>';
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
            var modalItemIdTag = "modalItemId_"+postId;
            modalHTML='<div id="'+modalItemIdTag+'"> </div>';
            callEmbedlyAPIForDiv(modalItemIdTag,postURL);
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

      $(document).ready(function(){
          getInvites();
          getGroups();
      });//end ready

      
    </script>
  <!--End Feature Content-->
  <!-- Modal Views -->
   <div id="leaveGroupModal" class="reveal-modal small" data-reveal>

      <h2 id="leaveGroupModalTitle">Loading...</h2>
      <p> Please confirm - once you leave, you'll need to be invited back into the group to rejoin. <p>
        <div id="modalButtons">

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

  <!-- End Modal Views -->

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
    function customModalClose(){
        $('#leaveGroupModal').foundation('reveal', 'close');
    }
  </script>
  </body>
  
</html>