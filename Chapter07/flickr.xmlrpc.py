API_KEY = "[API-KEY]"

from xmlrpclib import ServerProxy, Error, Fault
server = ServerProxy("http://api.flickr.com/services/xmlrpc/")

try:
  from xml.etree import ElementTree as et
except:
  from elementtree import ElementTree as et

# call flickr.search.photos

args = {'api_key': API_KEY, 'tags':'flower', 'per_page':3}
try:
    rsp = server.flickr.photos.search(args)
except Fault, f:
    print "Error code %s: %s" % (f.faultCode, f.faultString)

# show a bit of XML parsing using elementtree
# useful examples:  http://www.amk.ca/talks/2006-02-07/
# context page for photo: http://www.flickr.com/photos/{user-id}/{photo-id}

print rsp
tree = et.XML(rsp)
print "total number of photos: %s" %(tree.get('total'))
for p in tree.getiterator('photo'):
    print "%s: http://www.flickr.com/photos/%s/%s" % (p.get("title"), p.get("owner"), p.get("id"))

