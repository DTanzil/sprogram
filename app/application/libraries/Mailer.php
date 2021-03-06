<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	/**
	 * CREATED 5/17/17 by JSHILL
	 * 

	*/	
	class Mailer {
		private $CI; # Codeigniter instance

		# Store a reference to the CodeIgniter object, load database
		public function __construct() {
			$this->CI =& get_instance();
			$this->CI->load->library('email');
			$this->CI->load->database();
			$this->CI->load->model('Applications_model');
			$this->CI->load->model('Admin_model');
			$this->CI->load->library('parser');
			$this->CI->load->library('approval');

			$config['protocol'] = 'sendmail';
			$config['mailpath'] = '/usr/sbin/sendmail';
			$config['charset'] = 'iso-8859-1';
			$config['wordwrap'] = TRUE;

			//$this->CI->email->initialize($config);
		}

		public function sendEmail($to, $subject, $message, $cc = null) {

			# changing this for testing
			$to = 'jshill@uw.edu';

			$this->CI->email->from('postmaster@localhost', 'SProgram Dev');
			$this->CI->email->reply_to('jshill@uw.edu', 'JS');
			$this->CI->email->to($to);
			
			# $this->CI->email->cc($cc);

			$this->CI->email->subject($subject);
			$this->CI->email->message($message);

			 $this->CI->email->send();
				return $this->CI->email->print_debugger();
		}

		public function mailAction($appID, $actionName, $params = null) {
			echo 'mailAction ';
			echo 'params:\r\n';
			var_dump($params);
			$details = $this->CI->Applications_model->getDetailsForApp($appID);
			$details = array_merge($details, $this->CI->Applications_model->getUsersForApp($appID));
			if(isset($params)) {array_merge($details, $params); }
			if(isset($params['VenueID'])) {
				$details['Venue'] = $this->CI->db->query("
					SELECT v.*, r.RoomName, r.RoomAbbr, b.BuildingName, 
								b.BuildingAbbr, appr.ApprovalEndDate, appr.Descision AS Decision, 
								appr.DescisionRemark AS DecisionRemark 
					FROM Venue v
						JOIN Room r ON v.RoomID = r.RoomID
						JOIN Building b ON b.BuildingID = r.BuildingID
						JOIN VenueUserRole vur ON v.VenueID = vur.VenueID
						JOIN Approval appr ON appr.VenueUserRoleID = vur.VenueUserRoleID
					WHERE v.VenueID = {$params['VenueID']}
						AND appr.ApprovalType = 'VenueOperator'
						AND (appr.Descision = 'denied' OR appr.Descision = 'approved')
				")->result_array();
			} else {
				$details['Venues'] = $this->CI->Applications_model->getVenueDetails($appID);
				$details['EventStartDate'] = $details['Venues'][0]['EventStartDate'];
				$details['EventEndDate'] = $details['Venues'][0]['EventEndDate'];
				$approvals = $this->CI->approval->summarizeApprovalStages($appID);
				foreach($approvals as $appr) {
					$details = array_merge($details, $appr);
				}

			}
			var_dump($details);

			$templates = $this->getActionTemplates($actionName, $details['PermitID'], $details['ApplicationTypeID']);

			if(isset($params)) {
				$params["ApplicationID"] = $appID;
			} else {
				$params = array("ApplicationID" => $appID);
			}
			foreach($templates as $template) {
				//echo 'template: ';
				//var_dump($template['EmailTemplateName']);

				//var_dump($details);
				 
				$parsedTitle = $this->CI->parser->parse_string($template['EmailSubject'], $details, TRUE);
				$parsedTemplate = $this->CI->parser->parse_string($template['EmailBody'], $details, TRUE);

				//echo 'get recs';
				$recipients = $this->parseAddressees($template['Recipients'], $params);
				//echo 'get ccs';
				$cc = $this->parseAddressees($template['CC'], $params);
				$cc_recs = array();
				foreach($cc as $addresses) {
					$cc_recs[] = $addresses['UserEmail'];
				}
				$cc_recs = implode(',', $cc_recs);
				foreach($recipients as $rec) {
					//echo 'rec\r\n';
					//var_dump($rec);
					//$rec = $rec[0];
					$this->sendEmail('jshill@uw.edu', $parsedTitle, $parsedTemplate, $cc_recs);

					$this->logEmail($appID, $template['EmailTemplateID'], $rec['UserRoleID']);
				}
			}
		}

		# Used when you need to send emails with venue specific info to each venue
		public function venueMailAction($appID, $actionName, $params =  null) {
			echo 'venueMailAction ';
			$details = $this->CI->Applications_model->getDetailsForApp($appID);
			$details = array_merge($details, $this->CI->Applications_model->getUsersForApp($appID));
			if(isset($params)) {array_merge($details, $params); }

			$venues = $this->CI->Applications_model->getVenueDetails($appID);
			$details['EventStartDate'] = $venues[0]['EventStartDate'];
			$details['EventEndDate'] = $venues[0]['EventEndDate'];
			$details['Approvals'] = $this->CI->approval->summarizeApprovalStages($appID);

			$templates = $this->getActionTemplates($actionName, $details['PermitID'], $details['ApplicationTypeID']);

			if(isset($params)) {
				$params["ApplicationID"] = $appID;
			} else {
				$params = array("ApplicationID" => $appID);
			}
			foreach($templates as $template) {
				echo 'template: ';
				var_dump($template['EmailTemplateName']);
				foreach($venues as $venue) {
					$details['Venue'] = array($venue);

					echo 'DETAILS ';
					var_dump($details);

					$parsedTitle = $this->CI->parser->parse_string($template['EmailSubject'], $details, TRUE);
					$parsedTemplate = $this->CI->parser->parse_string($template['EmailBody'], $details, TRUE);
	
					$params['VenueID'] = $venue['VenueID'];
					echo 'get recs';
					$recipients = $this->parseAddressees($template['Recipients'], $params);
					echo 'get ccs';
					$cc = $this->parseAddressees($template['CC'], $params);
					$cc_recs = array();
					foreach($cc as $addresses) {
						$cc_recs[] = $addresses['UserEmail'];
					}
					$cc_recs = implode(',', $cc_recs);
					foreach($recipients as $rec) {
						# Reparse the template to add details for each individual recipient
						$details['Operator'] = $rec;
						$parsedTemplateRec = $this->CI->parser->parse_string($template['EmailBody'], $details, TRUE);
						echo 'rec\r\n';
						var_dump($rec);
						//$rec = $rec[0];
						$this->sendEmail('jshill@uw.edu', $parsedTitle, $parsedTemplateRec, $cc_recs);
	
						$this->logEmail($appID, $template['EmailTemplateID'], $rec['UserRoleID']);
					}
				}
			}
		}
		

		public function getActionTemplates($actionName, $permitID, $apptypeID) {
			return $this->CI->db->query("
				SELECT et.* FROM AppAction paat
				JOIN Action ac ON ac.ActionID =  paat.ActionID
				JOIN ActionEmailTemplate aet ON aet.ActionID = ac.ActionID
				JOIN EmailTemplate et ON et.EmailTemplateID = aet.EmailTemplateID
				WHERE paat.PermitID = {$permitID}
				AND paat.ApplicationTypeID = {$apptypeID}
				AND ActionName LIKE '%{$actionName}%'
			")->result_array();
		}

		private function parseAddressees($groups, $params = null) {
			$rec_groups = explode(',', $groups);
			//echo 'recipient groups: ';
			//var_dump($rec_groups);

			$addresses = array();
			foreach($rec_groups as $group) {
				//echo 'strpos of singlevenue: ';
				//var_dump(strpos($group, 'SingleVenue' !== false));
				if($group == 'SingleVenue') {
					//echo 'single venue\r\n';
					//echo 'params: ';
					//var_dump($params);
					//echo '\r\n';
					# recipients will be operators of a single venue
					if($params['VenueID']) { 
						$addresses = array_merge($addresses, $this->CI->Admin_model->getOperatorsForVenue($params['VenueID']));
					} else {
						trigger_error("Cannot retrieve SingleVenue recipients without VenueID", E_USER_ERROR);
					}
				} elseif(strpos($group, '@' !== false)) {
					# We're sending an email to a specific person
					$netid = explode('@', $group)[0];
					$admin = $this->CI->Admin_model->getAdminByNetid($netid);
					
					$addresses[] = array(
						"UserRoleID" => $admin['UserRoleID'],
						"UserFname" => $admin['UserFname'],
						"UserLname" => $admin['UserLname'],
						"UserEmail" => $data
					);
				} else {
					# If it's not a specialty case, we can assume we want all users of a given type
					$admins = $this->CI->Applications_model->getAdminsForApp($params['ApplicationID'], $group);
					$addresses = array_merge($addresses, $admins);
				}
			}
			//echo '<p>Addressees</p>';
			//echo '<pre>';
			//print_r($addresses);
			//echo '</pre>';
			return $addresses;
		}

		public function getTemplates($appID) {
			$templates = $this->CI->db->query("
				SELECT DISTINCT et.* FROM AppAction paat
				JOIN Action ac ON ac.ActionID =  paat.ActionID
				JOIN ActionEmailTemplate aet ON aet.ActionID = ac.ActionID
				JOIN EmailTemplate et ON et.EmailTemplateID = aet.EmailTemplateID
				JOIN ApplicationType at ON at.ApplicationTypeID = ac.ApplicationTypeID
				JOIN Application a ON a.ApplicationTypeID = at.ApplicationTypeID
				WHERE a.ApplicationID = {$appID}
			");
			return $templates->result_array();
		}

		public function getActionsForApp($appID) {
			$actions = $this->CI->db->query("
				SELECT a.ActionID FROM Action a
					JOIN AppEmailAction aea ON aea.ActionID = a.ActionID
				WHERE aea.ApplicationID = {$appID}
					
			");
			return $actions->result_array();
		}

		public function resend($to, $templateID, $appID) {
			$template = $this->CI->db->query("
				SELECT * FROM EmailTemplate WHERE EmailTemplateID = {$templateID}
			")->row_array();

			$email = $this->CI->db->query("SELECT UserEmail FROM User u JOIN UserRole ur ON ur.UserID = u.UserID WHERE ur.UserRoleID = {$to}")->row_array()['UserEmail'];

			# $to, $subject, $message, $cc = null
			$this->sendEmail($to, $template['EmailSubject'], $template['EmailBody']);
			$this->logEmail($appID, $templateID, $to);

		}

		private function logEmail($appID, $templateID, $urID) {
			echo 'inserting email record';
			$this->CI->db->query("
				INSERT INTO EmailRecord(ApplicationID, EmailTemplateID, UserRoleID, EmailRecordDate)
					VALUES({$appID}, {$templateID}, {$urID}, NOW())
			");
		}
	}


?>