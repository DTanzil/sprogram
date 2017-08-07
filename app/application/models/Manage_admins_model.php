<?php
class Manage_admins_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	# MODIFIED 2.14.17 by JSHILL
	# Get ID, name, NetID, status, and user type for all "admins" 
	# 	(admins, sponsors, venueOperators)
	# Return FALSE if no results
	function getAllAdmins(){

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
			join('usertype', 'userrole.UserTypeID = usertype.UserTypeID')->
			where('usertype.UserTypeName !=', 'Applicant')->
		get();
		
		return ( $query->num_rows() > 0 ) ? $query->result_array() : false; 
	}
}