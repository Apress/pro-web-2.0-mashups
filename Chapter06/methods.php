<?php

  require_once("flickr_methods.php");
  $API_KEY = "[API_KEY]";

  $fname = 'flickr.methods.info.txt';

  $fm = new flickr_methods($API_KEY);
# Uncomment the following line to update the local cache of the Flickr methods
  $fm->update_api_data($fname);
# reads the data about the Flickr methods from a local PHP serialization of the methods data
  $m = $fm->restore_api_data($fname);

    $methods = $m["methods"];
    $methods_info = $m["methods_info"];

    header("Content-Type:text/html");
    echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Flickr methods</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  </head>
  <body>
    <table>
      <tr>
        <th>method name</th>
        <th>description</th>
        <th>needs login</th>
        <th>needs signing</th>
        <th>permissions</th>
        <th>args (mandatory)</th>
        <th>args (optional)</th>
      </tr>
<?php
  foreach ($methods_info as $name=>$method) {
    $description = $method["description"];
# calc mandatory and optional arguments
    $m_args = "";
    $o_args = "";
    foreach ($method["arguments"] as $arg){
      //print "arg: {$arg['name']}\n";
      //print_r ($arg);
      // don't list api_key since it is mandatory for all calls
      if ($arg['name'] != 'api_key') {
        if ($arg["optional"] == '1') {
          $o_args .= " {$arg['name']}";
        } else {
          $m_args .= " {$arg['name']}";
        }
      } //if
    }
    print <<<EOT
      <tr>
        <td><a href="http://www.flickr.com/services/api/{$name}.html">{$name}</a></td>
        <td>{$description}</td>
        <td>{$method["needslogin"]}</td>
        <td>{$method["needssigning"]}</td>
        <td>{$method["requiredperms"]}</td>
        <td>{$m_args}</td>
        <td>{$o_args}</td>
      </tr>
EOT;
  }
?>
    </table>
  </body>
</html>
