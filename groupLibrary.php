
<?php 
define("CURRENT_PAGE_STYLE","css/library-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="groupOptionButtons">


    </div>

    <div id="content">
      
        <script>
    		
            $(document).ready(function(){
				var groupId = getParameterByName('groupId');
				var groupName = getParameterByName('groupName');

				//write the invite/leave button html here with groupId
				var optionButtonHTML ='';
              optionButtonHTML +='<a class="button radius left" data-reveal-id="inviteFriendsModal"> Invite Friends to Group</a>';
              optionButtonHTML +='<a class="button radius left" data-reveal-id="leaveGroupModal" onclick="setModalContent(&#39;'+groupName+'&#39;,&#39;'+groupId+'&#39;);"> Leave Group </a>';
              $('#groupOptionButtons').html(optionButtonHTML);
				
              $.getJSON('inc/posts.php',{action:"getGroupData",groupId:groupId},function(response){
              	console.log("RESPONSE:");
              	console.log(response);
                var blockgridHTML = '';

                $.each(response, function(index, post){
                  blockgridHTML += '<li>';
                  blockgridHTML += writeItemHTML(post);
                  blockgridHTML += '</li>';
                  
                });//end each

                $('#itemGrid').html(blockgridHTML);

              }); //end getJSON

              
              $('#inviteFriends').submit(function(evt){
	          console.log("inviteFriends event detected!");
	          evt.preventDefault();
	          var url = $(this).attr("action");
	          var formData = $(this).serialize();
	          formData+='&action=inviteFriends&groupId='+groupId;

	          console.log(formData)

		          $.post(url, formData, function(response){
		             console.log(response);
		              if (response="success") {
		                $('#inviteFriendsModal').html("<p> Invites sent! </p>");
		                $('#inviteFriendsModal').foundation('reveal', 'close');
		              }
		              else{
		                $('#inviteFriendsModal').html("<p> Something seems to have gone wrong! Please try again later </p>");
		              }
		            
		          
		          }); //end post
	      	  });
            });//end ready

	      function getParameterByName(name) {
  				name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  				var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
  	    		results = regex.exec(location.search);
  				return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
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

	      


          </script>
       <ul class="large-block-grid-3" id="itemGrid" data-equalizer> 
       
      </ul>
      
    </div>

  <!--End Feature Content-->
  <!-- Modal Views -->
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
   <div id="leaveGroupModal" class="reveal-modal small" data-reveal>

      <h2 id="leaveGroupModalTitle">Loading...</h2>
      <p> Please confirm - once you leave, you'll need to be invited back into the group to rejoin. <p>
        <div id="modalButtons">

        </div>
      
      
    

    </div>

	<div id="inviteFriendsModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <form method="post" action='/inc/invites.php' id="inviteFriends">
      <fieldset>
        <legend> Select Friends to Invite:</legend>
        <div class="ui-widget">
          <input placeholder="Enter friend's email" id="inviteAutocomplete" size="30"><p id="inviteWarningArea"></p> <button onclick="addFriendToInviteTable(); return false;"> Invite Friend to Group</button>
           
          <script>
          
          //invite friend code

          $("#inviteAutocomplete").autocomplete({
          source: "inc/search.php",
          minLength: 1//search after two characters
         
          });

          function addFriendToInviteTable(){
            var friendEmail = $('#inviteAutocomplete').val();
            if (friendEmail.indexOf('@')>0) {
              var existingFriends = $('#inviteFriendZone').html();
              var newFriend = '<input type="checkbox" name="members[]" value="'+friendEmail;
              if (existingFriends.indexOf(newFriend)==-1) {
                $('#inviteWarningArea').html('');
                $('#inviteFriendZone').append(newFriend+'" checked> '+friendEmail+'<br>');
              }
              else{
                $('#inviteWarningArea').html('Friend already selected');
              }
            } 
            else{
              $('#inviteWarningArea').html('Invalid Email');
            }
          }
          </script>
        </div>
        <div>
          Selected Friends: <br>
          <ul id="inviteFriendZone">

          </ul>
        </div>
      </fieldset>
     
      <input type="submit" value="Invite Friends!">
      </form>
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

    function setModalContent(titleInput, groupId){
      $('#leaveGroupModalTitle').html("Are you sure you'd like to leave "+titleInput+"?");

       $('#modalButtons').html('<a class="button left" onclick="leaveGroup(&#39;'+groupId+'&#39;); return false;"> Yes, Leave Group </a><a class="button right" onclick="customModalClose();"> No, Never Mind </a>');


      }

      function leaveGroup(groupId){
        $.getJSON('inc/invites.php',{action:"leaveGroup", rejectedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            customModalClose();
            location.replace('groups.php');

          } else{
            alert("Something went wrong!");
          }

        });

      }

      function customModalClose(){
        $('#leaveGroupModal').foundation('reveal', 'close');
    }

  </script>
  </body>
  
</html>