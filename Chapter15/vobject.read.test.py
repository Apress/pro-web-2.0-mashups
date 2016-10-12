import vobject
fname = r'D:\Document\Docs\2007\05\iCal-20070508-082112.ics'
cal = vobject.readOne(open(fname,'rb').read())
event = cal.vevent
print event.sortChildKeys()
print "summary: ", event.getChildValue('summary')
print "start:", str(event.getChildValue('dtstart'))
# event.getChildValue('dtstart') is datetime.date() object
print "end:", str(event.getChildValue('dtend'))


