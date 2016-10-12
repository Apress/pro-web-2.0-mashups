<?php

function getResource($url){
  $chandle = curl_init();
  curl_setopt($chandle, CURLOPT_URL, $url);
  curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($chandle);
  curl_close($chandle);
  return $result;
}

// get a collection_id
  $cid  = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : "1000014156";  //default to my own

  $url = "http://collections.local.yahooapis.com/LocalSearchService/V1/getCollection?appid=raymondyee.net&collection_id=". urlencode($cid);
  $feed = getResource($url);
  $xml = simplexml_load_string($feed);

  
  //header("Content-Type:text/csv");
  $out = fopen('php://output', 'w');
  
  $header = array("Caption","Street Address","City","State","Zip");
  fputcsv($out, $header);  
  
   foreach ($xml->Item as $item) {
    $caption = $item->Title;
    $street_address = $item->Address->Address1;
    $city = $item->Address->City;
    $state = $item->Address->State;
    $zip = $item->Address->PostalCode;
    fputcsv($out, array($caption,$street_address,$city,$state,$zip)); 
  }

    fclose($out);
?>
