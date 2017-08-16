<?php
class Applications_model extends CI_Model {
	public function __construct() {
		parent::__construct();

		$this->load->database();
		//$this->load->library('approval');
		$this->load->library('mailer');
	}

	public function getAllApps() {
		$query = 
			$this->db->select(
				'ApplicationID,
				EventName,
				EventDesc,
				DateApplied,
				ApplicationTypeName,
				has_flag')
			->from('Application')
			->join('ApplicationType', 'Application.ApplicationTypeID = ApplicationType.ApplicationTypeID')
		->get();

		return $query->num_rows() > 0 ? $query->result_array() : false;
	}

	public function getAppsByStage($appStage) {
		$appStage = $this->db->escape($appStage);
		echo $appStage;
		if($appStage == "'venue'") {
			$apps = $this->db->query("
				SELECT a.ApplicationID, a.DateApplied, v.EventStartDate, a.EventName, r.RoomName, u2.UserFname, u2.UserLname, aff.AffiliationName, u1.NetID AS sponsor FROM Venue v
				JOIN Application a ON a.ApplicationID = v.ApplicationID
				JOIN Room r ON r.RoomID = v.RoomID
				JOIN UserRoleApplication ura1 ON a.ApplicationID = ura1.ApplicationID
                JOIN UserRoleApplication ura2 ON a.ApplicationID = ura2.ApplicationID
				JOIN UserRole ur1 ON ur1.UserRoleID = ura1.UserRoleID
                JOIN UserRole ur2 ON ur2.UserRoleID = ura2.UserRoleID
				JOIN UserType ut1 ON ut1.UserTypeID = ur1.UserTypeID
                JOIN UserType ut2 ON ut2.UserTypeID = ur2.UserTypeID
				JOIN User u1 ON u1.UserID = ur1.UserID
				JOIN User u2 ON u2.UserID = ur2.UserID
				JOIN Affiliation aff ON aff.AffiliationID = u2.AffiliationID
				WHERE a.Status = 'venue'
					AND ut1.UserTypeName = 'Sponsor'
                  AND ut2.UserTypeName = 'Applicant'
			");
			return $apps->result_array();
		} else {
			$apps = $this->db->query("
				SELECT * FROM Application a
				JOIN Venue v ON v.ApplicationID = a.ApplicationID
				JOIN UserRoleApplication ura ON a.ApplicationID = ura.ApplicationID
				JOIN UserRole ur ON ur.UserRoleID = ura.UserRoleID
				JOIN UserType ut ON ut.UserTypeID = ur.UserTypeID
				JOIN User u ON u.UserID = ur.UserID
				LEFT JOIN Affiliation aff ON aff.AffiliationID = u.UserID
				WHERE a.Status = {$appStage}
				AND UserTypeName = 'Applicant'
				
			");
			return $apps->result_array();
		}
	}

	public function getVenues($appID) {
		$appID = $this->db->escape($appID);

		// $venues = $this->db->query("
		// 	SELECT * FROM Venue v
		// 	JOIN Application a ON a.ApplicationID = v.ApplicationID
		// 	JOIN Room r ON r.RoomID = v.RoomID
		// 	JOIN Building b ON b.BuildingID = r.BuildingID
		// 	JOIN UserRoom uroom ON uroom.RoomID = r.RoomID
		// 	JOIN UserRole ur ON ur.UserRoleID = uroom.UserRoleID
		// 	JOIN User u ON u.UserID = ur.UserID
		// 	JOIN VenueUserRole vur ON vur.VenueID = v.VenueID
		// 	JOIN Approval appr ON appr.VenueUserRoleID = vur.VenueUserRoleID
		// 	WHERE a.ApplicationID = {$appID}
		// 	AND appr.ApprovalType = 'VenueOperator'
		// ");
		// 
		// 
		# Fairly certain this is going to break stuff on the details end...
		$venues = $this->db->query("
			SELECT * FROM Venue v
			JOIN Application a ON a.ApplicationID = v.ApplicationID
			WHERE a.ApplicationID = {$appID}
		");

		return $venues->result_array();
	}

	public function getVenueDetails($appID) {
		$appID = $this->db->escape($appID);

		$venues = $this->db->query("
			SELECT *, GROUP_CONCAT(CONCAT(u.UserFName, ' ', u.UserLname)) AS Operators
			FROM Venue v 
			JOIN Application a 
				ON a.ApplicationID = v.ApplicationID 
			JOIN Room r 
				ON r.RoomID = v.RoomID 
			JOIN Building b 
				ON b.BuildingID = r.BuildingID 
			JOIN VenueUserRole vur 
				ON vur.VenueID = v.VenueID 
			JOIN UserRole ur 
				ON ur.UserRoleID = vur.UserRoleID 
			JOIN User u 
				ON u.UserID = ur.UserID 
			JOIN Approval appr 
				ON appr.VenueUserRoleID = vur.VenueUserRoleID 

			WHERE a.ApplicationID = {$appID}
				AND appr.ApprovalType = 'VenueOperator'
			GROUP BY v.VenueID
		");


		return $venues->result_array();
	}

	public function updateFlag($AppID, $flag) {
		$AppID = $this->db->escape($AppID);
		$flag = $this->db->escape($flag);

		$query = 
			"UPDATE Application
			SET has_flag = {$flag}
			WHERE ApplicationID = {$AppID}
			";
		$this->db->query($query);

		$return = "SELECT has_flag FROM Application WHERE ApplicationID = {$AppID}";
		$results = $this->db->query($return);

		return $results->row();
	}

	public function createApp($app, $admins, $locations, $applicant) {

		//$this->db->trans_start();

		$params = array(
			$app['ApplicationType'],
			$app['EventName'],
			$app['EventDesc'],
			$app['ProminantAttendees'],
			$app['TotalAttendees'],
			$app['IsPublic'],
			$app['Alcohol'],
			$app['AmpSound'],
			$app['AmpSoundDesc'],
			$app['FoodPermit'],
			$app['IsPolitical'],
			$app['GatehouseParking'],
			$app['ComuterServicesParking'],
			$app['BusParking'],
			$app['RegFeeAmount'],
			$app['HasDonations'],
			$app['DonationDesc'],
			$app['Security'],
		);
		$insert = $this->db->query("
			INSERT INTO Application
				(ApplicationTypeID, EventName, EventDesc, ProminantAttendees, TotalAttendees, DateApplied, IsPublic, Alcohol, AmpSound, AmpSoundDesc, FoodPermit, IsPolitical, GatehouseParking, ComuterServicesParking, BusParking, RegFeeAmount, HasDonations, DonationDesc, Security)
			VALUES 
				((SELECT ApplicationTypeID FROM ApplicationType WHERE ApplicationTypeName = ?), ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
		", $params); 

		$id = $this->db->insert_id();
		//echo $id;
		if(!$insert) {
			return $this->db->error();
		}

		$this->mailer->attachActionsToApp($id);

		
		# to attach to venues
		# NO Venue Operators
		foreach($admins as $admin) {
			$urID = $this->addAdminToApp($id, $admin);
		}

		# Operators added here
		foreach($locations as $venue) {
			$venueID = $this->addVenueToApp($id, $venue);
			//create approval
		}

		# add applicant here
		$this->addApplicant($id, $applicant);

		//$this->db->trans_complete();
		return $id;

	}

	public function addApplicant($appID, $data) {
		$params = array(
			$data['AffiliationName']
		);
		$affiliation = $this->db->query("
			SELECT AffiliationID FROM Affiliation
			WHERE AffiliationName = ?
		", $params)->result_array()[0];
		if(!$affiliation) {
			array_push($params, $data['AffTypeName']);
			$this->db->query("
				CALL uspNewAffiliation(?, null, null, ?, null, null, null, @newID);
			", $params);
			$affID = $this->db->query("SELECT @newID as id")->row()->id;
		} else {
			$affID = $affiliation['AffiliationID'];
		}
		echo '<p>aff ID: ' . $affID . '</p>';

		$params = array(
			$affID,
			$data['UserFname'],
			$data['UserLname'],
			$data['UserEmail'],
			$data['UserPhone']
		);
		$insert = $this->db->query("
			INSERT INTO User
				(AffiliationID, UserFname, UserLname, UserEmail, UserPhone)
			VALUES
				(?, ?, ?, ?, ?)
		", $params);
		$userID = $this->db->insert_id();

		$this->db->query("
			CALL uspAddRoleToAdmin({$userID}, 'Applicant');
		");

		$insert = $this->db->query("
			INSERT INTO UserRoleApplication
				(UserRoleID, ApplicationID)
			VALUES 
				((SELECT UserRoleID FROM UserRole WHERE UserID = {$userID}), {$appID});
		");
	}

	public function addVenueToApp($appID, $data) {
		$params = array(
			$data["RoomName"],
			$data['BuildingName']
		);

		# Get the venue operator(s) assigned to this room
		$operators = $this->db->query("
			SELECT ur.UserRoleID, u.NetID, ut.UserTypeName FROM UserRole ur
				JOIN User u ON u.UserID = ur.UserID
				JOIN UserRoom ro ON ro.UserRoleID = ur.UserRoleID
				JOIN UserType ut ON ut.UserTypeID = ur.UserTypeID
				JOIN Room r ON r.RoomID = ro.RoomID
				JOIN Building b ON b.BuildingID = r.BuildingID
			WHERE r.RoomName = ? AND b.BuildingAbbr = ?", $params)->result_array();

		var_dump($operators);

		$sponsors = $this->getUsersForApp($appID)['Sponsor'];
		$committees = $this->getUsersForApp($appID)['Committee'];
		foreach($sponsors as $sponsor) {
			$operators[] = $sponsor;
		}
		foreach($committees as $committee) {
			$operators[] = $committee;
		}
		// echo '<p>operators</p>';
		// echo '<pre>';
		// var_dump($operators);
		// echo '</pre>';

		# create venue
		 $params = array (
				$data['BuildingName'],
				$data["RoomName"],
				$data['EventStartDate'],
				$data['EventEndDate']
			);
		$insert = $this->db->query("
				INSERT INTO Venue
					(ApplicationID, RoomID, EventStartDate, EventEndDate)
				VALUES
					({$appID}, (SELECT RoomID FROM Room r JOIN Building b ON b.BuildingID = r.BuildingID WHERE BuildingAbbr = ? AND r.RoomName = ?), ?, ?)
			", $params);
		if(!$insert) {
			return $this->db->error();
		}
		$venueID = $this->db->query("SELECT LAST_INSERT_ID() AS id")->row()->id;

		# Connect every user in a userrole to this venue
		foreach($operators AS $operator) {
			# add the venue operator to the list of admins associated with this app
			# 
			if($operator['UserTypeName'] == 'VenueOperator') {
				$this->addAdminToApp($appID, array("netid" => $operator['NetID'], "UserTypeName" => $operator['UserTypeName']));

			}

			$this->db->query("
				INSERT INTO VenueUserRole (VenueID, UserRoleID)
				VALUES ({$venueID}, {$operator['UserRoleID']})
			");
		}
		$this->approval->createApproval($venueID, 'VenueOperator');
		$this->approval->createApproval($venueID, 'Sponsor');
		$this->approval->createApproval($venueID, 'Committee');

		return $venueID;
	}

	public function addAdminToApp($appID, $data) {
		$params = array(
			$data['netid'],
			$data['UserTypeName']
		);

		$urID = $this->db->query("
			SELECT ur.UserRoleID FROM UserRole ur 
				JOIN User u ON u.UserID = ur.UserID 
				JOIN UserType ut ON ur.UserTypeID = ut.UserTypeID
		  WHERE u.NetID = ? 
		  		AND ut.UserTypeName = ?
  		", $params)->row();
  		if($urID) {
  			$urID = $urID->UserRoleID;

			$insert = $this->db->query("
				INSERT INTO UserRoleApplication
					(UserRoleID, ApplicationID)
				VALUES 
					({$urID}, {$appID});
			");

			if(!$insert) {
				return $this->db->error();
			}
			return $urID;
		}
	}

	public function getAdminsForApp($appID, $userType) {
		$admins = $this->db->query("
			SELECT ur.UserRoleID, u.UserEmail, u.NetID FROM User u
			JOIN UserRole ur ON ur.UserID = u.UserID
			JOIN UserRoleApplication ura ON ura.UserRoleID = ur.UserRoleID
			JOIN UserType ut ON ut.UserTypeID = ur.UserTypeID
			WHERE ura.ApplicationID = {$appID}
			AND UserTypeName = '{$userType}';
		");
		//var_dump($admins->result_array());
		return $admins->result_array();
	}

	public function getUsersForApp($appID) {
		$admins = $this->db->query("
			SELECT ut.UserTypeName, ur.UserRoleID, u.UserFname, u.UserLname, u.UserEmail, u.NetID, a.AffiliationName, a.Street, a.City, a.StateProvince, a.Country, a.Zip, at.AffiliationTypeName, u.UserPhone, u.UWBox FROM User u
			JOIN UserRole ur ON ur.UserID = u.UserID
			JOIN UserRoleApplication ura ON ura.UserRoleID = ur.UserRoleID
			JOIN UserType ut ON ut.UserTypeID = ur.UserTypeID
			JOIN Affiliation a ON a.AffiliationID = u.AffiliationID
			JOIN AffiliationType at ON a.AffiliationTypeID = at.AffiliationTypeID
			WHERE ura.ApplicationID = {$appID};
		")->result_array();

		//Assign each results array key as the user type and flatten
		// $admins = array_map(function($user) {
		// 	return array(
		// 		$user['UserTypeName'] => $user
		// 	);
		// }, $admins);
		// $flattened = array();
		// foreach($admins as $key => $value) {
		// 	$nkey = array_keys($value)[0];
		// 	$flattened[$nkey] = $value[$nkey];
		// }
		 
		$transformed = array(
			"Sponsor" => array(),
			"VenueOperator" => array(),
			"Committee" => array(),
			"Applicant" => array()
		);
		foreach($admins as $admin) {
			# get the value of UserTypeName to sort each admin into the above arrays
			$userTypeIndex =  array_search("UserTypeName", array_keys($admin));
			$key = $admin[array_keys($admin)[$userTypeIndex]];
			$transformed[$key][] = $admin;

		}

		return $transformed;
	}

	public function getDetailsForApp($appID) {
		$appID = $this->db->escape($appID);

		$app = $this->db->query("
			SELECT *
			FROM Application a
			JOIN Venue v ON a.ApplicationID = v.ApplicationID
			JOIN ApplicationType at ON at.ApplicationTypeID = a.ApplicationTypeID
			WHERE a.ApplicationID = {$appID}
			LIMIT 1
		")->result_array()[0];
		return $app;
	}

	public function getEmailRecords($appID) {
		$appID = $this->db->escape($appID);

		$emails = $this->db->query("
			SELECT EmailRecordID, EmailRecordDate, EmailTemplateName, UserEmail
			FROM EmailRecord er
            JOIN UserRole ur ON er.UserRoleID = ur.UserRoleID
            JOIN User u ON ur.UserID = u.UserID
			JOIN EmailTemplate et ON et.EmailTemplateID = er.EmailTemplateID
			WHERE er.ApplicationID = {$appID}
				OR er.ApprovalID IN 
					(SELECT ApprovalID FROM Approval appr
						JOIN VenueUserRole vur ON vur.VenueUserRoleID = appr.VenueUserRoleID
						JOIN Venue v ON v.VenueID = vur.VenueID
						JOIN Application a ON a.ApplicationID = v.ApplicationID
					 WHERE a.ApplicationID = {$appID})
		")->result_array();
		return $emails;
	}

	public function getNotes($appID) {
		$appID = $this->db->escape($appID);

		$notes = $this->db->query("
			SELECT *
			FROM Note n
            JOIN Application a ON n.ApplicationID = a.ApplicationID
			WHERE a.ApplicationID = {$appID}
		")->result_array();
		return $notes;
	}

	private function flatten(array $array) {
	    $return = array();
	    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
	    return $return;
	}
}