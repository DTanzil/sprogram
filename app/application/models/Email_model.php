<?php
class Email_model extends CI_Model {

	function __construct(){
		$this->load->database();
	}

	public function getTemplates() {
		$templates = $this->db->query("
			SELECT * FROM EmailTemplate et
			JOIN Action a ON a.EmailTemplateID = et.EmailTemplateID
		")->result_array();

		return $templates;
	}

	public function getActions() {
		$actions = $this->db->query("
			SELECT * FROM Action
		")->result_array();

		return $actions;
	}
}
?>