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
                  column1HTML += '<div class="panel radius">';
                  column1HTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';
                  column1HTML += '<a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a>';
                  column1HTML += '<p> Recommendation text from the user who sent it. Check it out! </p>'
                  column1HTML += '<button>Funny</button><button>Interesting</button><button>Inspiring</button> </div>';
                  }
                  else{
                  column2HTML += '<div class="panel radius">';
                  column2HTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';
                  column2HTML += '<a class="embedly-card" href="'+post.url+'" target="_blank"> '+post.url+'</a>';
                  column2HTML += '<p> Recommendation text from the user who sent it. Check it out! </p>'
                  column2HTML += '  <button>Funny</button><button>Interesting</button><button>Inspiring</button> </div>';
                  }
                });

                column1HTML += '</p>';
                column2HTML += '</p>';
                $('#leftFeedColumn').html(column1HTML);
                $('#rightFeedColumn').html(column2HTML);

              }); //end getJSON
                
            });//end ready

          </script>

        <div class="large-6 columns" id="leftFeedColumn">
          
        </div>
        <div class="large-6 columns" id="rightFeedColumn">
          
        </div>
      </div>
      
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
  <script>
    $(document).foundation();
  </script>
  </body>
  
</html>