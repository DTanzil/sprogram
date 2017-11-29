<?php
class Email_model extends CI_Model {

	function __construct(){
		$this->load->database();
	}

	public function getTemplates() {
		$templates = $this->db->query("
			SELECT et.*, GROUP_CONCAT(a.ActionName) AS Actions FROM EmailTemplate et
				JOIN ActionEmailTemplate aet ON et.EmailTemplateID = aet.EmailTemplateID
				JOIN Action a ON aet.ActionID = a.ActionID
			GROUP BY et.EmailTemplateID
		")->result_array();

		return $templates;
	}

	public function getActions() {
		$actions = $this->db->query("
			SELECT * FROM Action
		")->result_array();

		return $actions;
	}

	public function getTemplate($id) {
		return $this->db->query("
			SELECT * FROM EmailTemplate WHERE EmailTemplateID = {$id}
		")->row_array();
	}

	public function editTemplate($id, $subject, $body) {
		$subject = $this->security->xss_clean($subject);
		$body = $this->security->xss_clean($body);

		//echo 'subject';
		#var_dump($subject);
		// echo 'body';
		// var_dump($body);

		$update = $this->db->query("
			UPDATE EmailTemplate
				SET EmailSubject = ?,
				EmailBody = ?
			WHERE EmailTemplateID = ?
		", array($subject, $body, $id));

		#var_dump($update);
	}

	public function getJustTemplates() {
		$templates = $this->db->query("
			SELECT * FROM EmailTemplate et
		")->result_array();

		return $templates;
	}

	public function updateTemplates($templates) {
		foreach($templates as $template) {
			$template['EmailBody'] = $this->db->escape($template['EmailBody']);
			$this->db->query("
			UPDATE EmailTemplate
				SET EmailBody = {$template['EmailBody']}
			WHERE EmailTemplateID = {$template['EmailTemplateID']}
		");
		}
	}
}
?>