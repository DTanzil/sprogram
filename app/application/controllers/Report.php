<?php
/** 
 * Handles viewing and execution of reports
 * UNFINISHED
 */
class Report extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Report_model');
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('admin_meta');
		$this->load->library('authenticate');

		$this->admin_meta->protectPage(array('admin', 'viewer', 'sponsor', 'advisor', 'venueOperator', 'committee'));
		$this->adminRoles = $this->authenticate->getLevels();
		$this->actions = $this->admin_meta->getPrimaryActions();
	}

	public function index() {
		echo 'index';
		$header['heading'] = "Reporting";
		$this->load->view('header', $header);
	}
}