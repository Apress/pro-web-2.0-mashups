# demonstrate the Google Earth COM interface
# 
import win32com.client
ge =  win32com.client.Dispatch("GoogleEarth.ApplicationGE")

fn = r'D:/Document/PersonalInfoRemixBook/examples/ch13/berkeley.campanile.evans.kml'
ge.OpenKmlFile(fn,True)

features = ['Campanile', 'Evans', 'Evans_Roll']

for feature in features:
    p = ge.GetFeatureByHref(fn + "#" + feature)
    p.Highlight()
    ge.SetFeatureView(p,0.1)
    raw_input('hit to continue') 
