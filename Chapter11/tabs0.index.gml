<gm:page title="Tabs0" authenticate="true">
  <!--
  Load the feeds in one tab and allow to copy selected entries to a data source in another tab
  -->
  
  <gm:tabs target="myContainer"/>
  
  <gm:container id="myContainer" style="padding:3px;border:1px solid #369;width:600px;">

    <gm:section id="sectionFlickrSearch" title="Search">
       <gm:list id="myList" template="flickrTemplate"
         data="http://pipes.yahoo.com/pipes/pipe.run?_id=YG9eZGWO3BGukZGJTqoASA&_render=rss&extras=geo&lat0=37.817785166068&lat1=37.926190569376&lon0=-122.34375&lon1=-122.17208862305&min_upload_date=820483200&per_page=10" pagesize="10"/>
    </gm:section>
    
    <gm:section id="sectionSavedEntries" title="Saved Results">
      <gm:list id="savedEntries" data="${user}/crud" template="savedEntryTemplate" />
    </gm:section>
    
  </gm:container>
  
   <gm:template id="flickrTemplate">
     <table class="blue-theme" style="width:50%">
        <tr repeat="true">
          <td style="padding-bottom:10px">
            <b><gm:text ref="atom:title"/></b>
            <br/>
            <gm:html ref="atom:summary"/>
            <br/>
            <span style="color:#3366cc">
               location: (<gm:text ref="geo:lat"/>, <gm:text ref="geo:long"/>)
            </span>
            <br/>
            <input type="button" value="Copy" onclick="copy_this(this)" />
          </td>
        </tr>
    </table>
  </gm:template>
  
   <gm:template id="savedEntryTemplate">
     <div>Your saved entries</div>
     <table class="blue-theme" style="width:50%">
        <tr repeat="true">
          <td style="padding-bottom:10px">
            <b><gm:text ref="atom:title"/></b>
            <br/>
            <gm:html ref="atom:summary"/>
            <br/>
            <span style="color:#3366cc">
               location: (<gm:text ref="geo:lat"/>, <gm:text ref="geo:long"/>)
            </span>
            <gm:editButtons deleteonly="true" />
          </td>
        </tr>
    </table>
  </gm:template>  
  
  <script type="text/javascript">
  //<![CDATA[
 
  function copy_this(DOMElement) {
    var entry = google.mashups.getEntryForElement(DOMElement);
    var myList = google.mashups.getObjectById('myList');
    var savedEntries = google.mashups.getObjectById('savedEntries');    
    savedEntries.getData().addEntry(entry);  
  }
   
  //]]>
  </script>
  
  
</gm:page>
  

