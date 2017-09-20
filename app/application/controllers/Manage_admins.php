<?php
/**
 * Handles view, create, edit, delete operations for Admins
 */
class Manage_admins extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('Manage_admins_model');
		$this->load->model('Admin_model');

		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->library('authorize');

		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('assets');
		$this->load->helper('form');

		

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
	 * Show a table of all admins
	 */
	public function index() {		
		$head['title'] = "Manage Admin";

		$header['mode'] = 'Admin';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		$content['admins'] = $this->Admin_model->getAllAdmins();
		$content['view'] = 'manage_admins';
		$content['description'] = "Manage Administrators";
		$js = array('views/pagination.js', 'views/manage_admins.js');
		$css = array('views/pagination.css');
		$head['pageDependencies'] = getPageDependencies($js, $css);

		$sidebar = array();


		$data['head'] = $head;
		$data['header'] = $header;
		$data['content'] = $content;
		$data['sidebar'] = $sidebar;

		$this->security->xss_clean($data);
		$this->load->view('main', $data);
	}

	/**
	 * Handles the creation of a new administrator
	 */
	public function create() {
		# IF a form has been submitted, process input
		# ELSE Show the form
		if(!empty($_POST)) {
			$adminInfo = $_POST;
			$_POST = null;

			# check to see if an admin with this netid already exists
			# IF exists, go to 'edit' view
			# ELSE perform the create operation
			$adminExists = $this->Admin_model->getAdminByNetid($adminInfo['NetID']);
			if($adminExists) {
				$this->edit($adminExists['UserID'], true, $adminInfo, $adminExists['AdminDeletedAt']);
			} else {
				$newID = $this->Admin_model->addAdmin($adminInfo);
				redirect(base_url('Manage_admins'));
			}
		} else {
			$head['title'] = "Create Admin";

			$header['mode'] = 'Admin';
			$header['view'] = 'templates/header.php';
			$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

			$content['view'] = 'manage_admins/create';
			$content['description'] = 'Add an Administrator';
			$js = array('views/pagination.js');
			$css = array('views/pagination.css');
			$head['pageDependencies'] = getPageDependencies($js, $css);

			$sidebar = $this->Admin_model->getContact('jshill');

			$data['head'] = $head;
			$data['header'] = $header;
			$data['content'] = $content;
			$data['sidebar'] = $sidebar;

			$this->security->xss_clean($data);
			$this->load->view('main', $data);
		}
	}

	/**
	 * @param  int $adminID The ID of the admin to edit
	 * @param  boolean $adminExists **NULLABLE** A flag used in create() indicating this Admin exists
	 * @param  mixed[] $createInfo **NULLABLE** The associated data related to an admin that already exists
	 * @param  string $deletedAt **NULLABLE** A string representation of the date the admin was deleted at
	 *
	 * Handles the editing of an admin
	 */
	public function edit($adminID, $adminExists = false, $createInfo = null, $deletedAt = null) {
		if(!empty($_POST)) {
			$adminInfo = $_POST;
			$adminInfo['UserID'] = $adminID;

			$this->Admin_model->editAdmin($adminInfo);
			redirect(base_url('Manage_admins'));
		} else {

			$head['title'] = "Edit Admin";

			$header['mode'] = 'Admin';
			$header['view'] = 'templates/header.php';
			$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

			# Get the associated data for the given admin
			# IF this admin already exists, overwrite this data with the data provided in $createInfo,
			# 	and pass deletedAt and netid into the view
			$content['adminInfo'] = $this->Admin_model->getAdminByID($adminID);
			if($adminExists) {
				$content['netid'] = $content['adminInfo']['NetID'];
				$content['adminInfo'] = $createInfo;
				$content['deletedAt'] = $deletedAt;
			}

			$content['view'] = 'manage_admins/edit';
			$content['description'] = 'Edit an Administrator';
			$content['adminID'] = $adminID;
			$js = array('views/pagination.js');
			$css = array('views/pagination.css');
			$head['pageDependencies'] = getPageDependencies($js, $css);

			$sidebar = $this->Admin_model->getContact('jshill');

			$data['head'] = $head;
			$data['header'] = $header;
			$data['content'] = $content;
			$data['sidebar'] = $sidebar;

			$this->security->xss_clean($data);
			$this->load->view('main', $data);
		}
	}

	/**
	 * @param  int $adminID The admin we want to delete
	 *
	 * Deletes an admin
	 */
	public function delete($adminID) {
		$this->Admin_model->deleteAdmin($adminID);
		redirect('Manage_admins');
	}
}
?>