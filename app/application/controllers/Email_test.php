<?php
/**
 * The main page of the application. Handles viewing of applications.	
 */
class Email_test extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('Mailer');
	}

	public function index() {
		echo 'This is a test bed for emails';
	}

	public function sendTestEmail() {
	echo $this->mailer->sendEmail('jshill@uw.edu', 
			'Testing Server', 
			'<p>Testing, Testing, 123.</p>');

}

}
?>