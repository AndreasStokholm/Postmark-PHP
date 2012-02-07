<?php
/**
* File for Postmark class
*
* @author	Andreas Stokholm
*
* See license in bottom of file.
*/

/**
* Class for handling interaction with Postmarkapp.com
*
* @author	Andreas Stokholm
* @version	v0.0.1
*/

class Postmark {

	private $key;
	private $mail = array(
		'From' 			=> '',
		'To'			=> '',
		'Cc'			=> '',
		'Bcc'			=> '',
		'Subject'		=> '',
		'Tag'			=> '',
		'HtmlBody'		=> '',
		'TextBody'		=> '',
		'ReplyTo'		=> '',
		'Headers'		=> array(),
		'Attachments'	=> array()
	);
	private $test_file = 'testmail.html'; // Default: 'testmail.html'


	/**
	 * Constructor
	 *
	 * @param	$postmark_key	string
	 *
	 * @author	Andreas Stokholm
	 * @since	v0.0.1
	 */
	public function __construct($postmark_key) {
		$this->key = $postmark_key;
	}


	/**
	 * Set sender mail
	 *
	 * This mail should be the one you set up in the Postmark interface.
	 *
	 * @param	$from	string
	 *
	 * @author	Andreas Stokholm
	 * @since	v0.0.1
	 */
	public function setFrom($from) {
		$this->mail['From'] = $from;
		if ($this->mail['ReplyTo'] == '') {
			$this->mail['ReplyTo'] = $from;
		}
	}


	/**
	 * Add delivery address
	 *
	 * Call this method as many times as you want to add recipients.
	 * Bear in mind that Postmark restricts mails to 20 recipients total.
	 *
	 * This method takes any kind of recipient form. It defaults to the "To" field,
	 * but can also add to "Cc" and "Bcc".
	 *
	 * Usage: 	$this->addTo('andreas@stokholm.me');
	 * Or:		$this->addTo('andreas@stokholm.me', 'bcc');
	 *
	 * @param	$to		string
	 * @param	$type	string
	 *
	 * @author	Andreas Stokholm
	 * @since 	v0.0.1
	 */
	public function addTo($email, $type = 'to') {

		$kind = 'To';
		if ($type == 'bcc') {
			$kind = 'Bcc';
		} elseif ($type == 'cc') {
			$kind = 'Cc';
		}

		if ($this->mail[$kind] == '') {
			$this->mail[$kind] = $email;
		} else {
			$this->mail[$kind] .= ', '.$email;
		}
	}


	/**
	 * Set subject
	 *
	 * Sets the subject of the mail
	 *
	 * @param	$subject	string
	 * 
	 * @author	Andreas Stokholm
	 * @since	v0.0.1
	 */
	public function setSubject($subject) {
		$this->mail['Subject'] = $subject;
	}


	/**
	 * Set tag of mail
	 *
	 * @param	$tag	string
	 *
	 * @author	Andreas Stokholm
	 * @since	v0.0.1
	 */
	public function setTag($tag) {
		$this->mail['Tag'] = $tag;
	}


	/**
	 * Sets body of mail
	 *
	 * @param	$html	string
	 * @param	$text	string
	 *
	 * @author	Andreas Stokholm
	 * @since	v0.0.1
	 */
	public function setBody($html = '', $text = '') {
		if ($html != '') {
			$this->mail['HtmlBody'] = utf8_encode($html);
		}

		if ($text != '') {
			$this->mail['TextBody'] = utf8_encode($text);
		}
	}


	/**
	 * Add header
	 *
	 * Adds a header to the mail. Call this method as many times as
	 * you have headers to add.
	 *
	 * @param	$header		string
	 * @param	$value		string
	 *
	 * @author	Andreas Stokholm
	 * @since	v0.0.1
	 */
	public function addHeader($header, $value) {
		$this->mail['Headers'][] = array('Name' => $header, 'Value' => $value);
	}


	/**
	 * Attach a file to the mail
	 *
	 * This method can attach as many files as you want to the message.
	 * Keep in mind that Postmark restricts attachments to 10 MB total.
	 * If you attach 5 files, the total size can't be more than 10 MB.
	 *
	 * Make sure the $file_path is readable by PHP. Also, this can be
	 * a big request. You should only use this method on mails sent via
	 * background jobs.
	 *
	 * @param	$file_name	string
	 * @param	$file_path	string
	 *
	 * @return 	boolean
	 *
	 * @author	Andreas Stokholm
	 * @since 	v0.0.1
	 */
	public function attachFile($file_name, $file_path) {

		if ($file_contents = file_get_contents($file_path)) {
			if ($attachment = base64_encode($file_contents)) {
				$this->mail['Attachments'][] = array(
					'Name' 			=> $file_name,
					'Content' 		=> $attachment,
					'ContentType'	=> 'application/octet-stream' // Always use octet-stream
				);

				return true;
			} 

			return false;
		}

		return false;
	}


	/**
	 * Send email using Postmark
	 *
	 * This method actually sends the email. If the parameter $test is
	 * set to true, it will write to a file instead of sending with
	 * Postmark. It will still call Postmark, to verify the JSON.
	 *
	 * @param	$test	boolean
	 *
	 * @return 	JSON string
	 *
	 * @author	Andreas Stokholm
	 * @since	v0.0.1
	 */
	public function send($test = false) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.postmarkapp.com/email');

		if ($test) {
			$api_token = 'X-Postmark-Server-Token: POSTMARK_API_TEST';
		} else {
			$api_token = 'X-Postmark-Server-Token: '.$this->key;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', $api_token));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->mail));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);
		curl_close($ch);

		if ($test) {
			if ($test_output = fopen($test_file, 'w')) {
				if (fwrite($test_output, $this->mail['HtmlBody'])) {
					return json_encode(array('Failed to write to test file. Is file writable?'));
				}
				fclose($test_output);				
			} else {
				return json_encode(array('Failed to open test file. Is directory writable?'));
			}

		}

		return $result;		

	}
}

/**
 * Copyright (c) 2012 Andreas Stokholm
 *
 * Permission is hereby granted, free of charge, to any person obtaining 
 * a copy of this software and associated documentation files 
 * (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS 
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */
?>