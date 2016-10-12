# parameters for creating the upcoming event -- now I want to write it to eventful
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



def venue_search(keywords,location):
    """
    print out possibilities...
    """
    import eventful

    api = eventful.API('[API-KEY]')
    api.login('[USER]','[PASSWORD]')
    vs = api.call('/venues/search', keywords = keywords, location=location)
    for v in vs['venues']['venue']:
        print "%s\t%s\t%s" % (v['id'], v['name'], v['address'])
    
    
    
import eventful

api = eventful.API('[API-KEY]')
api.login('[USER]','[PASSWORD]')

# If you need to log in:
# api.login('username', 'password')

#http://api.eventful.com/docs/events/new
tz_olsen_path = 'America/New_York'
all_day = '1'
privacy = 1
tags = ''
free = 0

# need to figure out venue_id in eventful
eventful_venue_id = 'V0-001-000412401-5'


ev = api.call('/events/new', title=name, start_time=start_date, stop_time=end_date, tz_olsen_path=tz_olsen_path, all_day=all_day, \
              description=description, privacy=privacy, venue_id=eventful_venue_id)

pprint(ev)