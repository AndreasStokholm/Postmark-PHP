<?php
	
	require_once dirname(dirname(__FILE__)).'/Postmark.php';

	class MailSender extends PHPUnit_Framework_TestCase {
	
		public function testInit() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertInstanceOf('Postmark', $mail);
		}
		
		public function testSetFrom() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertNull($mail->setFrom('test@test.tld'));
		}
		
		public function testAddTo() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertNull($mail->addTo('test@test.tld'));
			$this->assertNull($mail->addTo('test@test.tld', 'cc'));
			$this->assertNull($mail->addTo('test@test.tld', 'bcc'));
		}
		
		public function testSetSubject() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertNull($mail->setSubject('Some test subject'));
		}
		
		public function testSetTag() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertNull($mail->setTag('testTag'));
		}
	
		public function testSetBody() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertNull($mail->setBody('<html><body><title>content</title></body></html>', 'Plain text content'));
		}
		
		public function testAddHeader() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertNull($mail->addHeader('Header name', 'header content'));
		}
		
		public function testAttachFile() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertTrue($mail->attachFile('Readme.txt', dirname(dirname(__FILE__)).'/testAttachment.txt'));
		}
		
		public function testSend() {
			$mail = new Postmark('POSTMARK_API_TEST');
			$this->assertNull($mail->setFrom('test@postmark-php-test.io'));
			$this->assertNull($mail->addTo('test@postmark-php-test.io'));
			$this->assertNull($mail->setSubject('PHPUnit test'));
			$this->assertNull($mail->setTag('Automated test by PHPUnit'));
			$this->assertNull($mail->setBody('<html><body><title>content</title></body></html>', 'Plain text content'));
			$this->assertNull($mail->addHeader('Test-header', 'This is a test'));
			$this->assertTrue($mail->attachFile('Readme.txt', dirname(dirname(__FILE__)).'/testAttachment.txt'));
			$response = json_decode($mail->send(true));
			$this->assertEquals(0, $response->ErrorCode);
		}

	}
