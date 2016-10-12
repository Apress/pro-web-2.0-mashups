def listBuckets(AWSAccessKeyId,AWSSecretAccessKey):
    """
    use the REST interface to get the list of buckets -- without the use the Authorization HTTP header
    """
    import time
    # give an hour for the request to expire (3600s)
    expires = int(time.time()) + 3600
    string_to_sign = "GET\n\n\n%s\n/" % (expires)
    print expires, string_to_sign
    sig = base64.encodestring(hmac.new(AWSSecretAccessKey, string_to_sign, sha).digest()).strip()
    request = "http://s3.amazonaws.com?AWSAccessKeyId=%s&Expires=%s&Signature=%s" % \
              (AWSAccessKeyId, expires, sig)
    return request    

if __name__ == "__main__":
    AWSAccessKeyId='[AWSAccessKeyID]'
    AWSSecretAccessKey = '[AWSSecretAccessKey]'
    print listBuckets(AWSAccessKeyId,AWSSecretAccessKey)
