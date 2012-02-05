Postmark-PHP
============

My take on a PHP class for sending mails with Postmarkapp.com

Usage
-----

$htmlBody = file_get_contents('http://www.google.com');
$textBody = 'Here the whole of google would have been if you did HTML!';

$mail = new Class_Postmark('MY_API_KEY');
$mail->setFrom('Me <me@example.com>');
$mail->addTo('you@example.com');
$mail->setSubject('All of google, in your inbox!');
$mail->setTag('Example');
$mail->setBody($htmlBody, $textBody);

echo "<pre>";
print_r($mail->send());
echo "</pre>";