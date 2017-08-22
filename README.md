# PHP Social Feed

**Author: Kilian Bohnenblust (http://sofasurfer.org)**


Simple library to get social feeds, supports:
* Facebook
* Twitter
* Instagram

## Usage

Rename config.tpl to config.php and set the required parameters.

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

Hope this helps :) check out the example page: http://dev.sofasurfer.org/?
