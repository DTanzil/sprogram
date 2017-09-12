<?php
class Errors extends CI_Controller {

	public function __construct() {
		parent::__construct();
		
		$this->load->helper('url');
		$this->load->library('authorize');

	}

	public function unauthorized() {

			$netid = $this->authorize->getNetid();
			$data["heading"] = "Authorization Error";
			$data["message"] = "The user associated with netid '{$netid}' is not authorized to view this page.";
			$this->load->view('errors/html/error_general', $data);
	}

	public function page_missing() {
		$this->load->view('errors/cli/error_404', array("heading" => "Page Missing", "message" => "The page is missing"));
	}

}
?>