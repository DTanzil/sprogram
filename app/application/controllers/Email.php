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

	public function edit($id) {
		$head['title'] = "Edit an Email Template";

		$header['mode'] = 'Application';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		$content['actions'] = $this->Email_model->getActions();
		$content['template'] = $this->Email_model->getTemplate($id);
		$content['view'] = "email/edit.php";
		//$content['description'] = "Manage Applications";

		$js = array('views/pagination.js', 'views/email/edit.js');
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

	public function postTemplate() {
		$this->Email_model->editTemplate($_POST['id'], $_POST['EmailSubject'], $_POST['EmailBody']);
		redirect(base_url('Email'));
	}

	public function cleanTemplates() {
		$templates = $this->Email_model->getJustTemplates();
		file_put_contents('C:/Users/HUB Tech Student/Desktop/clean-templates.txt', '');
		foreach($templates as $template) {
			$comments = '/^<!--\[if(.|\n)*<!\[endif\]-->$|^\s*<!--.*-->/';
			$template['EmailBody'] = '<p>' . strip_tags($template['EmailBody'], '<a><b><i><strong><br><ul><ol><li>') . '</p>';

			$this->Email_model->editTemplate($template['EmailTemplateID'], $template['EmailSubject'], $template['EmailBody']);


			file_put_contents('C:/Users/HUB Tech Student/Desktop/clean-templates.txt', 'template ' . $template['EmailTemplateID'] . $template['EmailBody'] . '\n\n', FILE_APPEND);
		}
		echo 'done';

		# $this->Email_model->updateTemplates($templates);
	}

}
?>