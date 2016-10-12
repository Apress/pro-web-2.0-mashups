# demonstrate the Google Earth COM interface
# 
import win32com.client
ge =  win32com.client.Dispatch("GoogleEarth.ApplicationGE")

# send to UC Berkeley Campanile
lat = 37.8721
long = -122.257704

#altitude in meters
altitude = 0

# http://earth.google.com/comapi/earth_8idl.html#5513db866b1fce4e039f09957f57f8b7
# AltitudeModeGE { RelativeToGroundAltitudeGE = 1, AbsoluteAltitudeGE = 2 }
altitudeMode = 1  

#range in meters; tilt in degrees, heading in degres
range = 200
tilt = 45
heading = 0 # aka azimuth

#set how fast to send Google Earth to the view
speed = 0.1

ge.SetCameraParams(lat,long,altitude,altitudeMode,range,tilt,heading,speed)
