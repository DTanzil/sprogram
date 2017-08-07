<?php
	
	class Admin_model extends CI_Model{
		function __construct(){
			$this->load->database();
		}
		
		# MODIFIED 2.14.17 by JSHILL
		# Get ID, name, NetID, status, and user type for all "admins" 
		# 	(admins, sponsors, venueOperators) for a given application
		# Return FALSE if no results
		# 	$applicationID: ID of application to search for
		function getAdminsByApplicationID($applicationID){
			//$this->getAdminByApplication($applicationID);
			$query = 
				$this->db->select(
					'user.UserID,
					user.UserFName,
					user.UserLName, 
					user.NetID,
					userrole.AdminIsActive,
					usertype.UserTypeName'
					)->
				from('user')->
				join('userrole', 'user.UserID = userrole.UserID')->
				join('userapplication', 'user.UserID = userapplication.UserID')->
				join('usertype', 'userrole.UserTypeID = usertype.UserTypeID')->
				join('application', 'userapplication.ApplicationID = application.ApplicationID')->
				where('usertype.UserTypeName !=', 'Applicant')->
				where('application.ApplicationID', $applicationID)->
			get();
			return ( $query->num_rows() > 0 ) ? $query->result_array() : false; 
		}
		
		# MODIFIED 2.14.17 by JSHILL
		# Get ID, name, NetID, status, and user type for all "admins" 
		# 	(admins, sponsors, venueOperators)
		# Return FALSE if no results
		function getAllAdmins(){
			$this->db->trans_start();
			$query =
				$this->db->distinct()->select(
					'user.UserID,
					user.UserFName,
					user.UserLName, 
					user.NetID,
					user.AdminIsActive'
					)->
				from('user')->
				join('userrole', 'user.UserID = userrole.UserID')->
				join('usertype', 'userrole.UserTypeID = usertype.UserTypeID')->
				where('usertype.UserTypeName !=', 'Applicant')->
				where("user.AdminDeletedAt IS NULL")->
			get();

			// TODO: expore using a computed column in the db for this for efficiency
			$admins = $query->result_array();
			foreach($admins as $i => $admin) {

				$rolesFromDB = 
					$this->db->select(
						'usertype.UserTypeName'
					)->
					from('UserType')->
					join('UserRole', 'usertype.UserTypeID = userrole.UserTypeID')->
					join('User', 'user.UserID = userrole.UserID')->
					where("user.UserID = {$admin['UserID']}")->
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
		# Get ID, name, NetID, status, and user type for an "admin"
		# 	(admins, sponsors, venueOperators)
		# Return FALSE if no results
		# $adminID: an adminID
		function getAdminByID($adminID){
			//$adminID = $this->db->escape($adminID);
			$query =
				$this->db->select(
					'user.UserID,
					user.UserFname,
					user.UserLname, 
					user.NetID,
					user.UserEmail,
					user.UserPhone,
					user.UserTitle,
					user.UWBox,
					user.AdminIsActive,
					user.AdminDeletedAt,
					usertype.UserTypeName,
					affiliation.AffiliationName'
					)->
				from('user')->
				join('userrole', 'user.UserID = userrole.UserID')->
				join('usertype', 'userrole.UserTypeID = usertype.UserTypeID')->
				join('affiliation', 'affiliation.AffiliationID = user.AffiliationID')->
				where('usertype.UserTypeName !=', 'Applicant')->
				where('user.UserID', $adminID)->
			get();


			$rolesFromDB = 
				$this->db->select(
					'usertype.UserTypeName'
				)->
				from('UserType')->
				join('UserRole', 'usertype.UserTypeID = userrole.UserTypeID')->
				join('User', 'user.UserID = userrole.UserID')->
				where('user.UserID', $adminID)->
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
			$query =
				$this->db->select(
					'user.UserID,
					user.UserFName,
					user.UserLName, 
					user.UserTitle,
					user.UserEmail,
					user.UserPhone'
					)->
				from('user')->
				where('user.NetID', $netid)->
			get();

			
			return ( $query->num_rows() > 0 ) ? $query->row_array() : false; 
		}
		

		/*
		
			TODO: This needs to be modified so that adminTypes is an array, not a single value
		 * DataKeys: "idContact", "netID", "isActive"
		 * adminType can be string or int.
		*/
		function addAdmin($data){
			echo 'addAdmin';
			$params = array(
				':fname' => $data['UserFname'],
				':lname' => $data['UserLname'],
				':netid' => $data['NetID'],
				':email' => $data['UserEmail'],
				':phone' => $data['UserPhone'],
				':affname' => $data['AffiliationName'],
				':title' => $data['UserTitle'],
				':box' => $data['UWBox']
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

				var_dump($result);
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
			echo 'editAdmin';
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
					'user.UserID,
					user.UserFName,
					user.UserLName, 
					user.NetID,
					user.UserTitle,
					user.UWBox,
					user.UserEmail,
					user.UserPhone,
					user.AdminIsActive,
					user.AdminDeletedAt,
					affiliation.AffiliationName,
					user.AdminIsActive,
					usertype.UserTypeName'
					)->
				from('user')->
				join('userrole', 'user.UserID = userrole.UserID')->
				join('usertype', 'userrole.UserTypeID = usertype.UserTypeID')->
				join('affiliation', 'user.AffiliationID = affiliation.AffiliationID')->
				where('user.NetID', $netid)->
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