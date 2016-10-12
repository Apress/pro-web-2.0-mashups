// ==UserScript==
// @name           New York Times Permlinker
// @namespace      http://mashupguide.net
// @description    Adds a link to a "permalink" or  "weblog-safe" URL for the NY Times article, if such a link exists
// @include        http://*.nytimes.com/*
// ==/UserScript==

function rd(){

  // the following code is based on the bookmarklet written by Aaron Swartz at http://nytimes.blogspace.com/genlink
  
  var x,t,i,j;
  // change %3A -> : and %2F -> '/'
  t=location.href.replace(/[%]3A/ig,':').replace(/[%]2f/ig,'/');
  
  // get last occurence of "http://"
  i=t.lastIndexOf('http://');
  
  // lop off stuff after '&'
  if(i>0){
    t=t.substring(i);
    j=t.indexOf('&');   
    if(j>0)t=t.substring(0,j)
  }

  var url = 'http://nytimes.blogspace.com/genlink?q='+t;
  
  // send the NY Times link to the nytimes.blogspace.com service. If there is a permalink, then the href attribute of the first a tag will start with 'http:' and not 'genlink'.  
  // if there is a permalink, then insert a new li element at the end of the <ul id="toolsList">.
  
  GM_xmlhttpRequest({
  method:"GET",
  url:url,
  headers:{
    "User-Agent":"monkeyagent",
    "Accept":"text/html",
  },
  onload:function(details) {
    var s = details.responseText;
    var p = /a href="(.*)"/;
    var plink = s.match(p)[1];
    if ( plink.match(/^http:/) && (tl = document.getElementById('toolsList')) )  { 
      plink = plink + "&pagewanted=all";
      plinkItem = document.createElement('li');
      plinkItem.innerHTML = '<a href="' + plink + '">PermaLink</a>';
      tl.appendChild(plinkItem);
    }     
  }
});
  
}

rd();
