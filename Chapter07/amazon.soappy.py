# amazon search using WSDL
KEY = "[AMAZON-KEY]"

from SOAPpy import WSDL

class amazon_ecs(object):
    def __init__(self, key):
        AMAZON_WSDL = "http://webservices.amazon.com/AWSECommerceService/AWSECommerceService.wsdl?"
        self.key = key
        self.server = WSDL.Proxy(AMAZON_WSDL)
    def ItemSearch(self,Keywords,SearchIndex):
        return self.server.ItemSearch(AWSAccessKeyId=self.key,Request={'Keywords':Keywords,'SearchIndex':SearchIndex})

if __name__ == "__main__":
    aws = amazon_ecs(KEY)
    results= aws.ItemSearch('flower','Books')
    print results.Items.TotalPages,  results.Items.TotalResults
    for item in results.Items.Item:
        print item.ASIN, item.DetailPageURL, item.ItemAttributes.Author
  
    
    
        
        




