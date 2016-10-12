#!/Library/Frameworks/Python.framework/Versions/Current/bin/pythonw

from appscript import *
ge = app("Google Earth")
#h = ge.GetViewInfo()
h = {k.latitude: 40.748434, k.distance: 10000.0, k.azimuth: 0, k.longitude: -73.984791, k.tilt: 0.0}
ge.SetViewInfo(h,speed=0.5)
