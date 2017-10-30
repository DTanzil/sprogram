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
			$this->CI->email->cc($cc);

			$this->CI->email->subject($subject);
			$this->CI->email->message($message);

			//$this->CI->email->send();
				return $this->CI->email->print_debugger();
		}

		public function performMailActionForApp($appID, $actionType, $userRoleID = null) {
			//if $actorID is specified, we only want to CC the email associated with that ID (some admin)

			//determine action, get templates
			
			//for each template
				$templates = $this->getTemplatesForApp($appID, $actionType);
				$params = array(
					"ApplicationID" => $appID,
				);
				foreach($templates as $template) {
					$recipients = $this->parseAddressees($template['Recipients'], $params);
					$cc = $this->parseAddressees($template['CC'], $params);


				var_dump($recipients);
				var_dump($cc);


					foreach($recipients as $rec) {
						echo 'rec\r\n';
						var_dump($rec);
						//$rec = $rec[0];
						$this->sendEmail('jshill@uw.edu', $template['EmailSubject'], 
							$template['EmailBody'] . 'Email would go here: ' . $rec['UserEmail']  );

						$this->logEmail($appID, $template['EmailTemplateID'], $rec['UserRoleID']);
					}
				}
				//parse recipients

				//send email
			
		}

		//mail action for venue ($venueID, $userRoleID, $actionType )
		public function mailActionForApproval($venueID, $userRoleID, $approvalType, $actionType) {
			$approval = $this->CI->approval->getApproval($venueID, $userRoleID, $approvalType)[0];

			$templates = $this->getTemplatesForApproval($approval["ApprovalID"], $actionType);
			$params = array(
				"VenueID" => $venueID,
				"UserRoleID" => $userRoleID,
				"ApprovalType" => $approvalType
			);
			foreach($templates as $template) {
				$recipients = $this->parseAddressees($template['Recipients'], $params);
				//var_dump($recipients);
				$cc = $this->parseAddressees($template['CC'], $params);

				var_dump($recipients);
				var_dump($cc);


				foreach($recipients as $rec) {
					//$rec = $rec[0];
					//var_dump($rec);
					$this->sendEmail('jshill@uw.edu', $template['EmailSubject'], 
						$template['EmailBody'] . 'Email would go here: ' . $rec['UserEmail'] );
					$this->logEmail($appID, $template['EmailTemplateID'], $rec['UserRoleID']);
				}
			}
			//parse recipients

			//send email
		}

		private function parseAddressees($data, $params = null) {
			# Quick hack that allows the admins to send emails directly to individual sponsors.
			if(strpos($data, '@') !== false) {
				# Get the user role id for this email
				$netid = explode('@', $data)[0];
				echo $netid;
				$admin = $this->CI->Admin_model->getAdminByNetid($netid);
				return array(
					array(
						"UserRoleID" => $admin['UserRoleID'],
						"UserEmail" => $data
					)
				);
			}

			$addressees = explode(',', $data);
			$return = array();
			foreach($addressees as $addr) {
				# we need all the admins of this type
				if(strpos($addr, 'all') !== false) {
					$addr = str_replace('-all', '', $addr);

					if($params['ApplicationID']) {
						$admins = $this->CI->Applications_model->getAdminsForApp($params['ApplicationID'], $addr);

						foreach($admins as $admin) {
							array_push($return, array(
								"UserRoleID" => $admin['UserRoleID'],
								"UserEmail" => $admin['UserEmail']
							));
						}
					} elseif($params['VenueID']) {
						$appr = $this->CI->approval->getApproval($params['VenueID'], $params['UserRoleID'], $params['ApprovalType'])[0];

						$admins = $this->CI->Applications_model->getAdminsForApp($appr['ApplicationID'], $addr);

						foreach($admins as $admin) {
							array_push($return, array(
								"UserRoleID" => $admin['UserRoleID'],
								"UserEmail" => $admin['UserEmail']
							));
						}
					}
				} else {
					if(!$params) {
						return 'Additional parameters required';
					} elseif(array_key_exists('VenueID', $params)) {
						$admin = $this->CI->approval->getApproval($params['VenueID'], $params['UserRoleID'], $params['ApprovalType'])[0];
						var_dump($admins);
						$admins = array(
							"UserRoleID" => $admin['UserRoleID'],
							"UserEmail" => $admin['UserEmail']
						);
						array_push($return, $admins);
					} else {
						$admins = $this->CI->Applications_model->getAdminsForApp($params['ApplicationID'], $addr);

						foreach($admins as $admin) {
							$return[] = array(
								"UserRoleID" => $admin['UserRoleID'], 
								"UserEmail" => $admin['UserEmail']);
						}

					}

				}
			}
			// $new = array();
			// foreach($return as $k=>$v) {
			// 	array_push($new, $v);
			// }
			//$return = $this->flattenOnce($return);
			echo '<p>Addressees</p>';
			echo '<pre>';
			print_r($return);
			echo '</pre>';
			return $return;
		}

		public function getTemplatesForApp($id, $actionType) {
			$templates = $this->CI->db->query("
				SELECT DISTINCT et.* FROM EmailTemplate et
					JOIN ActionEmailTemplate aet ON aet.EmailTemplateID = et.EmailTemplateID
					JOIN Action a ON a.ActionID = aet.ActionID
					JOIN AppEmailAction aea ON aea.ActionID = a.ActionID
					JOIN ActionType at ON a.ActionTypeID = at.ActionTypeID
					WHERE aea.ApplicationID = {$id}
					AND at.ActionTypeID = (SELECT ActionTypeID FROM ActionType WHERE ActionTypeName = '{$actionType}')
			");
			// echo '<p>templates</p>';
			// var_dump($templates->result_array());
			return $templates->result_array();
		}

		public function getTemplatesForApproval($id, $actionType) {
			$templates = $this->CI->db->query("
				SELECT DISTINCT et.EmailTemplateID, et.EmailTemplateName FROM EmailTemplate et
					JOIN Action a ON a.EmailTemplateID = et.EmailTemplateID
					JOIN AppEmailAction aea ON aea.ActionID = a.ActionID
					JOIN ActionType at ON a.ActionTypeID = at.ActionTypeID
					WHERE aea.ApprovalID = {$id}
					AND at.ActionTypeID = (SELECT ActionTypeID FROM ActionType WHERE ActionTypeName = '{$actionType}')
			");
			return $templates->result_array();
		}

		public function getTemplates($appID) {
			$templates = $this->CI->db->query("
				SELECT DISTINCT et.EmailTemplateID, et.EmailTemplateName FROM EmailTemplate et
					JOIN Action a ON a.EmailTemplateID = et.EmailTemplateID
					JOIN AppEmailAction aea ON aea.ActionID = a.ActionID
					JOIN ActionType at ON a.ActionTypeID = at.ActionTypeID
					WHERE aea.ApplicationID = {$appID}
			");
			return $templates->result_array();
		}

		public function attachActionsToApproval($approvalID, $approvalType) {
			$params = array($approvalID, $approvalType);

			//get actions for the app type and approval type
			$actions = $this->CI->db->query("
				SELECT ac.ActionID FROM Action ac
					JOIN ApplicationType at ON at.ApplicationTypeID = ac.ApplicationTypeID
					JOIN Application a ON at.ApplicationTypeID = a.ApplicationTypeID
					JOIN Venue v ON v.ApplicationID = a.ApplicationID
					JOIN VenueUserRole vur ON vur.VenueID = v.VenueID 
					JOIN Approval appr ON appr.VenueUserRoleID = vur.VenueUserRoleID
				WHERE appr.ApprovalID = ?
					AND ac.Category = ?
			", $params)->result_array();

			# Get any emails that are specific to the permit type
			$permits = $this->CI->db->query("
				SELECT ac.ActionID FROM Action ac
					JOIN Permit p ON p.PermitID = ac.PermitID
					JOIN Application a ON p.PermitID = a.PermitID
					JOIN Venue v ON v.ApplicationID = a.ApplicationID
					JOIN VenueUserRole vur ON vur.VenueID = v.VenueID 
					JOIN Approval appr ON appr.VenueUserRoleID = vur.VenueUserRoleID
				WHERE appr.ApprovalID = ?
					AND ac.Category = ?
			", $params)->result_array();
			$actions = array_merge($actions, $permits);

			// echo "<p>Actions attached to this approval of type {$approvalType}</p>";
			// echo '<pre>';
			// var_dump($actions);
			// echo '</pre>';

			foreach($actions as $action) {
				$insert = $this->CI->db->query("
					INSERT INTO AppEmailAction (ActionID, ApprovalID)
					VALUES ({$action['ActionID']}, {$approvalID})
				");
				if(!$insert) {
					return $this->CI->db->error();
				}
			}
		}

		public function attachActionstoApp($appID, $rene = null) {

			# Get for the general application type (UUF or ASR)
			$params = array($appID, 'application');
			$actions = $this->CI->db->query("
				SELECT * FROM Action ac
					JOIN ApplicationType at ON at.ApplicationTypeID = ac.ApplicationTypeID
					JOIN Application a ON at.ApplicationTypeID = a.ApplicationTypeID
				WHERE a.ApplicationID = ?
                AND ac.Category = ?
                AND ac.PermitID IS NULL
                AND ac.ActionName NOT LIKE '%Submit%'
			", $params)->result_array();

			# Get for the specific permit type (SOL, BANQ, etc)
			$permits = $this->CI->db->query("
				SELECT * FROM Action ac
					JOIN Permit p ON p.PermitID = ac.PermitID
					JOIN Application a ON a.PermitID = p.PermitID
				WHERE a.ApplicationID = ?
					AND ac.Category = ?
			", $params)->result_array();
			$actions = array_merge($actions, $permits);

			# If Rene is the sponsor, include any special emails she gets
			if($rene === null) {
				$submitAction = $this->CI->db->query("
				SELECT * FROM Action ac
						JOIN ApplicationType at ON at.ApplicationTypeID = ac.ApplicationTypeID
						JOIN Application a ON at.ApplicationTypeID = a.ApplicationTypeID
					WHERE a.ApplicationID = ?
						AND ac.Category = ?
						AND ac.ActionName NOT LIKE '%Rene%'
						AND ac.ActionName LIKE '%submit%'
						AND ac.PermitID IS NULL
				", $params)->result_array();
			} else {
				$submitAction = $this->CI->db->query("
					SELECT * FROM Action ac
						JOIN ApplicationType at ON at.ApplicationTypeID = ac.ApplicationTypeID
						JOIN Application a ON at.ApplicationTypeID = a.ApplicationTypeID
					WHERE a.ApplicationID = ?
						AND ac.Category = ?
						AND ac.ActionName LIKE '%Rene%'
						AND ac.PermitID IS NULL
				", $params)->result_array();
			}
			$actions = array_merge($actions, $submitAction);

			foreach($actions as $action) {
				$insert = $this->CI->db->query("
					INSERT INTO AppEmailAction (ActionID, ApplicationID)
					VALUES ({$action['ActionID']}, {$appID})
				");
				if(!$insert) {
					return $this->CI->db->error();
				}
			}
		}

		public function getActionsForApp($appID) {
			$actions = $this->CI->db->query("
				SELECT a.ActionID FROM Action a
					JOIN AppEmailAction aea ON aea.ActionID = a.ActionID
				WHERE aea.ApplicationID = {$appID}
					
			");
			return $actions->result_array();
		}

		public function getActionsForApproval($approvalID) {

		}

		public function createAction($data) {

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

		private function flatten(array $array) {
		    $return = array();
		    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
		    return $return;
		}

		private function flattenOnce($array) {
			$return = array();
			foreach($array as $el) {
				$return[] = array_keys($el);
			}
			return $return;
		}


		/**
		 * ACTION: App Submitted
		 * 	TEMPLATES:
			 	Applicant gets:
		 			TO: applicant
		 			TEMPLATE: app submitted - applicant

				sponsor(s) gets:
					TO: sponsor




		 */
	}


?>