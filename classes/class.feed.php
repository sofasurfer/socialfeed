<?php

/*
    Retrieves different social media feeds

*/
class SocialFeed {

    public function __construct() {

    }

    /*
        Retrieve all feeds together
    */
    public function get_feeds($sources=array('instagram','facebook','twitter')){

        // Feed list
        $feed = array();

        /*
            Get Instagram feed
        */
        if( in_array('instagram', $sources)){
            $ig_feed = $this->get_instagram();
            foreach($ig_feed->data as $ig){
                $media = array();
                // Get video/image sources
                if( $ig->type === 'video' ){
                    $media[] = array(
                        'type' => $ig->type,
                        'video' => $ig->videos->standard_resolution->url,
                        'source' => $ig->images->standard_resolution->url
                    );
                }else if(!empty($ig->carousel_media)){
                    $media = array();
                    foreach($ig->carousel_media as $image){
                        array_push($media, array(
                            'type' => 'photo',
                            'source' => $image->images->standard_resolution->url
                        ));
                    }
                }else{
                    array_push($media, array(
                        'type' => $ig->type,
                        'source' => $ig->images->standard_resolution->url
                    ));
                }
                // Replace hashtags and user links
                $message = preg_replace('/#([0-9a-zA-Z]+)/i', '<a target="_blank" href="https://www.instagram.com/explore/tags/$1/">#$1</a>', $ig->caption->text);
                $message = preg_replace('/@([0-9a-zA-Z]+)/i', '<a target="_blank" href="https://www.instagram.com/$1/">@$1</a>', $message);

                // Add new item
                $item = array(
                    'source' => 'instagram',
                    'date' => $ig->created_time,
                    'text' => $message,
                    'media' => $media,
                    'likes' => $ig->likes->count,
                    'comments' => $ig->comments->count,
                    'link' => $ig->link
                    );
                array_push($feed, $item);
            }
        }

        /*
            Get Facebook feed
        */
        if( in_array('facebook', $sources)){
            $fb_feed = $this->get_facebook();
            foreach($fb_feed->data as $ig){
                $media = array();
                // Get video/image sources
                if(!empty($ig->attachments->data[0]->type) && $ig->attachments->data[0]->type === 'video_inline'){
                    $media[] = array(
                        'type' => 'video_fb',
                        'width' => $ig->attachments->data[0]->media->image->width,
                        'height' => $ig->attachments->data[0]->media->image->height,                        
                        'video' => $ig->attachments->data[0]->url,
                        'source' => $ig->attachments->data[0]->media->image->src,
                        );
                }else if(!empty($ig->attachments->data[0]->media->image->src)){
                    $media[] = array(
                        'type' => $ig->attachments->data[0]->type,
                        'source' => $ig->attachments->data[0]->media->image->src,
                        );  
                }else{
                    $media[] = array('type'=>'none');
                }

                // Replace long URL's
                $message='';
                if(!empty($ig->message)){
                    $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
                    $message = preg_replace($regex, ' ', $ig->message);
                    $message = preg_replace('/#([0-9a-zA-Z]+)/i', '<a target="_blank" href="https://www.facebook.com/hashtag/$1/">#$1</a>', $message);                
                }
                $item = array(
                    'source' => 'facebook',
                    'date' => strtotime($ig->created_time),
                    'text' => $message,
                    'media' => $media,
                    'likes' => count($ig->likes->data),
                    'comments' => count($ig->comments->data),
                    'link' => $ig->link
                    );
                array_push($feed, $item);
            }
        }

        /*
            Get Twitter feed
        */
        if( in_array('twitter', $sources)){
            $tw_feed = $this->get_twitter();
            foreach($tw_feed as $ig){
                $media = array();
                $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i'; 
                $message = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $ig->text);

                // Replace hashtags
                $message = preg_replace('/#([0-9a-zA-Z]+)/i', '<a target="_blank" href="https://twitter.com/hashtag/$1/">#$1</a>', $message);
                $message = preg_replace('/@([0-9a-zA-Z]+)/i', '<a target="_blank" href="https://twitter.com/$1/">@$1</a>', $message);
                // Get media
                if(!empty($ig->entities->media)){
                    // error_log($ig->text . ': ' . print_r($ig->entities->media,true));

                    if($ig->entities->media[0]->type === 'video'){
                        $media[] = array(
                            'type' => 'video',
                            'video' => '',
                            'source' => $ig->entities->media[0]->media_url_https,
                            );
                    }else if($ig->extended_entities->media[0]->type === 'animated_gif'){
                        $media[] = array(
                            'type' => 'video',
                            'video' => $ig->extended_entities->media[0]->video_info->variants[0]->url,
                            'source' => $ig->entities->media[0]->media_url_https,
                            );                        
                    }else{
                        $media[] = array(
                            'type' => $ig->entities->media[0]->type,
                            'source' => $ig->entities->media[0]->media_url_https,
                            );  
                    }
                }else{
                    $media[] = array('type'=>'none');
                }


                $item = array(
                    'source' => 'twitter',
                    'date' => strtotime($ig->created_at),
                    'text' => $message,
                    'media' => $media,
                    'likes' => $ig->favorite_count,
                    'comments' => $ig->retweet_count,                
                    'link' => 'https://twitter.com/statuses/'.$ig->id
                    );
                array_push($feed, $item);
            } 
        }

        // sort result
        usort($feed, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });
        $feed = array_reverse($feed);

        return json_decode(json_encode($feed));
    }



    /*
        Get Instagram Feed
    */
    public function get_instagram(){
        if($this->get_cache('instagram')){
            $json = $this->get_cache('instagram');
        }else{
            $url = "https://api.instagram.com/v1/users/self/media/recent/?access_token=".INSTAGRAM_ACCESSTOKEN;

            if (!function_exists('curl_init')){ 
                die('CURL is not installed!');
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $json = curl_exec($ch);
            curl_close($ch);
            $this->set_cache('instagram',$json);
        }
        return json_decode($json);

    }

    /*
        Get facebook feed
    */
    public function get_facebook(){
        if($this->get_cache('facebook')){
            $json = $this->get_cache('facebook');
        }else{
            require_once('classes/facebook/src/facebook.php');
            // connect to app
            $config = array();
            $config['appId'] = FACEBOOK_APPID;
            $config['secret'] = FACEBOOK_SECRET;
            $config['fileUpload'] = false; // optional

            // instantiate
            $facebook = new Facebook($config);

            // now we can access various parts of the graph, starting with the feed
            $pagefeed = $facebook->api("/" . FACEBOOK_PAGEID . "/feed?fields=attachments,message,link,created_time,likes,comments");
            $json = json_encode($pagefeed);
            $this->set_cache('facebook',$json);
        }
        return json_decode($json);
    }


    /*
        Get twitter feed
    */
    public function get_twitter(){

        if($this->get_cache('twitter')){
            $json = $this->get_cache('twitter');
        }else{
            $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
            $oauth = array( 'oauth_consumer_key' => TWITTER_CONSUMER_KEY,
                            'oauth_nonce' => time(),
                            'oauth_signature_method' => 'HMAC-SHA1',
                            'oauth_token' => TWITTER_ACCESS_TOKEN,
                            'oauth_timestamp' => time(),
                            'oauth_version' => '1.0');

            $base_info = $this->buildBaseString($url, 'GET', $oauth);
            $composite_key = rawurlencode(TWITTER_CONSUMER_SECRET) . '&' . rawurlencode(TWITTER_ACCESS_TOKEN_SECRET);
            $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
            $oauth['oauth_signature'] = $oauth_signature;

            // Make requests
            $header = array($this->buildAuthorizationHeader($oauth), 'Expect:');
            $options = array( CURLOPT_HTTPHEADER => $header,
                              //CURLOPT_POSTFIELDS => $postfields,
                              CURLOPT_HEADER => false,
                              CURLOPT_URL => $url,
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_SSL_VERIFYPEER => false);

            $feed = curl_init();
            curl_setopt_array($feed, $options);
            $json = curl_exec($feed);
            curl_close($feed);
            $this->set_cache('twitter',$json);
        }
        return json_decode($json);
    }

    private function buildBaseString($baseURI, $method, $params) {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    private function buildAuthorizationHeader($oauth) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }


    /*
        Caches the feed items
    */
    private function get_cache($key){
        $filename = 'cache/'. $key;
        if(file_exists($filename) && (filemtime($filename)+FEED_CACHE_TIME) > time() ){
            return file_get_contents($filename);
        }
        return false;
    }

    private function set_cache($key,$data){
        $filename = 'cache/'. $key;
        $myfile = fopen($filename, "w") or die("Unable to open file! Make sure the cache folder exists in the root directory ");
        fwrite($myfile, $data);
        fclose($myfile);
    }  
}
