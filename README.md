# PHP Social Feed

**Author: Kilian Bohnenblust (http://sofasurfer.org)**


Simple library to get social feeds, supports
* Facebook
* Twitter
* Instagram

## Usage
```php
<?php
include 'config.php';
require 'classes/class.feed.php';

$feed = new SocialFeed();

$result = $feed->get_instagram();
foreach ($result->items as $media) {
  echo $media->images->low_resolution->url;
  ...
}

```