<?php 
define("CURRENT_PAGE_STYLE","css/index-styles.css");

require_once("inc/config.php");
include(ROOT_PATH . 'inc/header.php'); ?>


    <!--Feature Content-->

    <div class="jumbotron">
      
        <img src="img/welcomePic.png" alt="Mountain View">
      
      
    </div>
  
    <div id="content" class="row">
      <div class="large-4 columns">
        <i class="fi-torsos-all"></i>
        <h3> Connect Through Content</h3>
        <p> Clique is a group-focused platform that makes it easy to share what you are browsing online with friends and vice versa. Share what you love, and see what your friends are enjoying and talking about - the newest Buzzfeed article, the hottest music video, or the most interesting article or blog post.
        </p>
      </div>

      <div class="large-4 columns">
        <i class="fi-heart"></i>
        <h3> Personal, Not 'Personalized'</h3>
        <p> So much of our experience of the internet is driven by feeds, news streams created and controlled by personalization algorithms. Clique is unique because it is built on the idea that your friends, not machines, know you best and can connect you with what inspires and excites you.

        </p>
      </div>

      <div id="secondStop" class="large-4 columns">
        <i class="fi-lightbulb"></i>
        <h3>Discover Something New</h3>
        <p> The internet is a huge, weird place, but we only see a small part of it. By offering your friends a way to share what they are reading, watching, or enjoying online, Clique provides you a way to explore the nooks and crannies of the internet. Break out of your Facebook bubble and discover something unexpected!

        </p>
      </div>


    </div>

  <!--End Feature Content-->

  <!--Footer-->
      <footer id="footer">
          <p> &copy; 2015 Clique </p>
      </footer>
  
  <!--Joyride
  <ol class="joyride-list" data-joyride>
    <li data-id="firstStop" data-text="Go!" data-options="tipLocation:top; tipAnimation:fade; scrollSpeed:600;"> 
      <p> Check out these neat features!</p>
    </li>
    <li data-id="secondStop" data-text="Next"> 
      <p> Lorem Ipsum</p>
    </li>
    <li data-id="thirdStop" data-button="End"> 
      <p> Here is our copyright!</p>
    </li>
  </ol>
-->
    </div>
  <script>
    document.write('<script src=' +
      ('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +
      '.js><\/script>')

  </script>
  <script src="js/foundation.min.js"></script>
  <script>
    $(document).foundation();
    $(document).foundation('joyride','start');
  </script>
  </body>
  
</html>