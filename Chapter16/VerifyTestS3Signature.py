import sha, hmac, base64, urllib

AWSAccessKeyId = "0PN5J17HBGZHT7JJ3X82"
AWSSecretAccessKey = "uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o"
Expires = 1175139620
HTTPVerb = "GET"
ContentMD5 = ""
ContentType = ""
CanonicalizedAmzHeaders = ""
CanonicalizedResource = "/johnsmith/photos/puppy.jpg"
string_to_sign = HTTPVerb + "\n" +  ContentMD5 + "\n" +  ContentType + "\n" + str(Expires) + "\n" + CanonicalizedAmzHeaders + CanonicalizedResource
sig = base64.encodestring(hmac.new(AWSSecretAccessKey, string_to_sign, sha).digest()).strip()
print urllib.urlencode({'Signature':sig})