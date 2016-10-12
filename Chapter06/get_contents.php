<?php
// retrieve Atom feed of recent flower-tagged photos in Flickr
$url = "http://api.flickr.com/services/feeds/photos_public.gne?tags=flower&lang=en-us&format=atom";
$content = file_get_contents($url);
echo $content;
?>