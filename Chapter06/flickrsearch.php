<?php
header("Content-Type:text/html");
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>flickrsearch.php</title>
  </head>
  <body>
<?php
if (isset($_GET['tag'])) {
   do_search($_GET['tag']);
} else {
?>
     <form action="<?php echo $_SERVER['PHP_SELF']?>" method="get">
     <p>Search for photos with the following tag:
    <input type="text" size="20" name="tag"/> <input type="submit" value="Go!"/></p>
     </form>
<?php
}
?>
<?php

# uses libcurl to return the response body of a GET request on $url
function getResource($url){
  $chandle = curl_init();
  curl_setopt($chandle, CURLOPT_URL, $url);
  curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($chandle);
  curl_close($chandle);

  return $result;
}

function do_search($tag) {
  $tag = urlencode($tag);

#insert your own Flickr API KEY here

  $api_key = "[API-Key]";
  $per_page="5";
  $url = "http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key={$api_key}&tags={$tag}&per_page={$per_page}";

  $feed = getResource($url);
  $xml = simplexml_load_string($feed);
  print "<p>Total number of photos for {$tag}: {$xml->photos['total']}</p>";

# http://www.flickr.com/services/api/misc.urls.html
# http://farm{farm-id}.static.flickr.com/{server-id}/{id}_{secret}.jpg
foreach ($xml->photos->photo as $photo) {
  $title = $photo['title'];
  $farmid = $photo['farm'];
  $serverid = $photo['server'];
  $id = $photo['id'];
  $secret = $photo['secret'];
  $owner = $photo['owner'];
  $thumb_url = "http://farm{$farmid}.static.flickr.com/{$serverid}/{$id}_{$secret}_t.jpg";
  $page_url = "http://www.flickr.com/photos/{$owner}/{$id}";
  $image_html= "<a href='{$page_url}'><img alt='{$title}' src='{$thumb_url}'/></a>";
  print "<p>$image_html</p>";
}

} # do_search
?>
  </body>
</html>
