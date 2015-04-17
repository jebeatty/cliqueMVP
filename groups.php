<?php 
define("CURRENT_PAGE_STYLE","css/group-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="invitations">

      
    </div>
    <div id="content"> 
     
      
    </div>
    <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>
    <script>
      function getInvites(){
        console.log("getting invites");
        $.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){

          console.log(" Invite REPSONSE: ");
          console.log(response);
          if (response.length>0){
          inviteHTML = ' <div class="row"> <div class="large-12 columns"> <div class="panel"> <h2> Pending Invitations </h2> </div> </div> </div> <div class="row"><div class="large-10 large-offset-1 columns">';
          
          $.each(response, function(index, groupInfo){
          inviteHTML+= '<div class="panel"> <h4> '+groupInfo['groupName']+'</h4>';
          inviteHTML+='<ul class="button-group round"><li> <a onclick="acceptInvite('+groupInfo['groupId']+'); return false;" class="button success"> Accept</a></li><li> <a onclick="rejectInvite(); return false;" class="button alert"> Reject</a></li> </ul> </div>';

          });// end each
          
          inviteHTML +='</div> </div> ';
          console.log(inviteHTML);
          $('#invitations').html(inviteHTML);
          } 
        });

      }

      function getGroups(){
        $.getJSON('inc/posts.php',{action:"getAllGroupData"},function(response){
            $('#content').html(' <div class="row"><div class="large-12 columns"><div class="panel"><h2> My Groups </h2></div></div></div>');

            if (response.length>0){
               var newHTML = '<div class="row">';
               $.each(response, function(index, group){

              newHTML+='<div class="large-12 columns"><div class="panel"><p>'+group[0].groupName+' </p></div><ul class="large-block-grid-3">';

                $.each(group[1], function(index, post){
                  newHTML +='<li><a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a></li>';

                });
                newHTML += '</ul><a class="button radius left" href="groupLibrary.php?groupName='+ encodeURI(group[0].groupName)+'&groupId='+group[0].groupId+'"> See More Posts in this Group </a><a class="button right">Leave Group</a></div>';

              }); //end first each
             
              newHTML +='</div>';
              console.log(newHTML);
              $('#content').append(newHTML);
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
            location.reload(true);

          } else{
            alert("Something went wrong in accepting the invite!");
          }

        });

      }

       function rejectInvite(){
        console.log('invite rejected');

      }


      $(document).ready(function(){
          getInvites();
          getGroups();
      });//end ready

      
    </script>
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