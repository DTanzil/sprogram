<?php
/**
 * Handles operations for managing locations
 * 'Location' here is defined as a specific sublocation (room, area) within a building (which includes 'Outdoors' and other stuff)
 */
class Manage_locations extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$this->load->model('Manage_locations_model');
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
	 * Show a list of all locations
	 */
	public function index() {		
		$head['title'] = "Manage Locations";

		$header['mode'] = 'Location';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		$content['locations'] = $this->Manage_locations_model->getAllLocations();
		$content['view'] = 'manage_locations/index';
		$content['description'] = "Manage Locations";
		$js = array('views/pagination.js', 'views/manage_admins.js');
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

	/**
	 * Handles creating a new location
	 */
	public function create() {
		#IF a form has been submitted, process data
		#ELSE show the form
		if(!empty($_POST)) {
			$locationInfo = $_POST;
			$_POST = null;

			# Create the location
			# Protect against the possibility this location existed before by wiping
			#	any operator relations from this location and then adding them again
			$locationID = $this->Manage_locations_model->createLocation($locationInfo);
			$admins = $locationInfo['admins'];
			$this->Manage_locations_model->deleteAllOperatorsFromLocation($locationID);
			$this->Manage_locations_model->addOperatorsToRoom($admins, $locationID);

			redirect(base_url('Manage_locations'));
			
		} else {
			//show form
			$head['title'] = "Create Location";

			$header['mode'] = 'Admin';
			$header['view'] = 'templates/header.php';

			$content['view'] = 'manage_locations/create';
			$content['description'] = 'Add a Location';
			$content['buildings'] = $this->Manage_locations_model->getAllBuildings();

			$js = array('views/pagination.js', 'views/manage_locations.js');
			$css = array('views/pagination.css', 'views/manage_locations.css');
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
	 * @param  int $locationID The location we want to edit
	 *
	 * Handles editing of a location
	 */
	public function edit($locationID) {
		if(!empty($_POST)) {
			$locationInfo = $_POST;
			$locationInfo['locationID'] = $locationID;

			$this->Manage_locations_model->editLocation($locationInfo);

			$admins = $locationInfo['admins'];

			echo '<pre>';
			print_r($locationInfo);
			print_r($admins);
			echo '</pre>';

			$this->Manage_locations_model->deleteAllOperatorsFromLocation($locationID);
			$this->Manage_locations_model->addOperatorsToRoom($admins, $locationID);

			die;
			echo 'edit form submit';
			redirect(base_url('Manage_locations'));
		} else {

			$head['title'] = "Edit Location";

			$header['mode'] = 'Location';
			$header['view'] = 'templates/header.php';

			$content['locationInfo'] = $this->Manage_locations_model->getLocationByID($locationID);
			$content['buildings'] = $this->Manage_locations_model->getAllBuildings();
			$content['admins'] = $this->Manage_locations_model->getOperatorsForLocation($locationID);

			$content['view'] = 'manage_locations/edit';
			$content['description'] = 'Edit a Location';
			$content['locationID'] = $locationID;
			$js = array('views/pagination.js', 'views/manage_locations.js');
			$css = array('views/pagination.css', 'views/manage_locations.css');
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
	 * @param  int $locationID
	 * 
	 * Deletes an admin
	 */
	public function delete($locationID) {
		$this->Manage_locations_model->deleteLocation($locationID);
		redirect("Manage_locations");
	}

	/**
	 * @api
	 * @deprecated [<version>] Doesn't use AJAX anymore
	 * 
	 */
	public function getOperators() {
		$ops = $this->Manage_locations_model->getOperators();

		header('Content-Type: application/json');
		echo json_encode($ops);
	}

	/**
	 * @api
	 * @deprecated [<version>] Doesn't use AJAX anymore
	 * 
	 */
	public function getOperatorsForLocation() {
		$locationID = $this->input->post('room');
		$ops = $this->Manage_locations_model->getOperatorsForLocation($locationID);

		header('Content-Type: application/json');
		echo json_encode($ops);
	}

	/**
	 * @api
	 * @deprecated [<version>] Doesn't use AJAX anymore
	 */
	public function addOperatorToRoom() {
		$locationID = $this->input->post('locationID');
		$admins = $this->input->post('admins');
		$this->Manage_locations_model->deleteAllOperatorsFromLocation($locationID);
		$this->Manage_locations_model->addOperatorsToRoom($admins, $locationID);
	}
}
?>