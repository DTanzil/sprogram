<?php
	
	class Admin_model extends CI_Model{
		function __construct(){
			$this->load->database();
		}
		
		# MODIFIED 2.14.17 by JSHILL
		# Get ID, name, NetID, status, and User type for all "admins" 
		# 	(admins, sponsors, venueOperators) for a given Application
		# Return FALSE if no results
		# 	$ApplicationID: ID of Application to search for
		function getAdminsByApplicationID($ApplicationID){
			//$this->getAdminByApplication($ApplicationID);
			$query = 
				$this->db->select(
					'User.UserID,
					User.UserFName,
					User.UserLName, 
					User.NetID,
					UserRole.AdminIsActive,
					UserType.UserTypeName'
					)->
				from('User')->
				join('UserRole', 'User.UserID = UserRole.UserID')->
				join('UserApplication', 'User.UserID = UserApplication.UserID')->
				join('UserType', 'UserRole.UserTypeID = UserType.UserTypeID')->
				join('Application', 'UserApplication.ApplicationID = Application.ApplicationID')->
				where('UserType.UserTypeName !=', 'Applicant')->
				where('Application.ApplicationID', $ApplicationID)->
			get();
			return ( $query->num_rows() > 0 ) ? $query->result_array() : false; 
		}
		
		# MODIFIED 2.14.17 by JSHILL
		# Get ID, name, NetID, status, and User type for all "admins" 
		# 	(admins, sponsors, venueOperators)
		# Return FALSE if no results
		function getAllAdmins(){
			$this->db->trans_start();
			$query =
				$this->db->distinct()->select(
					'User.UserID,
					User.UserFName,
					User.UserLName, 
					User.NetID,
					User.AdminIsActive'
					)->
				from('User')->
				join('UserRole', 'User.UserID = UserRole.UserID')->
				join('UserType', 'UserRole.UserTypeID = UserType.UserTypeID')->
				where('UserType.UserTypeName !=', 'Applicant')->
				where("User.AdminDeletedAt IS NULL")->
			get();

			// TODO: expore using a computed column in the db for this for efficiency
			$admins = $query->result_array();
			foreach($admins as $i => $admin) {

				$rolesFromDB = 
					$this->db->select(
						'UserType.UserTypeName'
					)->
					from('UserType')->
					join('UserRole', 'UserType.UserTypeID = UserRole.UserTypeID')->
					join('User', 'User.UserID = UserRole.UserID')->
					where("User.UserID = {$admin['UserID']}")->
				get();

				$rolesTransformed = "";

				foreach($rolesFromDB->result_array() as $key => $value) {
					$rolesTransformed .= $value['UserTypeName'] . ' ';
				}
				$admins[$i]['privileges'] = $rolesTransformed;
			}
			$this->db->trans_complete();
			
			return ( $query->num_rows() > 0 ) ? $admins : false; 
		}
		
		# MODIFIED 2.14.17 by JSHILL
		# Get ID, name, NetID, status, and User type for an "admin"
		# 	(admins, sponsors, venueOperators)
		# Return FALSE if no results
		# $adminID: an adminID
		function getAdminByID($adminID){
			//$adminID = $this->db->escape($adminID);
			$query =
				$this->db->select(
					'User.UserID,
					User.UserFname,
					User.UserLname, 
					User.NetID,
					User.UserEmail,
					User.UserPhone,
					User.UserTitle,
					User.UWBox,
					User.AdminIsActive,
					User.AdminDeletedAt,
					UserType.UserTypeName,
					Affiliation.AffiliationName'
					)->
				from('User')->
				join('UserRole', 'User.UserID = UserRole.UserID')->
				join('UserType', 'UserRole.UserTypeID = UserType.UserTypeID')->
				join('Affiliation', 'Affiliation.AffiliationID = User.AffiliationID')->
				where('UserType.UserTypeName !=', 'Applicant')->
				where('User.UserID', $adminID)->
			get();


			$rolesFromDB = 
				$this->db->select(
					'UserType.UserTypeName'
				)->
				from('UserType')->
				join('UserRole', 'UserType.UserTypeID = UserRole.UserTypeID')->
				join('User', 'User.UserID = UserRole.UserID')->
				where('User.UserID', $adminID)->
			get();

			$rolesTransformed = array();
			foreach($rolesFromDB->result_array() as $key => $value) {
				array_push($rolesTransformed, $value['UserTypeName']);
			}

			$return = $query->result_array()[0];
			$return['privileges'] = $rolesTransformed;
			
			return ( $query->num_rows() > 0 ) ? $return : false; 
		}


		function getContact($netid){
			$query = $this->db->query("
				SELECT * FROM User
				WHERE NetID = '{$netid}'
			");

			
			return ( $query->num_rows() > 0 ) ? $query->row_array() : false; 
		}
		

		/*
		
			TODO: This needs to be modified so that adminTypes is an array, not a single value
		 * DataKeys: "idContact", "netID", "isActive"
		 * adminType can be string or int.
		*/
		function addAdmin($data){
			$params = array(
				':fname' => $data['UserFname'],
				':lname' => $data['UserLname'],
				':netid' => $data['NetID'],
				':email' => $data['UserEmail'],
				':phone' => $data['UserPhone'],
				':affname' => $data['AffiliationName'],
				':title' => $data['UserTitle'],
				':box' => $data['UWBox'],
				':active' => $data['AdminIsActive']
			);
			$insertQuery = 
				"CALL uspNewAdmin(
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					@newid
				);
			";
			$selectId = "SELECT @newid;";
			
			$this->db->trans_start();
				$this->db->query($insertQuery, $params);
				$id = $this->db->query($selectId)->row()->{"@newid"};
				
				foreach($data['privileges'] as $privilege) {
					$role = "CALL uspAddRoleToAdmin({$id}, '{$privilege}')";
					$result = $this->db->query($role);
				}
			$this->db->trans_complete();
			return 'inserted';
			
		}

		function editAdmin($adminInfo) {
			$params = array(
				':fname' => $adminInfo['UserFname'],
				':lname' => $adminInfo['UserLname'],
				':email' => $adminInfo['UserEmail'],
				':phone' => $adminInfo['UserPhone'],
				':active' => $adminInfo['AdminIsActive'],
				':id' => $adminInfo['UserID']
			);
			$this->db->trans_start();
			$this->db->query(
				"UPDATE User
				SET UserFname = ?,
				UserLname = ?,
				UserEmail = ?,
				UserPhone = ?,
				AdminIsActive = ?,
				AdminDeletedAt = null
				WHERE UserID = ?;", $params);

			$params = array(
				':id' => $adminInfo['UserID'],
				':affname' => $adminInfo['AffiliationName'],
			);

			$this->db->query(
				"CALL uspUpdateAffiliationFor(?, ?, 1);", $params);

			$params = array(
				':id' => $adminInfo['UserID'],
			);
			$this->db->query(
				"DELETE FROM UserRole
				WHERE UserID = ?;", $params);

			if(isset($adminInfo['privileges'])) {
				foreach($adminInfo['privileges'] as $privilege) {
					$params = array(
						':id' => $adminInfo['UserID'],
						':priv' => $privilege,
					);
					$role = "CALL uspAddRoleToAdmin(?, ?)";
					$result = $this->db->query($role, $params);
				}
			}
			$this->db->trans_complete();
		
		}
		
		
		function deleteAdmin($adminID){
			$adminID = $this->db->escape($adminID);
			
			return $this->db->query("
				UPDATE User 
				SET AdminDeletedAt = NOW(),
				AdminIsActive = 0 
				WHERE UserID = {$adminID}
			");
		}
		
		# MODIFIED 2.16.17 by JSHILL
		# Get id, name, netid, title, box number, email, phone, UW department, status, and admin type
		#	for an "admin" for a given netid
		# netid: the given netid for an admin
		//Returns admin info associated with this netid
		public function getAdminByNetid($netid){
			$query =
				$this->db->select(
					'User.UserID,
					User.UserFName,
					User.UserLName, 
					User.NetID,
					User.UserTitle,
					User.UWBox,
					User.UserEmail,
					User.UserPhone,
					User.AdminIsActive,
					User.AdminDeletedAt,
					Affiliation.AffiliationName,
					User.AdminIsActive,
					UserRole.UserRoleID,
					UserType.UserTypeName'
					)->
				from('User')->
				join('UserRole', 'User.UserID = UserRole.UserID')->
				join('UserType', 'UserRole.UserTypeID = UserType.UserTypeID')->
				join('Affiliation', 'User.AffiliationID = Affiliation.AffiliationID')->
				where('User.NetID', $netid)->
			get();

			return ( $query->num_rows() > 0 ) ? $query->result_array()[0] : false;
		
		}		
		
		public function addType($idType, $idAdmin){

		}
		
		public function removeType($idType, $idAdmin){

		}
		
		public function updateType($idTypeRelation, $idNewType){

		}
		
		public function getTypesForAdmin($idAdmin){

		}
		
		public function getAdminTypes(){

		}
		
		
		public function getAdminTypeId($name){

		}
		
		public function getAdminsByType($idType, $order = "netid"){

		}
		
		public function getAdvisors(){

		}
		

		

	}
?>