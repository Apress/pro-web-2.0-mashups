<?php
# ch6 parameter
  $api_key = "[API_KEY]";
  $secret = "[SECRET]";

  $perms = "read";

  function login_link($api_key,$secret,$perms) {
    # calculate API SIG
    # sig string = secret + [arguments listed alphabetically name/value -- including api_key and perms]

    $sig_string = "{$secret}api_key{$api_key}perms{$perms}";
    $api_sig = md5($sig_string);

    $url = "http://flickr.com/services/auth?api_key={$api_key}&perms={$perms}&api_sig={$api_sig}";
    return $url;
  }

  $url = login_link($api_key,$secret,$perms);

?>
<html>
  <body>
  <a href="<?php print($url);?>">Login to Flickr</a>
  </body>
</html> 