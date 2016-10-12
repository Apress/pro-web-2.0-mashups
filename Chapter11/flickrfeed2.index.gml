<gm:page title="Flickr Photos on Google Maps" authenticate="false">
 
<!--
Displaying Flickr Thumbnails on a Google Map (hardwired parameters)
@author: Raymond Yee
-->

  <h1>Flickr Photos</h1>
    
  <gm:list id="flickrList" template="flickrTemplate" data="http://pipes.yahoo.com/pipes/pipe.run?_id=YG9eZGWO3BGukZGJTqoASA&_render=rss&api_key=e81ef8102a5160154ef4662adcc9046b&extras=geo&lat0=37.817785166068&lat1=37.926190569376&lon0=-122.34375&lon1=-122.17208862305&min_upload_date=820483200&per_page=100"
pagesize="10">
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

</gm:page>
