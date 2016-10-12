<gm:page title="Flickr Photos on Google Maps" authenticate="false" onload="init_data();">
 
<!--
Displaying Flickr Thumbnails on a Google Map 
@author: Raymond Yee
-->   

  <h1>Flickr Photos</h1>
  
  <form action="#" onsubmit="update_feed(); return false;">
    <label>Input tags:</label><input type="text" size="30" name="tags" value="" />
    <input type="submit" value="Update feed" />
  </form>
  
  <p>URL of current feed: <span id="current_tags">.</span></p>          

  <gm:list id="flickrList" template="flickrTemplate" pagesize="10">
    <gm:handleEvent event="select" src="flickrMap"/>
  </gm:list>
  
  <gm:map id="flickrMap" style="border:solid black 1px" control="large"
          maptypes="true" data="${flickrList}" latref="geo:lat" lngref="geo:long"
            infotemplate="FlickrMapDetailsTemplate" height="600">
      <gm:handleEvent event="select" src="flickrList"/>
  </gm:map>  
        
  <!-- flickrTemplate -->

  <gm:template id="flickrTemplate" class="blue-theme">
    <div style="float:left; width:85px" repeat="true">
      <gm:html ref="atom:summary"/>
    </div>
    <br style="clear:both"/>
    <gm:pager/>
  </gm:template>


  <!-- FlickrMapDetailsTemplate -->

  <gm:template id="FlickrMapDetailsTemplate">
    <div >
      <b><gm:link ref="atom:link[@rel='alternate']/@href" labelref="atom:title" /></b>
      <br/>
      <gm:html ref="atom:summary"/>
      <br/>
      Lat: <gm:text ref="geo:lat"/><br/>
      Long: <gm:text ref="geo:long"/>
    </div>
  </gm:template>
  
  <script type="text/javascript">
    //<![CDATA[
     function update_feed() {
         var tags = document.forms[0].tags.value;
 
      // let's get the bounds of the map
         var flickrMap = google.mashups.getObjectById('flickrMap');
         
         var bounds = flickrMap.getBounds();
         var lat0 = bounds.getSouthWest().lat();
         var lon0 = bounds.getSouthWest().lng();
         var lat1 = bounds.getNorthEast().lat();
         var lon1 = bounds.getNorthEast().lng();

         update_feed0 (tags,lat0,lon0,lat1,lon1);


      } // update_feed
      
  function update_feed0(tags,lat0,lon0,lat1,lon1) {

      var flickrList = google.mashups.getObjectById('flickrList');
      var flickrMap = google.mashups.getObjectById('flickrMap');

      var url =
          'http://pipes.yahoo.com/pipes/pipe.run?_id=YG9eZGWO3BGukZGJTqoASA&_render=rss&api_key=e81ef8102a5160154ef4662adcc9046b&extras=geo&min_upload_date=820483200&per_page=100'
          + '&tags=' + escape (tags) + "&lat0=" + lat0 + "&lon0=" + lon0 + "&lat1=" + lat1 + "&lon1=" + lon1;

      // clear the old overlays (I'm doing this to get rid of the boundary
      
      flickrMap.getMap().clearOverlays();
      
      document.getElementById('current_tags').innerHTML = "<a href='" + url + "'>Feed Link</a>";
      
      flickrList.setData(url);
      flickrList.setPage(0);  // reset the pager
      
      // now draw a bounding box
      
      border = new GPolygon([
      new GLatLng(lat0, lon0),
      new GLatLng(lat1, lon0),
      new GLatLng(lat1, lon1),
      new GLatLng(lat0,lon1),
      new GLatLng(lat0,lon0)
      ], "#ff0000", 2);
      
      flickrMap.getMap().addOverlay(border);

  } // update_feed0


    function init_data() {
    
        var lat0=37.817785166068;
        var lat1=37.926190569376
        var lon0=-122.34375;
        var lon1=-122.17208862305;
        
        update_feed0("",lat0,lon0,lat1,lon1);
    
    
    } // init_data
      
    //]]>
  </script>    

</gm:page>

