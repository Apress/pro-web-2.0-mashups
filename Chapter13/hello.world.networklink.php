<?php
// http://earth.google.com/kml/kml_tut.html#tracking_point
// get the time
$timesnap = date("H:i:s");


// for clarity, place each coordinate into a clearly marked bottom_left or top_right variable

$bboxWest  = isset($_GET['bboxWest']) ? $_GET['bboxWest'] : "-180.0";
$bboxSouth  = isset($_GET['bboxSouth']) ? $_GET['bboxSouth'] : "-90.0";
$bboxEast  = isset($_GET['bboxEast']) ? $_GET['bboxEast'] : "180.0";
$bboxNorth  = isset($_GET['bboxNorth']) ? $_GET['bboxNorth'] : "90.0";


// calculate the approx center of the view -- note that this is innaccurate if the user is not looking straight down
$userlon = (($bboxEast - $bboxWest)/2) + $bboxWest;
$userlat = (($bboxNorth - $bboxSouth)/2) + $bboxSouth;

$response = '<?xml version="1.0" encoding="UTF-8"?>';
$response .= '<kml xmlns="http://earth.google.com/kml/2.2">';
$response .= '<Placemark>';
$response .= "<name>Hello at: $timesnap</name>";

# calculate all the parameters

$arg_text = "";
foreach ($_GET as $key => $val) {
  $arg_text .= "<b>{$key}</b>:{$val}<br>";
}

# calculate a LookAt so that the act of making this 

$description_text = $arg_text;
$description = "<![CDATA[{$description_text}]]>";       
$response .= "<description>{$description}</description>";

$response .= '<Point>';
$response .= "<coordinates>$userlon,$userlat,0</coordinates>";
$response .= '</Point>';
$response .= '</Placemark>';
$response .= '</kml>';
# set $myKMLCode together as a string
 $downloadfile="myKml.kml"; # give a name to appear at the client
 header("Content-disposition: attachment; filename=$downloadfile");
 header("Content-Type: application/vnd.google-earth.kml+xml; charset=utf8");
 header("Content-Transfer-Encoding: binary");
 header("Content-Length: ".strlen($response));
 header("Pragma: no-cache");
 header("Expires: 0");
echo $response;
?>
