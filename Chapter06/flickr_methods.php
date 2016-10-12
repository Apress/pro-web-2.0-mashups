<?php
# flickr_methods.php
# can use this class to return a $methods (an array of methods) and $methods_info -- directly from the Flickr API
# or via a cached copy

class flickr_methods {

  protected $api_key;

  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  public function test() {
    return $this->api_key;
  }

# generic method for retrieving content for a given url.
  protected function getResource($url){
    $chandle = curl_init();
    curl_setopt($chandle, CURLOPT_URL, $url);
    curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($chandle);
    curl_close($chandle);

    return $result;
  }

# return simplexml object for $url if successful with specified number of retries
  protected function flickrCall($url,$retries) {
    $success = false;
    for ($retry = 0; $retry < $retries; $retry++) {
      $rsp = $this->getResource($url);
      $xml = simplexml_load_string($rsp);
      if ($xml["stat"] == 'ok') {
        $success = true;
        break;
      }
    } // for
    if ($success) {
      return $xml;
    } else {
      throw new Exception("Could not successfully call Flickr");
    }
  }

# go through all the methods and list

  public function getMethods() {

  // would be useful to return this as an array (later on, I can have another method to group them under common prefixes.)

    $url = "http://api.flickr.com/services/rest/?method=flickr.reflection.getMethods&api_key={$this->api_key}";
    $xml = $this->flickrCall($url, 3);
    foreach ($xml->methods->method as $method) {
      //print "${method}\n";
      $method_list[] = (string) $method;
    }
    return $method_list;
  }

# get info about a given method($api_key, $method_name)

  public function getMethodInfo($method_name) {

    $url =
    "http://api.flickr.com/services/rest/?method=flickr.reflection.getMethodInfo&api_key={$this->api_key}&method_name={$method_name}";
    $xml = $this->flickrCall($url, 3);
    return $xml;
  }


# get directly from Flickr the method data
# returns an array with data
  public function download_flickr_methods () {

    $methods = $this->getMethods();

    // now loop to grab info for each method

# this counter lets me limit the number of calls I make -- useful for testing
    $limit = 1000;
    $count = 0;

    foreach ($methods as $method) {

      $count += 1;
      if ($count > $limit) {
        break;
      }

      $xml = $this->getMethodInfo($method);
      $method_array["needslogin"] = (integer) $xml->method["needslogin"];
      $method_array["needssigning"] = (integer) $xml->method["needssigning"];
      $method_array["requiredperms"] = (integer) $xml->method["requiredperms"];
      $method_array["description"] = (string) $xml->method->description;
      $method_array["response"] = (string) $xml->method->response;
    // loop through the arguments
      $args = array();
      foreach ($xml->arguments->argument as $argument) {
        $arg["name"] = (string) $argument["name"];
        $arg["optional"] = (integer) $argument["optional"];
        $arg["text"] = (string) $argument;
        $args[] = $arg;
      }
      $method_array["arguments"] = $args;

    // loop through errors
      $errors = array();
      foreach ($xml->errors->error as $error) {
        $err["code"] = (string) $error["code"];
        $err["message"] = (integer) $error["message"];
        $err["text"] = (string) $error;
        $errors[] = $err;
      }
      $method_array["errors"] = $errors;

      $methods_info[$method] = $method_array;
    }

    $to_store['methods'] = $methods;
    $to_store['methods_info'] = $methods_info;
    return $to_store;

  } // download_Flickr_API

# store the data
  public function store_api_data($fname, $to_store) {

    $to_store_str = serialize($to_store);
    $fh = fopen($fname,'wb') OR die ("can't open $fname!");
    $numbytes = fwrite($fh, $to_store_str);
    fclose($fh);
  }

# convenience method for updating the cache
  public function update_api_data($fname) {

    $to_store = $this->download_flickr_methods();
    $this->store_api_data($fname,$to_store);
  }

# restore the data

  public function restore_api_data($fname) {

    $fh = fopen($fname,'rb') OR die ("can't open $fname!");
    $contents = fread($fh, filesize($fname));
    fclose($fh);
    return unserialize($contents);

  }

} //flickr_methods
