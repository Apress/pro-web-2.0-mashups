// based on http://www.kaply.com/weblog/wp-content/uploads/2007/07/dominos.js

var geocoder_us = {
  description: "Geocode with geocoder_us",
  shortDescription: "geocoder_us",
  scope: {
    semantic: {
      "adr" : "adr"
    }
  },
  doAction: function(semanticObject, semanticObjectType) {
    var url;
    if (semanticObjectType == "adr") {
      var adr = semanticObject;
      url = "http://geocoder.us/demo.cgi?address=";
      if (adr["street-address"]) {
        url += adr["street-address"].join(", ");
        url += ", ";
      }
      if (adr.locality) {
        url += adr.locality;
        url += ", ";
      }      
      if (adr.region) {
        url += adr.region;
        url += ", ";
      }
      if (adr["postal-code"]) {
        url += adr["postal-code"];
      }
    }
    
    return url;
  }
};

SemanticActions.add("geocoder_us", geocoder_us);
