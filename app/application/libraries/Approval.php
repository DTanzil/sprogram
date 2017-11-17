<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	/**
	 * CREATED 5/24/17 by JSHILL
	 * This library is responsible for determining the approval status of an application,
	 * and advancing/not advancing that application. It will also trigger email events.

	*/	
	class Approval {
		private $CI; # Codeigniter instance
		private $approvalOrder; # defines an order to approval statuses (submitted before sponsor, sponsor before venue, etc)

		# Store a reference to the CodeIgniter object, load database
		public function __construct() {
			$this->CI =& get_instance();
			$this->CI->load->database();
			$this->CI->load->library('mailer');
			$this->approvalOrder = array(
				'submitted',
				'sponsor',
				'venue',
				'committee',
				'completed'
			);
		}

		/**
		 * @param  int $appID the ID of the application we want to check
		 * @return string The status of the entire application (submitted, venue, sponsor, etc)
		 */
		public function getStatus($appID) {
			$status = $this->CI->db->query(
				"SELECT Status FROM Application
				 WHERE ApplicationID = {$appID}"
			);

			return $status->row()->Status;
		}

		public function setStatus($appID, $status, $reason = null) {
			$this->CI->db->query("
				UPDATE Application
					SET Status = '{$status}', StatusReason = '{$reason}'
				WHERE ApplicationID = {$appID}
			");
		}

		# UNFINISHED
		# A nice wrapper for updating the sponsor status of an app. Only sends one email
		public function updateSponsorDecision($appID, $signature, $descision, $remark = null) {
			$sponsorApprovals = $this->getApprovalsByType($appID, 'sponsor');
			foreach($sponsorApprovals as $appr) {
				$this->updateApproval($appr['UserRoleID'], $appr['VenueID'], 'sponsor', $signature, $descision);
				//$this->mailer->mailActionForApproval($appr['VenueID'], $appr['UserRoleID'], $appr['ApprovalType'], 'approved');
			}
			//$this->CI->mailer->performMailActionforApp($appID, 'sponsor descision');
		}

		public function updateCommitteeDecision($appID, $signature, $descision, $remark = null) {
			$committeeApprovals = $this->getApprovalsByType($appID, 'committee');
			foreach($committeeApprovals as $appr) {
				$this->updateApproval($appr['UserRoleID'], $appr['VenueID'], 'committee', $signature, $descision);
				//$this->mailer->mailActionForApproval($appr['VenueID'], $appr['UserRoleID'], $appr['ApprovalType'], 'approved');
			}

		}

		public function updateVenueDecision($approvalID, $signature, $decision) {
			# $userRoleID, $venueID, $type, $signature, $descision, $descisionRemark = ''
			//$this->updateApproval($userRoleID, $venueID, 'venue', 'default', $decision);
			//
			$params = array(
				$decision,
				$signature,
				$approvalID
				
			);

			$this->CI->db->query("
				UPDATE Approval
					SET ApprovalEndDate = NOW(),
					Descision = ?,
					ApprovalSignature = ?
				WHERE ApprovalID = ?
					
			", $params);

			$confirm = $this->CI->db->query("
				SELECT ApprovalID, ApprovalEndDate, Descision FROM Approval WHERE ApprovalID = {$approvalID}
			")->result_array()[0];
			
			return $confirm;

			

		}

		# UNTESTED
		# Advances the status of an applcation if all the proper requirements have been completed
		public function advanceApplication($appID, $toStep) {

			$currentStatus = $this->getStatus($appID);


			# ensure that all appropriate approvals have been submitted
			# IF any denials, return them and exit
			# IF the app isn't exculsively trying to be advanced exactly 1 step, return
			# ELSE perform advancement
			
			// $denials = $this->checkAppHasDenials($appID, $currentStatus);

			// if($denials) {
				// return $denials;
			// } elseif
			// if(array_search($toStep, $this->approvalOrder) != false && 
			// 		 array_search($toStep, $this->approvalOrder) - array_search($currentStatus, $this->approvalOrder) == 1) {
			// 	$this->CI->db->query("
			// 		UPDATE Application
			// 		SET Status = '{$toStep}'
			// 		WHERE ApplicationID = {$appID}
			// 	");

			// 	//trigger email action?
			// } else {
			// 	return "You are attemping to advance an application more than one step or regress an application. This action is not supported";
			// }

			$this->CI->db->query("
				UPDATE Application
				SET Status = '{$toStep}'
				WHERE ApplicationID = {$appID}
			");
		}

		/**
		 * @param  int $appID the ID for the app we want to check
		 * @param  string $currentStatus The status we want to check denials for
		 * @return any[] An array of denials if there are some, FALSE otherwise
		 */
		public function checkAppHasDenials($appID, $currentStatus) {
			$denials = $this->CI->db->query("
				SELECT v.VenueID, Descision, DescisionRemark, ApprovalEndDate
				FROM Approval appr
					JOIN VenueUserRole vur ON vur.VenueUserRoleID = appr.VenueUserRoleID
					JOIN Venue v ON v.VenueID = vur.VenueID
					JOIN Application app ON app.ApplicationID = v.ApplicationID
				WHERE appr.ApprovalType = '{$currentStatus}'
					AND app.ApplicationID = {$appID}
					AND appr.Descision != 'approved'
			");

			return $denials->num_rows() > 0 ? $denials->result_array() : false;
		}


		# UNTESTED
		# Will create an approval for the given stage (type). Also sends an email out for each approval
		public function createApproval($venueID, $type) {
			
			# get all user roles for this venue with type $type
			 $vurs = $this->CI->db->query("
				SELECT vur.*
				FROM UserRole ur
					JOIN UserType ut ON ur.UserTypeID = ut.UserTypeID
					JOIN VenueUserRole vur ON vur.UserRoleID = ur.UserRoleID
					JOIN Venue v ON v.VenueID = vur.VenueID
				WHERE v.VenueID = {$venueID} AND UserTypeName = '{$type}'
			")->result_array();

			foreach($vurs as $vur) {
				//insert new approval for each userrole
				$insert = $this->CI->db->query("
					INSERT INTO Approval
						(ApprovalType, ApprovalStartDate, VenueUserRoleID, Descision)
					VALUES 
						((SELECT ut.UserTypeName
						 FROM UserType ut 
						 	JOIN UserRole ur ON ur.UserTypeID = ut.UserTypeID 
					 	 WHERE ur.UserRoleID = {$vur['UserRoleID']}),
						NOW(),
						{$vur['VenueUserRoleID']},
						'pending')
				");
				if(!$insert) {
					return $this->CI->db->error();
				}
				$approvalID = $this->CI->db->query("SELECT LAST_INSERT_ID() AS id")->row()->id;

				//$this->CI->mailer->attachActionsToApproval($approvalID, $type);
				
			//send email
			}

		}

		public function createApprovalsForOperator($venues, $userRoleID) {
			foreach($venues as $venue) {


				$this->CI->db->query("
					INSERT INTO UserRoleApplication(UserRoleID, ApplicationID)
						VALUES(
							{$userRoleID}, (
							SELECT a.ApplicationID FROM Application a
							JOIN Venue v ON v.ApplicationID = a.ApplicationID
							WHERE v.VenueID = {$venue['VenueID']}
							)
						)
				");

				$this->CI->db->query("
					INSERT INTO VenueUserRole(VenueID, UserRoleID)
					VALUES ({$venue['VenueID']}, {$userRoleID})
				");

				$vurID = $this->CI->db->query("SELECT LAST_INSERT_ID() AS id")->row()->id;

				echo($vurID);

				$this->CI->db->query("
					INSERT INTO Approval
						(ApprovalType, ApprovalStartDate, VenueUserRoleID, Descision)
					VALUES
						('VenueOperator', NOW(), {$vurID}, 'pending')
				");
			}
		}

		public function updateApproval($userRoleID, $venueID, $type, $signature, $descision, $descisionRemark = '') {
			$params = array(
				$signature,
				$descision,
				$descisionRemark,
				$userRoleID,
				$venueID,
				$type
			);
			$this->CI->db->query("
				UPDATE Approval
					SET ApprovalEndDate = NOW(),
					ApprovalSignature = ?,
					Descision = ?,
					DescisionRemark = ?
				WHERE VenueUserRoleID = 
						(SELECT VenueUserRoleID 
						 FROM VenueUserRole 
						 WHERE UserRoleID = ? AND VenueID = ?)
					AND ApprovalType = ?
					
			", $params);

			//send email 
		}

		public function updateApprovalByID($apprID, $signature, $decision, $decisionRemark = null) {
			$params = array(
				$signature,
				$decision,
				$decisionRemark,
				$apprID
			);

			$this->CI->db->query("
				UPDATE Approval
					SET ApprovalEndDate = NOW(),
					ApprovalSignature = ?,
					Descision = ?,
					DescisionRemark = ?
				WHERE ApprovalID = ?
					
			", $params);

		}

		public function getApproval($venueID, $adminRoleID, $type) {
			// TODO: figure out if ApprovalType table is necessary
			// $approval = $this->CI->db->query("
			// 	SELECT * FROM Approval a
			// 		JOIN Venue v ON v.VenueID = a.VenueID
			// 		JOIN UserRole ur ON ur.UserRoleID = a.UserRoleID
			// 		JOIN User u ON u.UserID = ur.UserID
			// 	WHERE ur.UserRoleID = {$adminRoleID}
			// 		AND v.VenueID = {$venueID}
			// 		AND a.ApprovalType = '{$type}'
			// ");

			$approval = $this->CI->db->query("
				SELECT * FROM Approval a
					JOIN VenueUserRole vur ON vur.VenueUserRoleID = a.VenueUserRoleID
				WHERE vur.UserRoleID = {$adminRoleID}
					AND vur.VenueID = {$venueID}
					AND a.ApprovalType = '{$type}'
			");
			return $approval->result_array();
		}

		public function getApprovalsForApp($appID) {
			// $approvals = $this->CI->db->query("
			// 	SELECT * FROM Approval appr
			// 		JOIN Venue v ON v.VenueID = appr.VenueID
			// 		JOIN Application a ON a.ApplicationID = v.ApplicationID
   //                  JOIN UserRole ur ON ur.UserRoleID = appr.UserRoleID
   //              WHERE a.ApplicationID = {$appID}

			// ");

			$approvals = $this->CI->db->query("
				SELECT * FROM Approval appr
					JOIN VenueUserRole vur ON vur.VenueUserRoleID = appr.VenueUserRoleID
					JOIN Venue v ON v.VenueID = vur.VenueID
					JOIN Application a ON a.ApplicationID = v.ApplicationID
                    JOIN UserRole ur ON ur.UserRoleID = vur.UserRoleID
                WHERE a.ApplicationID = {$appID}

			");

			return $approvals->result_array();
		}

		//public function get

		public function getApprovalsByType($appID, $approvalType) {
			$approvals = $this->CI->db->query("
				SELECT * FROM Approval appr
					JOIN VenueUserRole vur ON vur.VenueUserRoleID = appr.VenueUserRoleID
					JOIN Venue v ON v.VenueID = vur.VenueID
					JOIN Application a ON a.ApplicationID = v.ApplicationID
                    JOIN UserRole ur ON ur.UserRoleID = vur.UserRoleID
                WHERE a.ApplicationID = {$appID}
                	AND ApprovalType = '{$approvalType}'

			");

			return $approvals->result_array();
		}

		public function getOpenApprovals($appID) {
		$approvals = $this->CI->db->query("
			SELECT * FROM Approval appr
				JOIN VenueUserRole vur ON appr.VenueUserRoleID = vur.VenueUserRoleID
				JOIN Venue v ON v.VenueID = vur.VenueID
				JOIN Room r ON r.RoomID = v.RoomID
				JOIN Application a ON a.ApplicationID = v.ApplicationID
				JOIN ApplicationType at ON at.ApplicationTypeID = a.ApplicationTypeID
                JOIN UserRole ur ON ur.UserRoleID = vur.UserRoleID
                JOIN User u ON u.UserID = ur.UserID
            WHERE a.ApplicationID = {$appID}
            	AND appr.ApprovalEndDate is null

		");

			return $approvals->result_array();
		}

		public function getOpenApprovalsByType($appID, $type) {
					$approvals = $this->CI->db->query("
						SELECT * FROM Approval appr
							JOIN VenueUserRole vur ON appr.VenueUserRoleID = vur.VenueUserRoleID
							JOIN Venue v ON v.VenueID = vur.VenueID
							JOIN Room r ON r.RoomID = v.RoomID
							JOIN Application a ON a.ApplicationID = v.ApplicationID
							JOIN ApplicationType at ON at.ApplicationTypeID = a.ApplicationTypeID
		                    JOIN UserRole ur ON ur.UserRoleID = vur.UserRoleID
		                    JOIN User u ON u.UserID = ur.UserID
		                WHERE a.ApplicationID = {$appID}
		                	AND appr.Descision = 'pending'
		                	AND appr.ApprovalType = '{$type}'

					");

					return $approvals->result_array();
		}

		public function getOpenApprovalsByVenue($venueID, $type) {
						$approvals = $this->CI->db->query("
							SELECT * FROM Approval appr
								JOIN VenueUserRole vur ON appr.VenueUserRoleID = vur.VenueUserRoleID
								JOIN Venue v ON v.VenueID = vur.VenueID
								JOIN Room r ON r.RoomID = v.RoomID
								JOIN Application a ON a.ApplicationID = v.ApplicationID
								JOIN ApplicationType at ON at.ApplicationTypeID = a.ApplicationTypeID
			                    JOIN UserRole ur ON ur.UserRoleID = vur.UserRoleID
			                    JOIN User u ON u.UserID = ur.UserID
			                WHERE v.VenueID = {$venueID}
			                	AND appr.Descision = 'pending'
			                	AND appr.ApprovalType = '{$type}'

						");

						return $approvals->result_array();
		}

		public function checkAllVenuesDenied($appID) {
			$numVenues = $this->CI->db->query("
				SELECT count(*) FROM Venue v
					JOIN Application a ON v.ApplicationID = a.ApplicationID
					WHERE a.ApplicationID = {$appID}
			")->row_array();

			$numDenied = $this->CI->db->query("
				SELECT DISTINCT count(*) FROM Venue v
					JOIN Application a ON v.ApplicationID = a.ApplicationID
					JOIN VenueUserRole vur ON vur.VenueID = v.VenueID
					JOIN Approval appr ON appr.VenueUserRoleID = vur.VenueUserRoleID
					WHERE a.ApplicationID = {$appID}
					AND appr.Descision = 'Denied'
			")->row_array();

			return $numVenues == $numDenied;
		}

		public function getOpenAppsForUser($netID) {
			$approvals = $this->CI->db->query("
				SELECT * FROM Approval appr
					JOIN VenueUserRole vur ON appr.VenueUserRoleID = vur.VenueUserRoleID
					JOIN Venue v ON v.VenueID = vur.VenueID
					JOIN Room r ON r.RoomID = v.RoomID
					JOIN Application a ON a.ApplicationID = v.ApplicationID
					JOIN ApplicationType at ON at.ApplicationTypeID = a.ApplicationTypeID
                    JOIN UserRole ur ON ur.UserRoleID = vur.UserRoleID
                    JOIN User u ON u.UserID = ur.UserID
            	WHERE u.NetID = '{$netID}'
            		AND appr.ApprovalEndDate is null
        		GROUP BY a.ApplicationID

			");

			return $approvals->result_array();
		}

		public function voidApprovals($appID) {
			$openApprs = $this->getOpenApprovals($appID);
			foreach($openApprs as $appr) {
				if($appr['Descision'] == 'pending') {
					$this->updateApprovalByID($appr['ApprovalID'], 'SYSTEM ACTION', 'voided', '');
				}
			}
		}

	}

?>