<?php
require_once 'Postmark.php';
$postmark = new Postmark('b343f43b-eed3-4722-ad8b-9187dfd6d68a');

$htmlBody = file_get_contents('http://www.google.com');
$textBody = 'Here the whole of google would have been, if you did HTML!';

$postmark->setFrom('GoGemba <robot@gogemba.com>');
$postmark->addTo('asn@asn24.dk');
$postmark->setSubject('All of google, in your inbox!');
$postmark->setTag('Example');
$postmark->setBody($htmlBody, $textBody);

$result = $postmark->send();

echo "<pre>";
print_r($result);
echo "</pre>";	
?>