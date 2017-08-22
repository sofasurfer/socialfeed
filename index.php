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
            </div>
          </div>
        </nav>

        <div class="container main">
            <div id="all" class="row">
                <?php 
                $all = $feed->get_feeds();
                
                if( false ){
                    echo '<pre>'.print_r($all,true).'</pre>';
                }else{
                    echo '<div class="grid all">';
                    foreach($all as $item){
                        $image = "";
                        if ($item->media->type === 'video' && $item->source === 'instagram') {
                            $poster = $item->media->source;
                            $source = $item->media->video;
                            $image = "<video class=\"embed-responsive-item\" width=\"230\" height=\"180\" 
                                    poster=\"{$poster}\" controls>
                                     <source src=\"{$source}\" type=\"video/mp4\" />
                                   </video>";
                        } else {
                            // image
                            $source = $item->media->source;
                            $image = "<img class=\"media\" src=\"{$source}\"/>";
                        }
                        echo '<div class="grid-item col-md-3"><div class="thumbnail"><p>' . $item->text .'</p>' . $image 
                                . '<div class="meta">' . date('d.m.Y', $item->date) . ' - ' . $item->source . '</div>'.
                              '</div></div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <footer class="container">
            Built by Raptus AG
        </footer>
<?php
include 'templates/footer.php';
?>

