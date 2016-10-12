<?php

require_once 'OpenDocumentPHP/OpenDocumentText.php';

$fullpath = 'D:\Document\PersonalInfoRemixBook\examples\ch17\odp_gen_example.odt';

/*
 * If file exists, remove it first.
 */
if (file_exists($fullpath)) {
    unlink($fullpath);
}

$text = new OpenDocumentText($fullpath);

# set some styles

/**

<style:style style:name="Standard" style:family="paragraph" style:class="text"/>
<style:style style:name="Text_20_body" style:display-name="Text body" style:family="paragraph"
 style:parent-style-name="Standard" style:class="text">
 <style:paragraph-properties fo:margin-top="0in" fo:margin-bottom="0.0835in"/>
</style:style>

**/

$Standard_Style = $text->getStyles()->getStyles()->getStyle();
$Standard_Style->setStyleName('Standard');
$Standard_Style->setFamily('paragraph');
$Standard_Style->setClass('text');

$textBody_Style = $text->getStyles()->getStyles()->getStyle();
$textBody_Style->setStyleName('Text_20_body');
$textBody_Style->setDisplayName('Text body');
$textBody_Style->setFamily('paragraph');
$textBody_Style->setClass('text');

$pp = $textBody_Style->getParagraphProperties();
$pp->setMarginTop('0in');
$pp->setMarginBottom('0.0835in');

# write the headers and paragraphs

$textbody = $text->getBody()->getTextFragment();

$heading = $textbody->nextHeading();
$heading->setHeadingLevel(1);
$heading->append('Purpose (Heading 1)');

$paragraph = $textbody->nextParagraph();
$paragraph->setStyleName('Text_20_body');
$paragraph->append('The following sections illustrate various possibilities in ODF Text');

$heading = $textbody->nextHeading();
$heading->setHeadingLevel(2);
$heading->append('A simple series of paragraphs (Heading 2)');

$paragraph = $textbody->nextParagraph();
$paragraph->setStyleName('Text_20_body');
$paragraph->append('This section contains a series of paragraphs.');
$paragraph = $textbody->nextParagraph();
$paragraph->setStyleName('Text_20_body');
$paragraph->append('This is a second paragraph.');
$paragraph = $textbody->nextParagraph();
$paragraph->setStyleName('Text_20_body');
$paragraph->append('And a third paragraph.');

$text->close();

?> 