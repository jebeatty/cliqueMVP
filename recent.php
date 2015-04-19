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
            <h4> Recommendations for You Today, <?php echo $_SESSION['username'] ?> </p>
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
                
                console.log(response);

                var column1HTML = '<p>';
                var column2HTML = '<p>';


                $.each(response, function(index, post){
                  var mod = index%2;
                  if (mod===1) {
                  var cleanURL = encodeURI(post.url);
                  column1HTML += '<div class="panel radius">';
                  column1HTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';

                  if (post.comment!='') {
                    column1HTML += '<p class="posterComment"> "'+post.comment+'"" </p>'
                  }
                  
                  column1HTML += '<a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a>';
                  column1HTML += '<ul class="button-group round even-3">';
                  column1HTML += '<li><a id="okay'+post.postId+'" class="button secondary" onclick="submitLike(&#39;ehs&#39;,&#39;'+post.postId+'&#39;);">Okay</a></li>';
                  column1HTML += '<li><a id="like'+post.postId+'" class="button" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">Like It</a></li>';
                  column1HTML += '<li><a id="love'+post.postId+'" class="button success" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">Love It</a></li>';
                  column1HTML += '</ul>';
                  column1HTML += '<p class="discussionStats">X Comments and Y Responses </p>';
                  column1HTML += '<p class="discussionStats"><a data-reveal-id="detailModal" onclick="fillModal2(&#39;'+post.postId+'&#39;,&#39;'+cleanURL+'&#39;,&#39;'+post.posterName+'&#39;);"> <i class="fi-comments"></i> See Discussion</a></p> </div>';
                  }
                  else{
                  var cleanURL = encodeURI(post.url);
                  column2HTML += '<div class="panel radius">';
                  column2HTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';
                  
                  if (post.comment!='') {
                    column2HTML += '<p class="posterComment"> "'+post.comment+'"" </p>'
                  }
                  
                  column2HTML += '<a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a>';
                  column2HTML += '<ul class="button-group round even-3">';
                  column2HTML += '<li><a id="okay'+post.postId+'" class="button secondary" onclick="submitLike(&#39;ehs&#39;,&#39;'+post.postId+'&#39;);">Okay</a></li>';
                  column2HTML += '<li><a id="like'+post.postId+'" class="button" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">Like It</a></li>';
                  column2HTML += '<li><a id="love'+post.postId+'" class="button success" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">Love It</a></li>';
                  column2HTML += '</ul>';
                  column2HTML += '<p class="discussionStats">X Comments and Y Responses </p>';
                  column2HTML += '<p class="discussionStats"><a data-reveal-id="detailModal" onclick="fillModal2(&#39;'+post.postId+'&#39;,&#39;'+cleanURL+'&#39;,&#39;'+post.posterName+'&#39;);"> <i class="fi-comments"></i> See Discussion</a></p> </div>';
                  }
                });

                column1HTML += '</p>';
                column2HTML += '</p>';
                $('#leftFeedColumn').html(column1HTML);
                $('#rightFeedColumn').html(column2HTML);

              }); //end getJSON
                
                
            });//end ready

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
         

          function fillModal2(postId, postURL, posterName){
            postURL = decodeURI(postURL);
            var modalHTML = '<p id="modalTitle">Post #'+postId+' was posted by '+posterName+'</p><br>';
            modalHTML += '<a class="embedly-card" href="'+postURL+'" target="_blank"> '+postURL+'</a>';

            $('#detailModalContent').html(modalHTML);
            
            var tab1Content = "<p> Tab 1 test </p>";
            var tab2Content = "<p> Tab 2 test </p>";
            $('#panel1').html(tab1Content);
            $('#panel2').html(tab2Content);
            
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
      <div id="discussionTabs">
        <ul class="tabs" data-tab>
          <li class="tab-title active"><a href="#panel1">Comments</a></li>
          <li class="tab-title"><a href="#panel2">Responses</a></li>
        </ul>
        <div class="tabs-content">
          <div class="content active" id="panel1">
            <p>This is the first panel of the basic tab example. You can place all sorts of content here including a grid.</p>
          </div>
          <div class="content" id="panel2">
            <p>This is the second panel of the basic tab example. This is the second panel of the basic tab example.</p>
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