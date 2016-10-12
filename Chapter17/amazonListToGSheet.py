"""
an example to copy over a public Amazon wishlist to a Google Spreadsheet owned by the user
based on code at http://code.google.com/apis/spreadsheets/developers_guide_python.html
"""

GoogleUser = "[GoogleUSER]"
GooglePW = "[GooglePASSWORD]"
GSheet_KEY = "[GSheetID]"
# GSheet_KEY = "o06341737111865728099.3585145106901556666"
GWrkSh_ID = "[worksheetID]"
GWrkSh_ID = "od6"

AMAZON_LIST_ID = "[LIST_ID_FOR_WISHLIST]"
#AMAZON_LIST_ID = "1U5EXVPVS3WP5"
AMAZON_ACCESS_KEY_ID = "[AMAZON_KEY]"


from xml.dom import minidom

import gdata.spreadsheet.service

def getText(nodelist):
    """
    convenience function to return all the text in an array of nodes
    """
    rc = ""
    for node in nodelist:
        if node.nodeType == node.TEXT_NODE:
            rc = rc + node.data
    return rc

# a sample row for testing the insertion of a row into the spreadsheet
GS_Example_Row = {'asin': '1590598385', 'dateadded': '6/5/2007', 'detailpageurl': 'http://www.amazon.com/gp/product/1590598385/ref=wl_it_dp/103-8266902-5986239?ie=UTF8&coliid=I1A0WT8LH796DN&colid=1U5EXVPVS3WP5', 'author': 'Joel Spolsky', 'quantitydesired': '1', 'price': '13.25', 'title': "Smart and Gets Things Done: Joel Spolsky's Concise Guide to Finding the Best Technical Talent (Hardcover) "}
GS_HEADER = ['ASIN', 'DetailPageURL', 'Title', 'Author', 'Date Added', 'Price', 'Quantity Desired']
GS_KEYS = ['asin', 'detailpageurl', 'title', 'author', 'dateadded', 'price', 'quantitydesired']

class GSheetForAmazonList:
    def __init__(self,user=GoogleUser,pwd=GooglePW):
        gd_client = gdata.spreadsheet.service.SpreadsheetsService()
        gd_client.email = user
        gd_client.password = pwd
        gd_client.source = 'amazonListToGsheet.py'
        gd_client.ProgrammaticLogin()
        self.gd_client = gd_client
    def setKey(self,key):
        self.key = key
    def setWkshtId(self,wksht_id):
        self.wksht_id = wksht_id        
    def listSpreadsheets(self):
        """
        return a list with information about the spreadsheets available to the user
        """
        sheets = self.gd_client.GetSpreadsheetsFeed()
        return map(lambda e: (e.title.text , e.id.text.rsplit('/', 1)[1]),sheets.entry)
    def listWorkSheets(self):
        wks = self.gd_client.GetWorksheetsFeed(key=self.key)
        return map(lambda e: (e.title.text , e.id.text.rsplit('/', 1)[1]),wks.entry)
    def getRows(self):
        return self.gd_client.GetListFeed(key=self.key,wksht_id=self.wksht_id).entry
    def insertRow(self,row_data):
        return self.gd_client.InsertRow(row_data,key=self.key,wksht_id=self.wksht_id)
    def deleteRow(self,entry):
        return self.gd_client.DeleteRow(entry)
    def deleteAllRows(self):
        entrylist = self.getRows()
        i = 0
        for entry in entrylist:
            self.deleteRow(entry)
            i += 1
            print "deleted row ", i

class amazonWishList:

# we can use Python and WSDL
# http://webservices.amazon.com/AWSECommerceService/AWSECommerceService.wsdl?

# I've been wondering how to introspect using WSDL -- Mark Pilgrim has some answers:
# http://www.diveintopython.org/soap_web_services/introspection.html
# well -- the introspection of the input parameters doesn't seem to yield the useful stuff I was hoping for more info

    def __init__(self,listID=AMAZON_LIST_ID,amazonAccessKeyId=AMAZON_ACCESS_KEY_ID):
        self.listID = listID
        self.amazonAccessKeyId = amazonAccessKeyId
        self.getListInfo()
        
    def getListInfo(self):

        aws_url = "http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&Version=2007-10-29&AWSAccessKeyId=%s&Operation=ListLookup&ListType=WishList&ListId=%s" % (self.amazonAccessKeyId, self.listID)
        import urllib
        f = urllib.urlopen(aws_url)
        dom = minidom.parse(f)
        self.title = getText(dom.getElementsByTagName('ListName')[0].childNodes)
        self.listLength = int(getText(dom.getElementsByTagName('TotalItems')[0].childNodes))
        self.TotalPages = int(getText(dom.getElementsByTagName('TotalPages')[0].childNodes))
        return(self.title, self.listLength, self.TotalPages)

    def ListItems(self):
        """
        a generator for the items on the Amazon list
        """

        import itertools
        for pageNum in xrange(1,self.TotalPages):  
            aws_url = "http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&Version=2007-10-29&AWSAccessKeyId=%s&Operation=ListLookup&ListType=WishList&ListId=%s&ResponseGroup=ListItems,Medium&ProductPage=%s" % (self.amazonAccessKeyId,self.listID,pageNum)
            import urllib
            f = urllib.urlopen(aws_url)
            dom = minidom.parse(f)
            f.close()
            items = dom.getElementsByTagName('ListItem')
            for c in xrange(0,10):
                yield items[c]
                
    def parseListItem(self,item):
        from string import join
        from decimal import Decimal

        itemDict = {}    
        
        itemDict['asin'] = getText(item.getElementsByTagName('ASIN')[0].childNodes)
        itemDict['dateadded'] = getText(item.getElementsByTagName('DateAdded')[0].childNodes)
        itemDict['detailpageurl'] = getText(item.getElementsByTagName('DetailPageURL')[0].childNodes)

        # join the text of all the author nodes, if they exist
        authorNodes = item.getElementsByTagName('Author')
        # blank not allowed
        itemDict['author'] = join(map(lambda e: getText(e.childNodes), authorNodes), ", ") or ' '

        itemDict['quantitydesired'] = getText(item.getElementsByTagName('QuantityDesired')[0].childNodes)
        
        titleNodes = item.getElementsByTagName('Title')
        # blank title not allowed
        itemDict['title'] = join(map(lambda e: getText(e.childNodes), titleNodes), ", ") or ' '
        
        # to fix -- not all things have a LowestNewPrice        
        itemDict['price'] = str(Decimal(getText(item.getElementsByTagName('LowestNewPrice')[0].getElementsByTagName('Amount')[0].childNodes))/100) or ' '

        return itemDict    


def main():
    
    gs = GSheetForAmazonList(user=GoogleUser,pwd=GooglePW)
    gs.setKey(GSheet_KEY)
    gs.setWkshtId(GWrkSh_ID)

    aWishList = amazonWishList(listID=AMAZON_LIST_ID,amazonAccessKeyId=AMAZON_ACCESS_KEY_ID)
    items = aWishList.ListItems()
    print "deleting all rows..."
    gs.deleteAllRows()
    for item in items:
        try:
            h = aWishList.parseListItem(item)
            print h['asin']
        except Exception, e:
            print "Error %s parsing %s" % (e, item.toprettyxml("  "))
        try:
            gs.insertRow(h)
        except Exception, e:
            print "Error %s inserting %s" % (e, h['asin'])


if __name__ == '__main__':
    main()