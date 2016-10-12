"""
generate iCalendar feed out of the UC Berkeley events calendar
"""

import sys
try:
	from xml.etree import ElementTree
except:
	from elementtree import ElementTree

import httplib2
client = httplib2.Http(".cache")

#from icalendar import Calendar, Event
import vobject

# a function to get individual iCalendar feeds for each event.
# http://events.berkeley.edu/index.php/ical/event_ID/3950/.ics

def retrieve_ical(event_id):
    ical_url = "http://events.berkeley.edu/index.php/ical/event_ID/%s/.ics" % (event_id)
    response, body = client.request(ical_url)
    return body

# read RSS 2.0 feed

from elementtree import ElementTree

cc_RSS = "http://events.berkeley.edu/index.php/critics_choice_rss.html"
response, xml = client.request(cc_RSS)
#print xml
doc = ElementTree.fromstring(xml)

from pprint import pprint
import urlparse

# create a blank iCalendar

ical = vobject.iCalendar()

for item in doc.findall('.//item'):
    # extract the anchor to get the elementID
    # http://events.berkeley.edu/index.php/critics_choice.html#2875
    ev_url = item.find('link').text
    # grab the anchor of the URL, which is the event_ID
    event_id = urlparse.urlparse(ev_url)[5]
    print event_id
    s = retrieve_ical(event_id)
    try:
        ev0 = vobject.readOne(s).vevent
        ical.add(ev0)
    except:
        print "problem in generating iCalendar for event # %s " % (event_id)
        

ical_fname = r'D:\Document\PersonalInfoRemixBook\examples\ch15\critics_choice.ics'
f = open(ical_fname, "wb")
f.write(ical.serialize())
f.close()

# upload my feed to the server
# http://examples.mashupguide.net/ch15/critics_choice.ics

import os
os.popen('scp2 critics_choice.ics "rdhyee@pepsi.dreamhost.com:/home/rdhyee/examples.mashupguide.net/ch15')

