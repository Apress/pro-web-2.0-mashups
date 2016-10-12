<?php
  require_once 'Flickr/API.php';
  include("flickr_key.php");
  # create a new api object
  $api =& new Flickr_API(array(
      'api_key'  => API_KEY,
      'api_secret' => API_SECRET
    ));

  # call a method

  $response = $api->callMethod('flickr.photos.search', array(
      'tags' => 'flower',
      'per_page' => '10'
    ));

  # check the response

  if ($response){
    # response is an XML_Tree root object
    echo "total number of photos: ", $response->children[1]->attributes["total"];
  }else{
    # fetch the error
    $code = $api->getErrorCode();
    $message = $api->getErrorMessage();
  }
?> 