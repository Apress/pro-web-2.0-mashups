<gm:page title="Squaring Input and Flickr feed" authenticate="false">
<!--
  Introducing custom JavaScript into a GME mashup
-->
  
    <form action="#" onsubmit="calc_square(); return false;">
      <label>Input a number:</label><input type="text" size="5" name="num" value="4" />
      <input type="submit" value="Square it!" />
    </form>
    
    <p>The square of the input is: <span id="answer">16</span></p>
    
  <gm:list id="flickrList" template="flickrTemplate" data="http://pipes.yahoo.com/pipes/pipe.run?_id=YG9eZGWO3BGukZGJTqoASA&_render=rss&api_key=e81ef8102a5160154ef4662adcc9046b&extras=geo&lat0=37.817785166068&lat1=37.926190569376&lon0=-122.34375&lon1=-122.17208862305&min_upload_date=820483200&per_page=100"
pagesize="10" />
     
<!-- flickrTemplate -->

<gm:template id="flickrTemplate" class="blue-theme">
   <div>
    <span repeat="true" style="padding: 5px">
      <gm:html ref="atom:summary"/>
    </span>
    <gm:pager/>
  </div>
</gm:template>
  
    <script type="text/javascript">
    //<![CDATA[
     function calc_square() {
         var n = document.forms[0].num.value;
         document.getElementById('answer').innerHTML = n*n;
      }

      document.forms[0].num.onchange = calc_square;  //register an event
      
    //]]>
    </script>    
</gm:page>
