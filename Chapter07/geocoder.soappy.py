from SOAPpy import WSDL

wsdl_url = r'http://geocoder.us/dist/eg/clients/GeoCoderPHP.wsdl'
server = WSDL.Proxy(wsdl_url)

# let's see what operations are support
server.show_methods()

# geocode the Apress address
address = "2855 Telegraph Ave, Berkeley, CA"
result = server.geocode(location=address)
print "latitude and longitude: %s, %s" % (result[0]['lat'], result[0]['long'])
