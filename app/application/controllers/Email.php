<?php
/**
 * The main page of the application. Handles viewing of applications.	
 */
class Email extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('Mailer');

		$this->load->library('authorize');
		//$this->load->library('session');
		$this->load->library('approval');
		$this->load->library('Mailer');

		$this->load->helper('assets');
		$this->load->helper('html');
		$this->load->helper('url');
		$this->load->helper('form');

		$this->load->model('Admin_model');
		$this->load->model('Email_model');
	}

	public function index() {
		$head['title'] = "Manage Email Templates";

		$header['mode'] = 'Application';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		$content['templates'] = $this->Email_model->getTemplates();
		$content['view'] = "email/index.php";
		//$content['description'] = "Manage Applications";

		$js = array('views/pagination.js');
		$css = array('views/pagination.css');
		$head['pageDependencies'] = getPageDependencies($js, $css);

		$sidebar = $this->Admin_model->getContact($this->authorize->getNetid());

		$data['head'] = $head;
		$data['header'] = $header;
		$data['content'] = $content;
		$data['sidebar'] = $sidebar;

		$this->security->xss_clean($data);
		$this->load->view('main', $data);

	}

	public function create() {
		$head['title'] = "Create an Email Template";

		$header['mode'] = 'Application';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		$content['actions'] = $this->Email_model->getActions();
		$content['view'] = "email/create.php";
		//$content['description'] = "Manage Applications";

		$js = array('views/pagination.js', 'editor.js');
		$css = array('views/pagination.css', 'content-tools-styles.css');
		$head['pageDependencies'] = getPageDependencies($js, $css);

		$sidebar = $this->Admin_model->getContact($this->authorize->getNetid());

		$data['head'] = $head;
		$data['header'] = $header;
		$data['content'] = $content;
		$data['sidebar'] = $sidebar;

		$this->security->xss_clean($data);
		$this->load->view('main', $data);
	}

}
?>