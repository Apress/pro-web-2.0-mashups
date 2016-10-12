<?php
# flickrgeo.php
# copyright Raymond Yee, 2007
# http://examples.mashupguide.net/ch13/flickrgeo.php

# xmlentities substitutes characters in $string that can be expressed as the predefined XML entities.

function xmlentities ($string)
{ return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string ); }

# converts an associative array representing form parameters and values into the request part of a URL.

function form_url_params($arg_list, $rid_empty_value=TRUE) {
    $list = array();

    foreach ($arg_list as $arg => $val) {
     if (!($rid_empty_value) || (strlen($val) > 0)) {
        $list[] = $arg . "=" . urlencode($val);
     }
    }
    return join("&",$list);
  }

# a simple wrapper around flickr.photos.search for public photos.
# It deals a request for either the Flickr REST or json formats

class flickrwrapper {

  protected $api_key;

  public function __construct($api_key) {
    $this->api_key = $api_key;
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

# returns a HTTP response body and headers

  public function search($arg_list) {

  # attach API key
    $arg_list['api_key'] = $this->api_key;


  # attach parameters specific to the format request, which is either json or rest.
    $format = $arg_list["format"];
    if ($format == "rest") {
      $url = "http://api.flickr.com/services/rest/?method=flickr.photos.search&" . form_url_params($arg_list);
      $rsp = $this->getResource($url);
      $response["body"] = $rsp;
      $response["headers"] = array("Content-Type"=>"application/xml");
      return $response;
    } elseif ($format == "json") {
      $arg_list["nojsoncallback"] = 1;
      $url = "http://api.flickr.com/services/rest/?method=flickr.photos.search&" . form_url_params($arg_list);
      $rsp = $this->getResource($url);
      $response["headers"] = array("Content-Type"=>"text/javascript");
      $response["body"] = $rsp;
      return $response;
    }

  } // search

} //flickrwrapper



class flickr_html {

# generates create a simple form based on the parameters and values of the input associative array $arg_array
# uses $path as the target of the form's action attribute

  public function generate_form($arg_array, $path) {
    $form_html = "";
    foreach ($arg_array as $arg => $default) {
      $form_html .= <<<EOT
{$arg}:<input type="text" size="20" name="{$arg}" value="{$default}"><br>
EOT;
    }

    $form_html = <<<EOT
<form action="{$path}" method="get">
{$form_html}<br>
<input type="submit" value="Go!">
</form>
EOT;

  return $form_html;

  } //generate_form

# generates a simple HTML representations of the results of flickr.photos.search

  public function html_from_pics($rsp) {

    $xml = simplexml_load_string($rsp);
    #print_r($xml);
    #var_dump($xml);
    $s = "";
    $s .= "Total number of photos: " . $xml->photos['total'] . "<br>";

  # http://www.flickr.com/services/api/misc.urls.html
  # http://farm{farm-id}.static.flickr.com/{server-id}/{id}_{secret}.jpg
    foreach ($xml->photos->photo as $photo) {
      $farmid = $photo['farm'];
      $serverid = $photo['server'];
      $id = $photo['id'];
      $secret = $photo['secret'];
      $owner = $photo['owner'];
      $thumb_url = "http://farm{$farmid}.static.flickr.com/{$serverid}/{$id}_{$secret}_t.jpg";
      $page_url = "http://www.flickr.com/photos/{$owner}/{$id}";
      $image_html= "<a href='{$page_url}'><img src='{$thumb_url}'></a>";
      $s .= $image_html;
    }
    return $s;
  }

} // flickr_html


# a class to handle conversion of Flickr results to KML

class flickr_kml {

# helper function to create a new text node with $string that is wrapped by an
# element named by $childName -- and then attach the whole thing to $parentNode.
# allow for a namespace to be specified for $childName

  protected function attachNewTextNode($parentNode,$childName,$childNS="",$string="") {
    $childNode = $parentNode->appendChild(new DOMElement($childName,$childNS));
    $childNode->appendChild(new DOMText($string));
    return $childNode;
  }

# create the subelements for Style

  protected function populate_style($style,$photo) {

    $id = $photo['id'];
    $farmid = $photo['farm'];
    $serverid = $photo['server'];
    $secret = $photo['secret'];
    $square_url = "http://farm{$farmid}.static.flickr.com/{$serverid}/{$id}_{$secret}_s.jpg";

    $id_attr = $style->setAttributeNode(new DOMAttr('id', $id));
    $iconstyle = $style->appendChild(new DOMElement("IconStyle"));
    $icon = $iconstyle->appendChild (new DOMElement("Icon"));
    $href = $this->attachNewTextNode($icon,"href","",$square_url);

    return $style;
  }


# converts the response from the Flickr photo search ($rsp),
# the arguments from the original search ($arg_array),
# the $path of the script to KML

  public function kml_from_pics($arg_array, $path, $rsp) {

    $xml = simplexml_load_string($rsp);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $kml = $dom->appendChild(new DOMElement('kml'));
    $attr = $kml->setAttributeNode(new DOMAttr('xmlns', 'http://earth.google.com/kml/2.2'));
    $document = $kml->appendChild(new DOMElement('Document'));


  # See http://www.flickr.com/services/api/misc.urls.html
  # Remember http://farm{farm-id}.static.flickr.com/{server-id}/{id}_{secret}.jpg syntax for URLs

  # parameters for LookAt -- hard-coded in this instance
    $range = 2000;
    $altitude = 0;
    $heading =0;
    $tilt = 0;

  # make the Style elements first

    foreach ($xml->photos->photo as $photo) {

      $style = $document->appendChild(new DOMElement('Style'));
      $this->populate_style($style,$photo);
    }

    # now make the Placemark elements -- but tuck them under one Folder
    # in the Folder, add URLs for the KML document and how to send the KML document to Google Maps

    $folder = $document->appendChild(new DOMElement('Folder'));
    $folder_name_node = $this->attachNewTextNode($folder,"name","","Flickr Photos");
    $kml_url =  $path . "?" . form_url_params($arg_array,TRUE);
    $description_string = "Total Number of Photos available: {$xml->photos['total']}" . "&nbsp;<a href='{$kml_url}'>KML</a>";
    $description_string .= "&nbsp;<a href='" . "http://maps.google.com?q=" . urlencode($kml_url) .  "'>GMap</a>";
    $folder_description_node = $this->attachNewTextNode($folder,"description","",$description_string);


    #loop through the photos to convert to a Placemark KML element
    foreach ($xml->photos->photo as $photo) {
      $farmid = $photo['farm'];
      $serverid = $photo['server'];
      $id = $photo['id'];
      $secret = $photo['secret'];
      $owner = $photo['owner'];
      $thumb_url = "http://farm{$farmid}.static.flickr.com/{$serverid}/{$id}_{$secret}_t.jpg";
      $med_url = "http://farm{$farmid}.static.flickr.com/{$serverid}/{$id}_{$secret}.jpg";
      $page_url = "http://www.flickr.com/photos/{$owner}/{$id}";
      $image_html= "<a href='{$page_url}'><img src='{$med_url}'></a>";
      $title = $photo['title'];
      $latitude = $photo['latitude'];
      $longitude = $photo['longitude'];

      $placemark = $folder->appendChild(new DOMElement('Placemark'));

      # place the photo title into the name KML element
      $name = $this->attachNewTextNode($placemark,"name","",$title);


      #drop the title and thumbnail into description and wrap in CDATA to work around encoding issues
      $description_string = "{$image_html}";
      $description = $placemark->appendChild(new DOMElement('description'));
      $description->appendChild($dom->createCDATASection($description_string));

      $lookat = $placemark->appendChild(new DOMElement('LookAt'));
      $longitude_node = $this->attachNewTextNode($lookat,"longitude","",$longitude);
      $latitude_node = $this->attachNewTextNode($lookat,"latitude","",$latitude);
      $altitudeNode = $this->attachNewTextNode($lookat,"altitude","",$altitude);
      $altitudeMode = $this->attachNewTextNode($lookat,"altitudeMode","","relativeToGround");
      $rangeNode = $this->attachNewTextNode($lookat,"range","",$range);
      $tiltNode = $this->attachNewTextNode($lookat,"tilt","",$tilt);
      $headingNode = $this->attachNewTextNode($lookat,"heading","",$heading);

      $styleurl = $this->attachNewTextNode($placemark, "styleUrl","","#".$id);

      $point = $placemark->appendChild(new DOMElement('Point'));
      $coordinates_string = "{$longitude},{$latitude},{$altitude}";
      $coordinates = $this->attachNewTextNode($point,"coordinates","",$coordinates_string);

    }

    return $dom->saveXML();
  }

  #generate a network link based on the user search parameters ($arg_list) and the $path to this script

  public function generate_network_link ($arg_list, $path) {

    # look through the $arg_list but get rid of lat/long and blanks
    unset ($arg_list['lat0']);
    unset ($arg_list['lat1']);
    unset ($arg_list['lon0']);
    unset ($arg_list['lon1']);

    $arg_list['o_format'] = 'kml';  //set to KML
    $url = $path . "?" . form_url_params($arg_list,TRUE);
    $url = xmlentities($url);

    # generate a description string to guide user to reparameterizing the network link
    $arg_list['o_format'] = 'html';
    $url2 = $path . "?" . form_url_params($arg_list,TRUE);
    $description = "<a href='{$url2}'>Search Something Different</a>";

$nl = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
  <NetworkLink>
    <flyToView>0</flyToView>
    <name>Pictures from Flickr</name>
    <description><![CDATA[{$description}]]></description>
    <open>1</open>
    <visibility>1</visibility>
    <Link>
      <href>{$url}</href>
      <viewRefreshMode>onStop</viewRefreshMode>
      <viewRefreshTime>3</viewRefreshTime>
      <viewFormat>lat0=[bboxSouth]&amp;lon0=[bboxWest]&amp;lat1=[bboxNorth]&amp;lon1=[bboxEast]</viewFormat>
    </Link>
  </NetworkLink>
</kml>
EOT;

   return $nl;

  }


} // flickr_kml


# this class translates what comes in on the URL and from form values to parameters to submit to Flickr

class flickr_view {

  # this function filters $_GET passed in as $get by parameters that are in $defaults
  # only parameters named in $defaults are allowed -- and if that value isn't set in
  # $get, then this function passes back the default value

  public function form_input_to_user_inputs($get,$defaults) {

    $params = array();
    foreach ($defaults as $arg => $default_value) {
       $params[$arg] = isset($get[$arg]) ? $get[$arg] : $default_value;
    }
    return $params;

  }

  # translate the user inputs to the appropriate ones for Flickr.
  # for example -- fold the latitudes and longitude coordinates into bbox
  # get rid of o_format  for Flickr

  public function user_inputs_to_flickr_params($user_inputs) {

    $search_params = $user_inputs;

    # look at what the format
    $o_format = $user_inputs["o_format"];
    unset ($search_params["o_format"]);

    if (($o_format == "json") || ($o_format == "rest")) {
      $search_params["format"] = $o_format;
    } else {
      $search_params["format"] = "rest";
    }

    #recast the lat and long parameters in bbox

    $bbox = "{$search_params['lon0']},{$search_params['lat0']},{$search_params['lon1']},{$search_params['lat1']}";
    $search_params['bbox'] = $bbox;
    unset($search_params['lon0']);
    unset($search_params['lon1']);
    unset($search_params['lat0']);
    unset($search_params['lat1']);

    return $search_params;

  } // user_inputs_to_flickr_params


} //flickr_view

// API key here

$api_key = "[API_KEY]";

# a set of defaults -- center the search around Berkeley by default and any geotagged photo in that bounding box.
# BTW, this script needs at least geo in exras.
# min_upload_date corresponds to Jan 1, 1996 (Pacific time)
$default_args = array(
  "user_id" => '',
  "tags" => '',
  "tag_mode" => '',
  "text" => '',
  "min_upload_date" => '820483200',
  "max_upload_date" => '',
  "min_taken_date" => '',
  "max_taken_date" => '',
  "license" => '',
  "sort" => '',
  "privacy_filter" => '',
  "lat0" => 37.81778516606761,
  "lon0" => -122.34374999999999,
  "lat1" => 37.92619056937629,
  "lon1" => -122.17208862304686,
  "accuracy" => '',
  "safe_search" => '',
  "content_type" => '',
  "machine_tags" => '',
  "machine_tag_mode" => '',
  "group_id" => '',
  "place_id" => '',
  "extras" => "geo",
  "per_page" => 10,
  "page" => 1,
  "o_format" => 'html'
);

# calculate the path to this script as a URL.
$path = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];

# instantiate the Flickr wrapper and the view object
$fw = new flickrwrapper($api_key);
$fv = new flickr_view();

# get the parameters that have been submitted to it.

$user_inputs = $fv->form_input_to_user_inputs($_GET,$default_args);
$search_params = $fv->user_inputs_to_flickr_params($user_inputs);

# see what the requested format is
$o_format = $user_inputs["o_format"];

# if the user is looking for a network link, calculate the Networklink KML and return it
# with the appropriate Content-Type for KML

if ($o_format == 'nl') {
  $fk = new flickr_kml();
  header("Content-Type:application/vnd.google-earth.kml+xml");
  $downloadfile="flickr.kml"; # give a name to appear at the client
  header("Content-disposition: attachment; filename=$downloadfile");
  print $fk->generate_network_link($user_inputs,$path);
  exit();
}

# If the user is looking instead for json, REST, html, or KML, we query Flickr

$response = $fw->search($search_params);

# If the request is for json or REST, just pass back the results of the Flickr search

if (($o_format == "json") || ($o_format == "rest")) {

  foreach ($response["headers"] as $header => $val) {
    header("{$header}:{$val}");
  }
  print $response["body"];

# if the request is for HTML or KML, do the appropriate transformations.

} elseif ($o_format == "html") {

  # now translate to HTML
  $fh = new flickr_html();
  header("Content-Type:text/html");
  print $fh->generate_form($user_inputs, $_SERVER['PHP_SELF']);
  print $fh->html_from_pics($response["body"]);

} elseif ($o_format == "kml") {

  $fk = new flickr_kml();
  header("Content-Type:application/vnd.google-earth.kml+xml");
  $downloadfile="flickr.kml"; # give a name to appear at the client
  header("Content-disposition: attachment; filename=$downloadfile");
  print $fk->kml_from_pics($user_inputs, $path, $response["body"]);

}


?>
