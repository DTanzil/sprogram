<?php
class Applications extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$this->load->model('Applications_model');
		$this->load->model('Admin_model');

		$this->load->library('authorize');
		//$this->load->library('session');
		$this->load->library('approval');
		$this->load->library('Mailer');

		$this->load->helper('assets');
		$this->load->helper('html');
		$this->load->helper('url');
	}

	public function create() {
		$f = file_put_contents(dirname(__FILE__) . "/log.txt", "******* New UUF ******\r\n", FILE_APPEND);
		$data = $_POST;
		//echo $f;
		if(isset($_POST)) {
			file_put_contents(dirname(__FILE__) . '/log.txt', print_r($data, true) . "\r\n", FILE_APPEND);
		}
	}

}

?>