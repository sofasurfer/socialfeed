<?php
include 'config.php';
include 'templates/header.php';
require 'classes/class.feed.php';

$feed = new SocialFeed();

?>

        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">SocialFeeds</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav">
                <li><a href="#instagram">Instagram</a></li>
                <li><a href="#twitter">Twitter</a></li>
                <li><a href="#facebook">Facebook</a></li>
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </nav>

        <div class="container main">
            <div id="instagram" class="row">
                <h1>SocialFeeds</h1>
                <hr/>
                <?php
                $result = $feed->get_instagram();

                ?>
                <h2>Instagram</h2>
                <div class="grid instagram">
                    <?php
                    // display all user likes
                    foreach ($result->items as $media) {
                        $content = '<div class="col-md-3"><div class="thumbnail">';
                        // output media
                        if ($media->type === 'video') {
                            // video
                            $poster = $media->images->low_resolution->url;
                            $source = $media->videos->standard_resolution->url;
                            $content .= "<video class=\"media video-js vjs-default-skin\" width=\"250\" height=\"250\" poster=\"{$poster}\"
                                   data-setup='{\"controls\":true, \"preload\": \"auto\"}'>
                                     <source src=\"{$source}\" type=\"video/mp4\" />
                                   </video>";
                        } else {
                            // image
                            $image = $media->images->low_resolution->url;
                            $content .= "<img class=\"media\" src=\"{$image}\"/>";
                        }
                        // create meta section
                        $avatar = $media->user->profile_picture;
                        $username = $media->user->username;
                        $comment = $media->caption->text;
                        $content .= "<div class=\"content\">
                                   <div class=\"avatar\" style=\"background-image: url({$avatar})\"></div>
                                   <p>{$username}</p>
                                   <div class=\"comment\">{$comment}</div>
                                 </div>";
                        // output media
                        echo $content . '</div></div>';
                    }
                    ?>
                </div>
            </div>
            <div id="twitter" class="row">
                <h2>Twitter</h2>
                
                <?php 
                $result = $feed->get_twitter();

                echo '<div class="grid twitter">';
                foreach($result as $tweet){
                    echo '<div class="col-md-3"><div class="thumbnail">' . $tweet->text . '<div class="meta">'.$tweet->created_at.'</div></div></div>';
                }
                echo '</div>';          
                ?>
            </div>
            <div id="facebook" class="row">
                <h2>Facebook</h2>
                
                <?php 
                $result = $feed->get_facebook();
                echo "<div class=\"grid facebook\">";
                //echo '<pre>'.print_r($result,true).'</pre>';
                // set counter to 0, because we only want to display 10 posts
                $i = 0;
                foreach($result->data as $post) {
                    echo '<div class="col-md-6"><div class="thumbnail">';
                    echo '<h3>'.$post->attachments->data[0]->title.'</h3>';
                    if(isset($post->attachments->data[0]->description)){
                        echo  '<p>' . $post->attachments->data[0]->description . '</p>';                        
                    }
                    if(isset($post->attachments->data[0]->media->image->src)){
                        echo  '<img src="' . $post->attachments->data[0]->media->image->src . '" />';                        
                    }                    
                    echo '</div></div>';
                } // end the foreach statement
                
                echo "</div>";
                ?>
            </div>
        </div>
        <footer class="container">
            Built by Raptus AG
        </footer>

<?php
include 'templates/footer.php';
?>

