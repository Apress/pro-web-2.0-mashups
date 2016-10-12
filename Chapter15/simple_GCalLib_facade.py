"""
Chapter 15:  simple facade for Python Google Calendar library
"""
__author__ = 'raymond.yee@gmail.com (Raymond Yee)'

EMAIL = '[USER]'
PASSWORD = '[PASSWORD]'

try:
  from xml.etree import ElementTree
except ImportError:
  from elementtree import ElementTree

import gdata.calendar.service
import gdata.calendar
import atom

class MyGCal:
    def __init__(self):
        self.client = gdata.calendar.service.CalendarService()
        self.client.email = EMAIL
        self.client.password = PASSWORD
        self.client.source = 'GCalendarUtil-raymondyee.net-v1.0'
        self.client.ProgrammaticLogin()
    def listAllCalendars(self):
        feed = self.client.GetAllCalendarsFeed()
        print 'Printing allcalendars: %s' % feed.title.text
        for calendar in feed.entry:
          print calendar.title.text
    def listOwnCalendars(self):
        feed = self.client.GetOwnCalendarsFeed()
        print 'Printing owncalendars: %s' % feed.title.text
        for calendar in feed.entry:
          print calendar.title.text          
    def listEventsOnCalendar(self,userID='default'):
      """
      list all events on the calendar with userID
      """
      query = gdata.calendar.service.CalendarEventQuery(userID, 'private', 'full')
      feed =  self.client.CalendarQuery(query)
      for event in feed.entry:
        print event.title.text, event.id.text, event.content.text
        for where in event.where:
          print where.value_string
        for when in event.when:
          print when.start_time, when.end_time
        if event.recurrence:
          print "recurrence:", event.recurrence.text

if __name__ == '__main__':
    gc = MyGCal()
    gc.listAllCalendars()
    # userID for Mashup Guide Demo calendar    
    userID = '9imfjk71chkcs66t1i436je0s0%40group.calendar.google.com'
    gc.listEventsOnCalendar(userID)

        
