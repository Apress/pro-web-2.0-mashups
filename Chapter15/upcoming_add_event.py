import urllib

# parameters for creating the upcoming event
method = 'event.add'
api_key = '[API-KEY]'
token = '[TOKEN]'
name = 'Joint Conference on Digital Libraries (JCDL) 2008'
venue_id = '56189' # 
category_id = '5'  #education
start_date = '2008-06-15'
end_date = '2008-06-20'
description = """
Since 2001, the Joint Conference on Digital Libraries has served as the major international forum focused on digital libraries and associated technical, practical, and social issues. JCDL encompasses the many meanings of the term "digital libraries", including (but not limited to) new forms of information institutions; operational information systems with all manner of digital content; new means of selecting, collecting, organizing, and distributing digital content; and theoretical models of information media, including document genres and electronic publishing. Digital libraries are distinguished from information retrieval systems because they include more types of media, provide additional functionality and services, and include other stages of the information life cycle, from creation through use. Digital libraries also can be viewed as a new form of information institution or as an extension of the services libraries currently provide.
Representatives from academe, government, industry, and others are invited to participate in this annual conference.  The conference draws from a broad array of disciplines including computer science, information science, librarianship, archival science and practice, museum studies and practice, technology, medicine, social sciences, and humanities.   Topics of the sessions and workshops will cover such aspects of digital libraries as infrastructure; institutions; metadata; content; services; digital preservation; system design; implementation; interface design; human-computer interaction; evaluation of performance; evaluation of usability; collection development; intellectual property; privacy; electronic publishing; document genres; multimedia; social, institutional, and policy issues; user communities; and associated theoretical topics. 
JCDL 2008 will be held in Pittsburgh, Pennsylvania. Once an industrial mecca, Pittsburgh has revitalized itself based on its vibrant arts scene, its amazing scenery, and the more than 25 universities and colleges that call it home. JCDL 2008 is hosted by the University of Pittsburgh's School of Information Sciences. It is organized by an international committee of scholars and leaders in the Digital Libraries field.  Four hundred attendees are expected for the five days of events including a day of cutting edge tutorials; 3 days of papers, panels, and keynotes; and a day of research workshops.
"""
url = 'http://www.jcdl2008.org/'
params = {'api_key': api_key, 'method':method, 'token':token, 'name':name, 'venue_id':venue_id, 'category_id': category_id, \
          'start_date':start_date, 'end_date':end_date, 'description': description, \
          'url': url}


def upcoming_curl_command():
    command = 'curl -v -X POST -d "%s" %s' % (urllib.urlencode(params), "http://upcoming.yahooapis.com/services/rest/")
    print command

def upcoming_event_add():
    from pprint import pprint

    UPCOMING_API_KEY = '[UPCOMING_API_KEY]'

    #from upcoming_api import Upcoming
    from upcoming_api import UpcomingCached
    upcoming = UpcomingCached(UPCOMING_API_KEY)

    new_event = upcoming.event.add(token=token,name=name,venue_id=venue_id, category_id=category_id, start_date=start_date,end_date=end_date, \
                       description=description)
    pprint(new_event)

