Postmark-PHP
============

My take on a PHP class for sending mails with Postmarkapp.com

Usage
-----
<pre>
$htmlBody = file_get_contents('http://www.google.com');
$textBody = 'Here the whole of google would have been if you did HTML!';

$mail = new Class_Postmark('MY_API_KEY');
$mail->setFrom('Me <me@example.com>');
$mail->addTo('you@example.com');
$mail->setSubject('All of google, in your inbox!');
$mail->setTag('Example');
$mail->setBody($htmlBody, $textBody);

$result = $mail->send();
</pre>

Wanna attach a file? - Easy!

Just call this nifty little thing:

<pre>
$mail->attachFile('AwesomeFile.txt', '/path/to/my/file.txt');
</pre>

Just remember Postmark limits you to 10 MB of attachments total. - No matter the number of files.