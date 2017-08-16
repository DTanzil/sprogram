<?php
class Manage_locations_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();

		$this->load->library('Approval');
	}

	/**
	 * Get a list of all rooms in all buildings
	 * @return any[] An array of locations
	 */
	public function getAllLocations() {
		$query = $this->db->select("
			Room.RoomID,
			RoomAbbr,
			RoomName,
			BuildingAbbr,
			Room.IsApproved,
			Building.MapURL,
			Operators")->
		from('Room')->
		join('Building', 'Building.BuildingID = Room.BuildingID')->
		join("
			(SELECT r.RoomID, GROUP_CONCAT(CONCAT(UserFname, ' ', UserLname)) AS Operators
			FROM User u
				JOIN UserRole ur ON u.UserID = ur.UserID
				JOIN UserRoom urm ON ur.UserRoleID = urm.UserRoleID
				JOIN Room r ON r.RoomID = urm.RoomID
			GROUP BY r.RoomID) AS other", 'other.RoomID = Room.RoomID', 'left')->
		get();

		return $query->result_array();
	}

	/**
	 * Create a new location
	 * @param  any[] $locationInfo an array of properties regarding a location
	 * @return int the newly created location's ID
	 */
	public function createLocation($locationInfo) {
		$params = array(
			$locationInfo['RoomName'],
			$locationInfo['RoomAbbr'],
			$locationInfo['RoomDesc'],
			$locationInfo['IsApproved'],
			$locationInfo['BuildingName']
		);
		$this->db->query("
			INSERT INTO Room 
				(RoomName, RoomAbbr, RoomDesc, IsApproved, BuildingID)
			VALUES 
				(?, ?, ?, ?, (SELECT BuildingID FROM Building WHERE BuildingName = ?));
			SELECT LAST_INSERT_ID();", $params);
		$newID = $this->db->query('SELECT LAST_INSERT_ID() AS id')->row()->id;
		return $newID;
	}

	/**
	 * Edit a location
	 * @param  any[] An array of properties we want to edit
	 */
	public function editLocation($locationInfo) {
		$params = array(
			$locationInfo['RoomName'],
			$locationInfo['RoomAbbr'],
			$locationInfo['RoomDesc'],
			$locationInfo['IsApproved'],
			$locationInfo['BuildingName'],
			$locationInfo['locationID']
		);
		$this->db->query("
			UPDATE Room
			SET RoomName = ?,
				RoomAbbr = ?,
				RoomDesc = ?,
				IsApproved = ?,
				BuildingID = (SELECT BuildingID FROM Building WHERE BuildingName = ?)
			WHERE RoomID = ?;
		", $params);
	}

	/**
	 * Delete a location
	 * @param  int $locationID the id of the location we want to delete
	 */
	public function deleteLocation($locationID) {
		$this->deleteAllOperatorsFromLocation($locationID);

		$locationID = $this->db->escape($locationID);
		$this->db->query("
			DELETE FROM Room WHERE RoomID = {$locationID};
		");
	}

	/**
	 * Get information for a location
	 * @param  int $locationID ID of the location we want to get
	 * @return any[] An array of properties regarding the given location
	 */
	public function getLocationByID($locationID) {
		$locationID = $this->db->escape($locationID);

		$query = $this->db->select('
			RoomID,
			RoomName,
			RoomAbbr,
			RoomDesc,
			IsApproved,
			BuildingName,

		')->
		from('Room')->
		join('Building', 'Room.BuildingID = Building.BuildingID')->
		where("RoomID = $locationID")->
		get();

		return $query->result_array()[0];
	}

	/**
	 * Get all buildings registered in the database
	 */
	public function getAllBuildings() {
		$query = $this->db->select('
			BuildingName,
			BuildingAbbr
		')->
		from('Building')
		->get();

		return $query->result_array();
	}

	/**
	 * Get all venue operators
	 * @return any[] An array of information for each venue operator
	 */
	public function getOperators() {
		$query = $this->db->select('
			User.UserID,
			UserFname,
			UserLname,
			NetID
		')->
		from('User')->
		join('UserRole', 'User.UserID = UserRole.UserID')->
		join('UserType', 'UserType.UserTypeID = UserRole.UserTypeID')->
		where("UserTypeName = 'VenueOperator'")
		->get();

		return $query->result_array();
	}

	/**
	 * Get a list of the venue operators assigned to a location
	 * @param  int $locationID the id of the location we want to get venue operators for
	 * @return any[] the venue operators
	 */
	public function getOperatorsForLocation($locationID) {
		$locationID = $this->db->escape($locationID);

		$query = $this->db->select('
			User.UserID,
			UserFname,
			UserLname,
			NetID
		')->
		from('User')->
		join('UserRole', 'UserRole.UserID = User.UserID')->
		join('UserRoom', 'UserRole.UserRoleID = UserRoom.UserRoleID')->
		where("UserRoom.RoomID = {$locationID}")
		->get();

		return $query->result_array();
	}

	/**
	 * Add any number of venue operators to a location
	 * @param any[] $admins An array of admin info
	 * @param int $locationID the id of the location we want to add venue operators to
	 */
	public function addOperatorsToRoom($admins, $locationID) {
		$locationID = $this->db->escape($locationID);

		foreach($admins as $admin) {
			$params = array(
				"UserID" => $admin,
				"RoomID" => $locationID
			);

			// $this->db->insert('UserRoom', $params);

			$this->db->query("
				INSERT INTO UserRoom (UserRoleID, RoomID)
				VALUES (
					(SELECT UserRoleID FROM UserRole ur
						JOIN UserType ut ON ur.UserTypeID = ut.UserTypeID
						JOIN User u ON u.UserID = ur.UserID
					WHERE UserTypeName = 'VenueOperator'
						AND ur.UserID = {$params['UserID']}),
					{$params['RoomID']} )
			");

			$venues = $this->db->query("
				SELECT DISTINCT v.VenueID FROM Venue v
				JOIN Room r ON r.RoomID = v.RoomID
				JOIN VenueUserRole vur ON vur.VenueID = v.VenueID
				LEFT JOIN Approval appr ON appr.VenueUserRoleID = vur.VenueUserRoleID
				WHERE r.RoomID = {$params['RoomID']}
					AND appr.ApprovalID IS NULL
					
			");

			echo 'venues query: ' . var_dump($venues);
			$venues = $venues->result_array();
			echo '<pre>Venues';
			var_dump($venues);
			echo '</pre>';

			# Add approvals to venues affected by this change so the admin can approve/deny locations in open applications
			# 



			$userRoleID = $this->db->query("SELECT UserRoleID FROM UserRole ur
						JOIN UserType ut ON ur.UserTypeID = ut.UserTypeID
						JOIN User u ON u.UserID = ur.UserID
					WHERE UserTypeName = 'VenueOperator'
						AND ur.UserID = {$params['UserID']}")->result_array()[0]['UserRoleID'];

			var_dump($userRoleID);

			$this->approval->createApprovalsForOperator($venues, $userRoleID);
		}
	}

	/**
	 * Remove all venue operators from a location
	 * @param  int $locationID the id of the location we want to purge venue operators for
	 */
	public function deleteAllOperatorsFromLocation($locationID) {
		$locationID = $this->db->escape($locationID);

		# remove pending approvals associated with venue operators at this location
		# 
		# 
		$this->db->query("
			DELETE FROM UserRoleApplication
			WHERE UserRoleID IN (
				SELECT ur.UserRoleID FROM UserRole ur
				JOIN UserType ut ON ut.UserTypeID = ur.UserTypeID
				JOIN VenueUserRole vur ON vur.UserRoleID = ur.UserRoleID
				JOIN Venue v ON v.VenueID = vur.VenueID
				JOIN Approval appr ON appr.VenueUserRoleID = vur.VenueUserRoleID
				JOIN Room r ON r.RoomID = v.RoomID
				WHERE r.RoomID = {$locationID}
					AND appr.Descision = 'pending'
					AND appr.ApprovalType = 'VenueOperator'
			)
			 AND ApplicationID IN (
			 	SELECT a.ApplicationID FROM Application a
					JOIN Venue v ON v.ApplicationID = a.ApplicationID
					JOIN Room r ON r.RoomID = v.RoomID
					WHERE r.RoomID = {$locationID}
			 )
		");

		$this->db->query("
			DELETE FROM Approval
			WHERE VenueUserRoleID IN
					(
					SELECT vur.VenueUserRoleID FROM VenueUserRole vur
						JOIN Venue v ON v.VenueID = vur.VenueID
						JOIN Room r ON r.RoomID = v.RoomID
						WHERE r.RoomID = {$locationID}
					)
				AND ApprovalType = 'VenueOperator'
		");		
		$this->db->query("
			DELETE FROM VenueUserRole
			WHERE VenueID IN 
				(
                SELECT v.VenueID 
                FROM Venue v 
                JOIN Room r ON r.RoomID = v.RoomID 
                WHERE r.RoomID = {$locationID})
			AND UserRoleID IN
				(
				SELECT ur.UserRoleID FROM UserRole ur
                JOIN UserRoom uroom ON uroom.UserRoleID = ur.UserRoleID
                JOIN Room r ON uroom.RoomID = r.RoomID
                WHERE r.RoomID = {$locationID}
                )
            AND VenueUserRoleID IN
            	(
            	SELECT appr.VenueUserRoleID FROM Approval appr
            	WHERE appr.descision = 'pending'
            		AND appr.ApprovalType = 'VenueOperator'
            	)
		");


		$this->db->query("
			DELETE FROM UserRoom
			WHERE RoomID = {$locationID}
		");

	}
}
?>