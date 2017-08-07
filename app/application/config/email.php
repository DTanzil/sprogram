<?php
/**
 * User-defined settings for email config
 * If you're working on localhost, uses XAMPPs sendmail executable
 * If you're on the production Unix server, uses the default settings
 */

if(ENVIRONMENT === 'production') {

} else {
	$config['mailpath'] = "C:/xampp/sendmail";
	$config['protocol'] = 'smtp';
	$config['smtp_host'] = 'smtp.uw.edu';
	//$config['smtp_user'] = 'jon.shilling96@gmail.com';
	//$config['smtp_pass'] = 'cprogzysbdwdzhds';
	$config['smtp_user'] = 'hubtech';
	$config['smtp_pass'] = 'F8Y2i2Eozx';
	$config['smtp_port'] = 587;
	$config['smtp_crypto'] = 'tls';
	$config['mailtype'] = 'html';
	    $config['charset'] = 'utf-8';
	    $config['wordwrap'] = TRUE;
	    $config['newline'] = "\r\n";
	// $config['charset'] = 'iso-8859-1';
	// $config['wordwrap'] = TRUE;
}


?>