<?php
/**
 * Handles views for applications
 * 
 */
class Dashboard extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('Applications_model');
		$this->load->model('Admin_model');

		$this->load->library('authorize');
		//$this->load->library('session');
		$this->load->library('approval');
		//$this->load->library('Mailer');

		$this->load->helper('assets');
		$this->load->helper('html');
		$this->load->helper('url');
	}

	/**
	 * Show all apps, across all approval states
	 */
	public function index() {
		$head['title'] = "Dashboard";

		//$header['mode'] = 'Application';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		//$content['apps'] = $this->Applications_model->getAllApps();
		//$content['viewVal'] = 3;
		$content['view'] = 'dashboard/dashboard';

		$content['openApps'] = $this->getOpenApps($this->authorize->getNetID());

		$js = array('views/pagination.js');
		$css = array('views/pagination.css', 'views/dashboard/dashboard.css');
		$head['pageDependencies'] = getPageDependencies($js, $css);

		$sidebar = $this->Admin_model->getContact($this->authorize->getNetid());

		$data['head'] = $head;
		$data['header'] = $header;
		$data['content'] = $content;
		//$data['content'] = array();
		$data['sidebar'] = $sidebar;

		$this->security->xss_clean($data);
		$this->load->view('main', $data);
	}

	private function getOpenApps($netID) {
		return $this->approval->getOpenAppsForUser($netID);
	}

}

?>