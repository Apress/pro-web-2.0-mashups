<gm:page title="Flickr Photos on Google Maps" authenticate="false">
 
<!--
Displaying Flickr Photos on a Google Map
@author: Raymond Yee
-->

<h1>Flickr Photos</h1>
 
  <gm:list id="flickrList" template="flickrTemplate" data="http://api.flickr.com/services/feeds/geo/?g=34427469792@N01&lang=en-us&format=rss_200"
pagesize="10"/>

  <gm:template id="flickrTemplate">
    <table class="blue-theme" style="width:50%">
      <tr repeat="true">
        <td style="padding-bottom:10px">
          <b><gm:text ref="atom:title"/></b>
          <br/>
          <gm:html ref="atom:summary"/>
          <br/>
          <span style="color:#3366cc">
            location: (<gm:text ref="geo:Point/geo:lat"/>, <gm:text ref="geo:Point/geo:long"/>)
          </span>
        </td>
      </tr>
    </table>
  </gm:template>

</gm:page>

