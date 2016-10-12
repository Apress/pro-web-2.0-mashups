<gm:page title="Tabs1" authenticate="true">
  <!--
  Load the feeds in one tab and allow to copy selected entries to a data source in another tab
  -->
  
  <gm:tabs target="myContainer"/>  
  
  <gm:container id="myContainer" style="padding:3px;border:1px solid #369;">
    
    <gm:section id="sectionFlickrSearch" title="Search">      
      <gm:list id="flickrList" template="flickrTemplate" data="http://pipes.yahoo.com/pipes/pipe.run?_id=YG9eZGWO3BGukZGJTqoASA&_render=rss&api_key=e81ef8102a5160154ef4662adcc9046b&extras=geo&lat0=37.817785166068&lat1=37.926190569376&lon0=-122.34375&lon1=-122.17208862305&min_upload_date=820483200&per_page=100"
pagesize="10" />
      <input type="button" value="Copy Selected" onclick="copy_selected()" />
    </gm:section>
    
    <gm:section id="sectionSavedEntries" title="Saved Results">
      <gm:list id="savedEntries" data="${user}/crud" template="savedEntryTemplate" />
      <input type="button" value="Delete Selected" onclick="delete_selected()" />
    </gm:section>
    
  </gm:container>
  
  <!-- flickrTemplate -->

  <gm:template id="flickrTemplate" class="blue-theme">
    <div style="float:left; width:85px" repeat="true">
      <gm:html ref="atom:summary"/>
    </div>
    <br style="clear:both"/>
    <gm:pager/>
  </gm:template>
  
  <!-- savedEntryTemplate -->
  
  <gm:template id="savedEntryTemplate">
     <div>Your saved entries</div>
     <div style="float:left; width:85px" repeat="true">
      <gm:html ref="atom:summary"/>
     </div>
     <br style="clear:both"/>
     <gm:pager/>
  </gm:template>  
  
  <script type="text/javascript">
  //<![CDATA[

  // figure what is the currently selected entry and copy that over
  function copy_selected() {
  
    var flickrList = google.mashups.getObjectById('flickrList');
    var entry = flickrList.getSelectedEntry();
    if (entry) {
      var savedEntries = google.mashups.getObjectById('savedEntries');    
      savedEntries.getData().addEntry(entry);
    }
  
  } // copy_selected
    
  function delete_selected() {
  
    var savedEntries = google.mashups.getObjectById('savedEntries');    
    var entry = savedEntries.getSelectedEntry();
    if (entry) {
      savedEntries.getData().removeEntry(entry);
    }
  
  } // delete_selected
    
   
  //]]>
  </script>
  
  
</gm:page>

