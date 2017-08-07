<?php
/**
 * The main page of the application. Handles viewing of applications.	
 */
class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Admin_model');
		$this->load->model('Applications_model');
		
		$this->load->library('session');
		$this->load->helper('url');

		$this->load->library('authorize');

		$this->load->helper('html');
		$this->load->helper('assets');

		# dummy data for now. Authorize library isn't finished yet
		$netid = 'jshill';
		$role = 'Admin';
		if($this->authorize->authUser($netid, $role)) {
			
		} else {
			$data["heading"] = "Authorization Error";
			$data["message"] = "'{$netid}' is not authorized to view this page. You must be '{$role}' to view this page.";
			$this->load->view('errors/html/error_general', $data);
		}

	}

	/**
	 * View all applications for any given approval state: sponsor, venue, committee
	 */
	public function index() {
		$head['title'] = "Admin";

		$header['mode'] = 'UUF';
		$header['view'] = 'templates/header.php';

		$sidebar = $this->Admin_model->getContact('jshill');

		$content['apps'] = $this->Applications_model->getAllApps();
		$content['viewVal'] = 3;
		$content['view'] = 'applications';
		$js = array('views/pagination.js', 'views/app-flags.js');
		$css = array('views/pagination.css', 'views/app-flags.css');
		$head['pageDependencies'] = getPageDependencies($js, $css);

		$data['head'] = $head;
		$data['header'] = $header;
		$data['content'] = $content;
		$data['sidebar'] = $sidebar;

		$this->security->xss_clean($data);
		$this->load->view('main', $data);
	}

	public function testAdminsByID() {
		$data = $this->Admin_model->getAdminsByApplicationID(1);
		var_dump($data);
	}

	public function testAllAdmins() {
		$data = $this->Admin_model->getAllAdmins();
		var_dump($data);
	}

	public function testGetAdmin() {
		$data = $this->Admin_model->getAdmin(1);
		var_dump($data);
	}
}

?>