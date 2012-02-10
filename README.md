Postmark-PHP
============

My take on a PHP class for sending mails with [Postmarkapp.com](http://www.postmarkapp.com)

The master branch will always contain the latest and most stable release. - I will try not to break anything in here.

Usage
-----
<pre>
$htmlBody = file_get_contents('http://www.google.com');
$textBody = 'Here the whole of google would have been, if you did HTML!';

$mail = new Postmark('MY_API_KEY');
$mail->setFrom('Me &lt;me@example.com&gt;');
$mail->addTo('you@example.com');
$mail->addTo('yourfriend@example.com', 'cc');
$mail->addTo('yoursecretlover@example.com', 'bcc');
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
