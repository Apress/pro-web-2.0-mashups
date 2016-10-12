<?php

# user and password for google spreadsheet
$user = "[GoogleUSER]";
$pass = "[GooglePASSWORD]";

# set parameters for your version of "My Amazon WishList" Google Spreadsheet
$GSheetID = "[GSheetID]";
$worksheetID="[worksheetID]";
#$GSheetID = "o06341737111865728099.3585145106901556666";
#$worksheetID="od6";

# list entries from a spreadsheet

require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Http_Client');

$service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
$spreadsheetService = new Zend_Gdata_Spreadsheets($client);


#  the following printFeed shows how to parse various types of feeds
#  coming from Google Spreadsheets API
#  function is extracted from
#  http://code.google.com/apis/spreadsheets/developers_guide_php.html

function printFeed($feed)
{
  $i = 0;
  foreach($feed->entries as $entry) {
    if ($entry instanceof Zend_Gdata_Spreadsheets_CellEntry) {
      print $entry->title->text .' '. $entry->content->text . "\n";
    } else if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry) {
      print $i .' '. $entry->title->text .' '. $entry->content->text . "\n";
    } else {
      print $i .' '. $entry->title->text . "\n";
    }
    $i++;
  }
}

# figuring out how to print rows

function printWorksheetFeed($feed)
{
  $i = 2;  # the first row of content is row 2
  foreach($feed->entries as $row) {
    print "Row " . $i .' '. $row->title->text . "\t";
    $i++;
    $rowData = $row->getCustom();
    foreach($rowData as $customEntry) {
      print $customEntry->getColumnName() . " = " . $customEntry->getText(). "\t";
    }
    print "\n";
  }
}


# first print a list of your Google Spreadsheets

$feed = $spreadsheetService->getSpreadsheetFeed();
printFeed($feed);

# Print the content of a specific Spreadsheet/Worksheet
# set a query to return a worksheet and print the contents of the worksheet

$query = new Zend_Gdata_Spreadsheets_ListQuery();
$query->setSpreadsheetKey($GSheetID);
$query->setWorksheetId($worksheetID);
$listFeed = $spreadsheetService->getListFeed($query);
printWorksheetFeed($listFeed);


?>

