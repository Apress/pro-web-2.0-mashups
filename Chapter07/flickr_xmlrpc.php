<?php

// flickr_xmlrpc.php
// This code demonstrates how to use XML-RPC using the PEAR::XML-RPC library.  gettime() is the simples example that involves
// calling a timeserver without passsing in any parameters.
// search_example() shows a specific case of how to pass in some parameters for flickr.photos.search
// the flickr_client class generalizes search_example() to handle Flickr methods in general.

require_once('XML/RPC.php');
$API_KEY = '[API-KEY]';

function process_xmlrpc_resp($resp) {
  if (!$resp->faultCode()) {
      $val = $resp->value()->scalarval();
      return $val;
  } else {
    $errormsg = 'Fault Code: ' . $resp->faultCode() . "\n" . 'Fault Reason: ' . $resp->faultString() . "\n";
    throw new Exception ($errormsg);
  }
}

class flickr_client {

  protected $api_key;
  protected $server;

  public function __construct($api_key, $debug) {
    $this->api_key = $api_key;
    $this->server = new XML_RPC_Client('/services/xmlrpc','http://api.flickr.com',80);
    $this->server->setDebug($debug);
  }

  public function call($method,$params) {

    # add the api_key to $params
    $params['api_key'] = $this->api_key;

    # build the struct parameter needed
    foreach ($params as $key=>$val) {
      $xrv_array[$key] = new XML_RPC_Value($val,"string");
    }
    $xmlrpc_val = new XML_RPC_Value ($xrv_array,'struct');

    $msg = new XML_RPC_Message($method, array($xmlrpc_val));
    $resp = $this->server->send($msg);

    return process_xmlrpc_resp($resp);

  } //call


} //class flickr_client


function search_example () {
  GLOBAL $API_KEY;
  $server = new XML_RPC_Client('/services/xmlrpc','http://api.flickr.com',80);
  $server->setDebug(0);

  $myStruct = new XML_RPC_Value(array(
      "api_key" => new XML_RPC_Value($API_KEY, "string"),
      "tags" => new XML_RPC_Value('flower',"string"),
      "per_page" => new XML_RPC_Value('2',"string"),
      ), "struct");

  $msg = new XML_RPC_Message('flickr.photos.search', array($myStruct));
  $resp = $server->send($msg);

  return process_xmlrpc_resp($resp);
}

function gettime() {

  # http://www.xmlrpc.com/currentTime
  $server = new XML_RPC_Client('/RPC2','http://time.xmlrpc.com',80);
  $server->setDebug(0);

  $msg = new XML_RPC_Message('currentTime.getCurrentTime');
  $resp = $server->send($msg);

  return process_xmlrpc_resp($resp);

}

print "current time: ".gettime();
print "output from search_example \n" . search_example(). "\n";

$flickr = new flickr_client($API_KEY,0);

print "output from generalized Flickr client using XML-RPC\n";
print $flickr->call('flickr.photos.search',array('tags'=>'dog','per_page'=>'2'));
?> 