from amazonListToGSheet import GS_HEADER, amazonWishList, AMAZON_LIST_ID, AMAZON_ACCESS_KEY_ID, GS_KEYS 
from win32com.client import Dispatch

# fire up the Excel application
xlApp = Dispatch("Excel.Application")
xlApp.Visible = 1
xlApp.Workbooks.Add()

# write the headers
col = 1

def insertRow(sheet,row,data,keys):
    col = 1
    for k in keys:
        sheet.Cells(row,col).Value = data[k]
        col += 1

for h in GS_HEADER:
    xlApp.ActiveSheet.Cells(1,col).Value = h
    col +=1
# now loop through the amazon wishlist

aWishList = amazonWishList(listID=AMAZON_LIST_ID,amazonAccessKeyId=AMAZON_ACCESS_KEY_ID)
items = aWishList.ListItems()

row = 2
for item in items:
    try:
        p = aWishList.parseListItem(item)
        print p['asin']
    except Exception, e:
        print "Error %s parsing %s" % (e, item.toprettyxml("  "))
    try:
        insertRow(xlApp.ActiveSheet,row,p,GS_KEYS)
        row += 1
    except Exception, e:
        print "Error %s inserting %s" % (e, p['asin'])
