<?php

/*
 *
 * ucb_critics_gcal.php
 */

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

require_once 'iCalcreator/iCalcreator.class.php';


function getResource($url){
  $chandle = curl_init();
  curl_setopt($chandle, CURLOPT_URL, $url);
  curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($chandle);
  curl_close($chandle);

  return $result;
}

// UCB events calendar



# gets all relevant rules for the first VEVENT in $ical_string
function extract_recurrence($ical_string) {

  $vevent_rawstr = "/(?ims)BEGIN:VEVENT(.*)END:VEVENT/";
  preg_match($vevent_rawstr, $ical_string, $matches);

  $vevent_str = $matches[1];

  # now look for DTSTART, DTEND, RRULE, RDATE, EXDATE, and EXRULE

  $rep_tags = array('DTSTART', 'DTEND', 'RRULE', 'RDATE', 'EXDATE', 'EXRULE');

  $recur_list = array();

  foreach ($rep_tags as $rep) {

    $rep_regexp = "/({$rep}(.*))/i";
    if (preg_match_all($rep_regexp, $vevent_str, $rmatches)) {
      foreach ($rmatches[0] as $match) {
         $recur_list[]= $match;
      }
    }

  } //foreach $rep

  return implode($recur_list,"\r\n");

}


function parse_UCB_Event($event_id) {

  $ical_url = "http://events.berkeley.edu/index.php/ical/event_ID/{$event_id}/.ics";
  $rsp = getResource($ical_url);

  # write out the file
  $tempfile = "temp.ics";
  $fh = fopen($tempfile,"wb");
  $numbytes = fwrite($fh, $rsp);
  fclose($fh);

  $v = new vcalendar(); // initiate new CALENDAR
  $v->parse($tempfile);

  # how to get to the prelude to the vevent? (timezone)

  #echo $v->getProperty("prodid");

  # get first vevent
  $comp = $v->getComponent("VEVENT");

  #print_r($comp);

  $event = array();

  $event["summary"] = $comp->getProperty("summary");
  $event["description"] = $comp->getProperty("description");


# optional -- but once and only once if these elements are here:
# dtstart, description,summary, url

  $dtstart = $comp->getProperty("dtstart", 1, TRUE);
  $event["dtstart"] = $dtstart;

# assume that dtend is used and not duration

  $event["dtend"] = $comp->getProperty("dtend", 1, TRUE);

  $event["location"] = $comp->getProperty("location");
  $event["url"] = $comp->getProperty("url");

# check for recurrence -- RRULE, RDATE, EXDATE, EXRULE

  $recurrence = extract_recurrence($rsp);

  $event_data = array();
  $event_data['event'] = $event;
  $event_data['recurrence'] = $recurrence;
  return $event_data;


} // parse_calendar



function extract_eventIDs($xml)
{

 $ev_list = array();

 foreach ($xml->channel->item as $item) {

   $link = $item->link;
   $k = parse_url($link);
   $ev_list[] = $k['fragment'];
 }
 return $ev_list;
}


// Google Calendar facade


function getClientLoginHttpClient($user, $pass)
{
  $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;

  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}


// code adapted from the Google documentation
// this posts to the DEFAULT calendar -- how do I change to post elsewhere?

function createGCalEvent ($client, $title, $desc, $where, $startDate = '2008-01-20', $startTime = '10:00:00',
     $endDate = '2008-01-20', $endTime = '11:00:00', $tzOffset = '-08', $recurrence=null, $calendar_uri=null)
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $newEvent = $gdataCal->newEventEntry();

  $newEvent->title = $gdataCal->newTitle($title);
  $newEvent->where = array($gdataCal->newWhere($where));
  $newEvent->content = $gdataCal->newContent("$desc");

# if $recurrence is not null then set recurrence -- else set the start and enddate:

  if ($recurrence) {
    $newEvent->recurrence = $gdataCal->newRecurrence($recurrence);
  } else {
    $when = $gdataCal->newWhen();
    $when->startTime = "{$startDate}T{$startTime}{$tzOffset}:00";
    $when->endTime = "{$endDate}T{$endTime}{$tzOffset}:00";
    $newEvent->when = array($when);
  } //if recurrence

// Upload the event to the calendar server
// A copy of the event as it is recorded on the server is returned

    $createdEvent = $gdataCal->insertEvent($newEvent,$calendar_uri);
    return $createdEvent;
}


function listEventsForCalendar($client,$calendar_uri=null) {

  $gdataCal = new Zend_Gdata_Calendar($client);

  $eventFeed = $gdataCal->getCalendarEventFeed($calendar_uri);
  foreach ($eventFeed as $event) {
    echo $event->title->text, "\t", $event->id->text, "\n";
    foreach ($event->when as $when) {
      echo "Starts: " . $when->startTime . "\n";
    }
  }
  echo "\n";
}

function clearAllEventsForCalendar($client, $calendar_uri=null) {

  $gdataCal = new Zend_Gdata_Calendar($client);

  $eventFeed = $gdataCal->getCalendarEventFeed($calendar_uri);
  foreach ($eventFeed as $event) {
    try {
      $event->delete();
      echo "Deleted ", $event->title, "\n";
    } catch (Exception $e) {
      echo 'Caught exception during deletion of : ', $event->title, " ", $e->getMessage(), "\n";
    }
  }

}

// bridge between UCB events calendar and GCal


function postUCBEventToGCal($client,$event_id, $calendar_uri=null) {

  $event_data = @parse_UCB_Event($event_id);

  $event = $event_data['event'];
  $recurrence = $event_data['recurrence'];

  #print_r($event);
  #echo $recurrence;

  $title = $event["summary"];
  $description = $event["description"];
  $where = $event["location"];

# there is a possible parameter that might have TZ info. Ignore for now.
  $dtstart = $event["dtstart"]["value"];
  $startDate = "{$dtstart["year"]}-{$dtstart["month"]}-{$dtstart["day"]}";
  $startTime = "{$dtstart["hour"]}:{$dtstart["min"]}:{$dtstart["sec"]}";

# there is a possible parameter that might have TZ info. Ignore for now.
  $dtend = $event["dtend"]["value"];
  $endDate = "{$dtend["year"]}-{$dtend["month"]}-{$dtend["day"]}";
  $endTime = "{$dtend["hour"]}:{$dtend["min"]}:{$dtend["sec"]}";

  # explicitly set for now instead of calculating.
  $tzOffset = '-07';

  # I might want to do something with the url
  $description .= "\n" . $event["url"];

  echo "Event: ", $title,$description, $where, $startDate, $startTime, $endDate, $endTime, $tzOffset, $recurrence, "\n";

  $new_event = createGCalEvent($client,$title,$description, $where, $startDate, $startTime, $endDate, $endTime, $tzOffset,$recurrence, $calendar_uri);

}


# credentials for Google calendar

$USER = "[USER]";
$PASSWORD = "[PASSWORD]";

# the calendar to write to has a userID of n7irauk3nns30fuku1anh43j5s@group.calendar.google.com
$userID = urlencode("[USERID]");
$calendar_uri = "http://www.google.com/calendar/feeds/{$userID}/private/full";

$client = getClientLoginHttpClient($USER, $PASSWORD);

# get UCB events list

$cc_RSS = "http://events.berkeley.edu/index.php/critics_choice_rss.html";
$rsp = getResource($cc_RSS);

# for now, read the cached file
#$fname = "D:\Document\PersonalInfoRemixBook\examples\ch15\cc_RSS.xml";
#$fh = fopen($fname, "r");

#$rsp = fread($fh, filesize($fname));
#fclose($fh);

$xml = simplexml_load_string($rsp);
$ev_list = extract_eventIDs($xml);

echo "list of events to add:";
print_r($ev_list);

# loop through events list

# DEBUG SET the ev_list explicitly right now
#$ev_list = array(4060);

# limit the number of events to do
$maxevent = 200;
$count = 0;

# clear the existing calendar

echo "Deleting existing events....";
clearAllEventsForCalendar($client,$calendar_uri);


# Add the events
foreach ($ev_list as $event_id) {

  $count +=1;
  if ($count > $maxevent) {
    break;
  }
  echo "Adding event: {$event_id}", "\n";
  postUCBEventToGCal($client,$event_id,$calendar_uri);

}

# list the events on the calendar
listEventsForCalendar($client,$calendar_uri);


?> 