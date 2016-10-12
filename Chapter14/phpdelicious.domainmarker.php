<?php
# a file storing DELICIOUS_USER and DELICIOUS_PASSWORD
include("delicious.cred.php");
require_once('php-delicious/php-delicious.inc.php');

$del_obj = new PhpDelicious(DELICIOUS_USER, DELICIOUS_PASSWORD);

# get all my bookmarks (and check for errors in the request)
#$aPosts = $del_obj->GetAllPosts();

# or I can get a subsection of my posts
# GetPosts('[tag]', '[date]', '[url]'))
# GetAllPosts('[tag]', '[date]', '[url]'))

if (!$aPosts = $del_obj->GetAllPosts()){

  echo $del_obj->LastError(), $del_obj->LastErrorString();
  exit();

}

# go through them and extract the domain.

//print_r($aPosts);

# track the hosts so that I can add the hosts bundle

# set a limit for the number of links the program does -- for debugging

$maxcount = 5;
$count = 0;

$hosts = array();

foreach ($aPosts as $post) {

  $count += 1;
  if ($count > $maxcount) {
    break;
  }

  $url = $post['url'];
  $tags = $post['tags'];

  $url_parts = parse_url($url);
  $host = $url_parts['host'];

  # make a new tag
  $host_tag = "host:" . $host;
  echo $url, " ", $host_tag, "\n";

  # add the post with the new tag
  # parameters of a post

  $sUrl = $post['url'];
  $aTags = $post['tags'];

  # add host_tag to it
  $aTags[] = $host_tag;

  # track hosts that we are seeing
  if (isset($hosts[$host_tag])) {
    $hosts[$host_tag] += 1;
  } else {
    $hosts[$host_tag] = 1;
  }

  $sDescription = $post['desc'];
  $sNotes = $post['notes'];
  $sDate = $post['updated'];
  $bReplace = true;
  echo $sUrl, $sDescription, " ", $sNotes, " ", $sDate, " ", $bReplace;
  print_r (array_unique($aTags));
  print "\n";
  if ($del_obj->AddPost($sUrl, $sDescription, $sNotes, array_unique($aTags), $sDate, $bReplace)) {
    print "added $sUrl successfully\n";
  } else {
    print "problem in adding $sUrl\n";
  }

}

print_r($hosts);



?>
