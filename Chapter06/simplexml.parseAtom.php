<?php
// An example to show how to parse an Atom feed (with multiple namespaces) with SimpleXML
# create the XML document in the $feed string
$feed=<<<EOT
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<title>Apress :: The Expert's Voice</title>
	<subtitle>Welcome to Apress.com. Books for Professionals, by Professionals(TM)...with what the
		professional needs to know(TM)</subtitle>
	<link rel="alternate" type="text/html" href="http://www.apress.com/"/>
	<link rel="self" href="http://examples.mashupguide.net/ch06/Apress.Atom.with.DC.xml"/>
	<updated>2007-07-25T12:57:02Z</updated>
	<author>
		<name>Apress, Inc.</name>
		<email>support@apress.com</email>
	</author>
	<id>http://apress.com/</id>
	<entry>
		<title>Excel 2007: Beyond the Manual</title>
		<link href="http://www.apress.com/book/bookDisplay.html?bID=10232"/>
		<id>http://www.apress.com/book/bookDisplay.html?bID=10232</id>
		<updated>2007-07-25T12:57:02Z</updated>
		<dc:date>2007-03</dc:date>
		<summary type="html"
			>&lt;p&gt;&lt;i&gt;Excel 2007: Beyond the Manual&lt;/i&gt; will introduce those who are already familiar with Excel basics to more advanced features, like consolidation, what-if analysis, PivotTables, sorting and filtering, and some commonly used functions. You'll learn how to maximize your efficiency at producing professional-looking spreadsheets and charts and become competent at analyzing data using a variety of tools. The book includes practical examples to illustrate advanced features.&lt;/p&gt;</summary>
	</entry>
	<entry>
		<title>Word 2007: Beyond the Manual</title>
		<link href="http://www.apress.com/book/bookDisplay.html?bID=10249"/>
		<id>http://www.apress.com/book/bookDisplay.html?bID=10249</id>
		<updated>2007-07-25T12:57:10Z</updated>
		<dc:date>2007-03-01</dc:date>
		<summary type="html"
			>&lt;p&gt;&lt;i&gt;Word 2007: Beyond the Manual&lt;/i&gt; focuses on new features of Word 2007 as well as older features that were once less accessible than they are now. This book also makes a point to include examples of practical applications for all the new features. The book assumes familiarity with Word 2003 or earlier versions, so you can focus on becoming a confident 2007 user.&lt;/p&gt;</summary>
	</entry>
</feed>
EOT;

# instantiate a simpleXML object based on the $feed XML
$xml = simplexml_load_string($feed);

# access the title and subtitle elements
print "title: {$xml->title}\n";
print "subtitle: {$xml->subtitle}\n";

# loop through the two link elements, printing all the attributes for each link.

print "processing links\n";
foreach ($xml->link as $link) {
	print "attribute:\t";
	foreach ($link->attributes() as $a => $b) {
		print "{$a}=>{$b}\t";
	}
	print "\n";
}
print "author: {$xml->author->name}\n";

# let's check out the namespace situation

$ns_array = $xml->getDocNamespaces(true);

# display the namespaces that are in the document
print "namespaces in the document\n";
foreach ($ns_array as $ns_prefix=>$ns_uri) {
	print "namespace: ${ns_prefix}->${ns_uri}\n";
}
print "\n";

# loop over all the entry elements
foreach ($xml->entry as $entry) {
	print "entry has the following elements in the global namespace: \t";
	
	// won't be able to access tags that aren't in the global namespace.
	foreach ($entry->children() as $child) {
		print $child->getName(). " ";
	}
	print "\n";
	print "entry title: {$entry->title}\t link: {$entry->link["href"]}\n";
	
	// show how to use xpath to get date
	// note dc is registered already to $xml.
	$date = $entry->xpath("./dc:date");
	print "date (via XPath): {$date[0]}\n";
	
	// use children() to get at date
	$date1 = $entry->children("http://purl.org/dc/elements/1.1/");
	print "date (from children()): {$date[0]}\n";
	
	}
	
# add <category term="books" /> to feed -- adding the element will work but the tag is in the
# wrong place to make a valid Atom feed.
# It is supposed to go before the entry elements
$category = $xml->addChild("category");
$category->addAttribute('term','books');

# output the XML to show that category has been added.
$newxmlstring = $xml->asXML();
print "new xml (with category tag): \n$newxmlstring\n";
	

?> 