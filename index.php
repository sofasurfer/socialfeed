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
              <a class="navbar-brand" href="#">SofaFeeds</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav">
                <li><a target="_blank" href="https://github.com/sofasurfer/socialfeed">Fork me on GitHub</a></li>
              </ul>
            </div>
          </div>
        </nav>

        <div class="container main">
            <div id="all" class="row">

                <?php 
                $all = $feed->get_feeds(array('instagram','twitter','facebook-'));
                
                if( false ){
                    echo '<pre>'.print_r($all,true).'</pre>';
                }else{
                    echo '<div class="grid all">';
                    foreach($all as $item){
                        $image = "";
                        if (!empty($item->media[0]) && $item->media[0]->type === 'video') {
                            $poster = $item->media[0]->source;
                            $source = $item->media[0]->video;
                            $image = "<video class=\"embed-responsive-item\" width=\"350\" 
                                    poster=\"{$poster}\" controls>
                                     <source src=\"{$source}\" type=\"video/mp4\" />
                                   </video>";
                        } else if (!empty($item->media[0]) && $item->media[0]->type === 'video_fb') {
                            $source = $item->media[0]->video;
                            $width =  '100%';
                            if($item->media[0]->height > $item->media[0]->width){
                                $height = '620';
                            } else if($item->media[0]->height == $item->media[0]->width){
                                $height = '350';
                            } else{
                                $height = '200';
                            }
                            $image = '<iframe wmode="opaque" width="'.$width.'" height="'.$height.'" border="0" src="https://www.facebook.com/plugins/video.php?href='.$source.'&show_text=0&width=350&controls=true"></iframe>';
                        } else if (!empty($item->media) && count($item->media) > 1 ){
                            $indicators = array();
                            $slides = array();
                            $counter = 0;
                            foreach($item->media as $mitem){
                                $class = '';
                                if ($counter == 0 ){
                                    $class = 'active';
                                }
                                array_push($indicators, '<li data-target="#carousel-example-generic-'.$item->date.'" data-slide-to="'.$counter.'" class="'.$class.'"></li>');
                                array_push($slides, '<div class="item '.$class.'"><img src="'.$mitem->source.'"></div>');
                                $counter++;

                            }
                            $image = '<div id="carousel-example-generic-'.$item->date.'" class="carousel slide" data-ride="carousel">';
                            $image .= '<ol class="carousel-indicators">';
                            $image .= implode('',$indicators);
                            $image .= '</ol>';
                            $image .= '<div class="carousel-inner" role="listbox">';
                            $image .= implode('',$slides);
                            $image .= '</div>';
                            $image .= '</div>';


                        } else if (!empty($item->media[0]->source)) {
                            // image
                            $source = $item->media[0]->source;
                            $image = "<img class=\"media\" src=\"{$source}\"/>";
                        }
                        echo '<div class="grid-item col-md-4"><div class="thumbnail">'
                                //. '<a target="_blank" href="' . $item->link . '">'
                                // . '<div class="media">' . $image . '</div>' 
                                . $image
                                //. '</a>'
                                . '<div class="meta">' . date('d.m.Y', $item->date) . ' - <a target="_blank" href="' . $item->link . '">' . $item->source . '</a></div>'
                                . '<p>' . $item->text .'</p>' 
                                . '<div class="info" >'
                                . '<i class="fa fa-thumbs-o-up"><span>' . $item->likes . ' Likes</span></i> '
                                . '<i class="fa fa-comment-o"><span>' . $item->comments . ' Comments</span></i>'
                                . '</div>'
                              . '</div></div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <footer class="container">
            Built by <a href="https://twitter.com/sofacoder" target="_blank">@SofaCoder</a>
            fork me on <a href="https://github.com/sofasurfer/socialfeed" target="_blank">GitHub</a>
        </footer>
<?php
include 'templates/footer.php';
?>

