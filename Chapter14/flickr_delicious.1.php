<?php

# This PHP script pushes a Flickr user's favorites into a del.icio.us account

# a function for appending two input strings separated by a comma.
function aconcat ($v, $w)
{
    return $v . "," . $w;
}

# read in passwords for Flickr, the MySQL cache for phpFlickr, and del.icio.us
# these following files define API_KEY and API_SECRET, DB_USER, DB_PASSWORD, DB_SERVER, DB_NAME 
# also DELICIOUS_USER, DELICIOUS_PASSWORD
include("flickr_key.php");
include("mysql_cred.php");
include("delicious.cred.php");

# use phpFlickr with caching
require_once("phpFlickr/phpFlickr.php");

$api = new phpFlickr(API_KEY, API_SECRET);
$db_string = "mysql://" . DB_USER . ":" . DB_PASSWORD. "@" . DB_SERVER . "/". DB_NAME;
$api->enableCache(
    "db", $db_string, 10 
);

# instantiate a del.icio.us object via the phpDelicious library
require_once('php-delicious/php-delicious.inc.php');
$del_obj = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASSWORD);

$username = 'Raymond Yee';

if ($user_id = $api->people_findByUsername($username)) {
  $user_id = $user_id['id'];
} else {
  print 'error on looking up $username';
  exit();
}

# get a list of the user's favorites (public ones first)
# http://www.flickr.com/services/api/flickr.favorites.getPublicList.html

# allow a maximum number of photos to be copied over -- useful for testing.
$maxcount = 100;
$count = 0;

# set the page size and the page number to start with
$per_page = 500;
$page = 1;

# loop over the pages of photos and the photos within each page

do {

if (!$photos = $api->favorites_getPublicList($user_id,"owner_name,last_update,tags", $per_page, $page)) {

  echo "Problem in favorites_getPublicList call: ", $api->getErrorCode(), " ", $api->getErrorMsg();
  exit();
}

$max_page = $photos['pages'];

foreach ($photos['photo'] as $photo) {

  $count += 1;
  if ($count > $maxcount) {
    break;
  }

  echo $photo['id'], "\n";

  # Map Flickr metadata to del.icio.us fields

  # use the URL of the context page as the del.icio.us URL
  $sUrl = "http://www.flickr.com/photos/$photo[owner]/$photo[id]/";

  # copy the photo title as the del.icio.us description
  $sDescription = $photo['title']. " (on Flickr)";

  # set del.icio.us note to empty
  $sNotes = '';

  # use the default date of now.
  $sDate = '';

  # replace previous del.icio.us posts with this URL
  $bReplace = true;

  # copy over the tags and add FlickrFavorite
  $aTags = split(' ', $photo['tags']);
  $aTags[] = 'FlickrFavorite';

  echo $sUrl, $sDescription, " ", $sNotes, " ", array_reduce($aTags, "aconcat") , " ", $sDate, " ", $bReplace;
  print "\n";

  if ($del_obj->AddPost($sUrl, $sDescription, $sNotes, $aTags, $sDate, $bReplace)) {
    print "added $sUrl successfully\n";
  } else {
    print "problem in adding $sUrl\n";
    echo $del_obj->LastError(), " ", $del_obj->LastErrorString(), "\n";
    exit();  // a cautious strategy is to stop on error. 
  }


} // foreach

  $page += 1;

} while (($page <= $max_page) && ($count <= $maxcount)) // do


?>
