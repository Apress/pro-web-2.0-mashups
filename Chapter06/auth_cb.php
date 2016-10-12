<?php
# ch 6 parameters
  $api_key = "[API_KEY]";
  $secret = "[SECRET]";

  $perms = "read";

  $frob = $_GET['frob'];

  function getResource($url){
    $chandle = curl_init();
    curl_setopt($chandle, CURLOPT_URL, $url);
    curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($chandle);
    curl_close($chandle);

    return $result;
  }

  function getContactList($api_key, $secret, $auth_token)  {
    # calculate API SIG
    # sig string = secret + [arguments listed alphabetically name/value -- including api_key and perms]; don't forget the method call

    $method = "flickr.contacts.getList";
    $sig_string = "{$secret}api_key{$api_key}auth_token{$auth_token}method{$method}";
    $api_sig = md5($sig_string);

    $token_url = "http://api.flickr.com/services/rest/?method=flickr.contacts.getList&api_key={$api_key}&auth_token={$auth_token}&api_sig={$api_sig}";
    $feed = getResource($token_url);
    $rsp = simplexml_load_string($feed);

    return $rsp;

  }

  function getToken($api_key,$secret,$frob) {
    # calculate API SIG
    # sig string = secret + [arguments listed alphabetically name/value -- including api_key and perms]; don't forget the method call

    $method = "flickr.auth.getToken";
    $sig_string = "{$secret}api_key{$api_key}frob{$frob}method{$method}";
    $api_sig = md5($sig_string);

    $token_url = "http://api.flickr.com/services/rest/?method=flickr.auth.getToken&api_key={$api_key}&frob={$frob}&api_sig={$api_sig}";
    $feed = getResource($token_url);
    $rsp = simplexml_load_string($feed);

    return $rsp;
  }

  $token_rsp = getToken($api_key,$secret,$frob);
  $nsid = $token_rsp->auth->user["nsid"];
  $username = $token_rsp->auth->user["username"];
  $auth_token = $token_rsp->auth->token;
  $perms = $token_rsp->auth->perms;

  # display some user info

  echo "You are: ", $token_rsp->auth->user["fullname"],"<br>";
  echo "Your nsid: ", $nsid, "<br>";
  echo "Your username: ", $username,"<br>";
  echo "auth token: ", $auth_token, "<br>";
  echo "perms: ", $perms, "<br>";

  # make a call to getContactList

  $contact_rsp = (getContactList($api_key,$secret,$auth_token));
  $n_contacts = $contact_rsp->contacts["total"];
  $s = "<table>";
  foreach ($contact_rsp->contacts->contact as $contact) {
    $nsid = $contact['nsid'];
    $username = $contact['username'];
    $realname = $contact['realname'];
    $s = $s . "<tr><td>{$realname}</td><td>{$username}</td><td>{$nsid}</td></tr>";
  }
  $s = $s . "</table>";
  echo "Your contact list (which requires read permission) <br>";
  echo "Number of contacts: {$n_contacts}<br>";
  echo $s;

?>
