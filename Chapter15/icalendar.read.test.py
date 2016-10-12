from icalendar import Calendar
fname = r'D:\Document\Docs\2007\05\iCal-20070508-082112.ics'
cal = Calendar.from_string(open(fname,'rb').read())
ev0 = cal.walk('vevent')[0]
print ev0.keys()
print "summary: ", str(ev0['SUMMARY'])
print "start:", str(ev0['DTSTART'])
# ev0['DTSTART'] is datetime.date() object
print "end:", str(ev0['DTEND'])


