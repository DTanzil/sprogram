<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	/**
	

	*/	
	$CI =& get_instance();
	$CI->load->library('authorize');
	$CI->load->helper('url');

	$here = current_url();

	print_r($_SESSION['roles']);
	
	if(sizeof($_SESSION['roles']) == 0 && strpos($here, 'errors') == false) {

		$netid = $CI->authorize->getNetid();

		//redirect('errors/unauthorized');
	}

?>