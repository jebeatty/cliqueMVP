
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
                  blockgridHTML += '<li class="panel radius" data-equalizer-watch>';
                  blockgridHTML += '<a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a>';
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

	      


          </script>
       <ul class="large-block-grid-4" id="itemGrid" data-equalizer> 
        <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>
      </ul>
      
    </div>

  <!--End Feature Content-->
  <!-- Modal Views -->
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