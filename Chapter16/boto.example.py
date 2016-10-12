AWSAccessKeyId='[AWSAccessKeyId]'
AWSSecretAccessKey = '[AWSSecretAccessKey]'
FILENAME = 'D:\Document\PersonalInfoRemixBook\858Xtoc___.pdf'
BUCKET = 'mashupguidetest'

from boto.s3.connection import S3Connection

def upload_file(fname, bucket, key, acl='public-read', metadata=None):
    from boto.s3.key import Key
    fpic = Key(bucket)
    fpic.key = key
    #fpic.set_metadata('source','flickr')
    fpic.update_metadata(metadata)
    fpic.set_contents_from_filename(fname)
    fpic.set_acl(acl)
    return fpic

# set up a connection to S3

conn = S3Connection(AWSAccessKeyId, AWSSecretAccessKey)

# retrieve all the buckets
buckets = conn.get_all_buckets()
print "number of buckets:", len(buckets)
# print out the names, creation date, and the XML the represents the ACL
# of the bucket

for b in buckets:
    print "%s\t%s\t%s" % (b.name, b.creation_date, b.get_acl().acl.to_xml())

# get list of all files for the mashupguide bucket

print "keys in " + BUCKETmg_bucket = conn.get_bucket(BUCKET)
keys = mg_bucket.get_all_keys()
for key in keys:
    print "%s\t%s\t%s" % (key.name, key.last_modified, key.metadata)

# upload the table of contents to mashupguide bucket.
metadata = {'author':'Raymond Yee'}
upload_file(FILENAME,mg_bucket,'samplefile','public-read',metadata)

# read back the TOC
toc = mg_bucket.get_key('samplefile')
print toc.metadata