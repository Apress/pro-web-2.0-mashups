<?php
require_once 'OpenDocumentPHP/OpenDocumentText.php';
$text = new OpenDocumentText('D:\Document\PersonalInfoRemixBook\examples\ch17\helloworld_opendocumentphp.odt');
$textbody = $text->getBody();
$paragraph = $textbody->nextParagraph();
$paragraph->append('Hello World!');
$text->close();
?>