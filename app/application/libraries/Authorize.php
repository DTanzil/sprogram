<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	/**
	 * CREATED 4/3/17 by JSHILL
	 * This library handles user authorization for viewing pages. Authentication is still handled by UW.

	*/	
	class Authorize {
		private $CI; # Codeigniter instance
		private $bypass; # Mimics the UW Auth service for developing locally

		# Store a reference to the CodeIgniter object, load database
		public function __construct() {
			$this->CI =& get_instance();
			$this->CI->load->database();
			$this->CI->load->library('session');

			$this->bypass = true; # uncomment this to use bypass

			if($this->bypass) {
				$_SERVER['REMOTE_USER'] = 'com';

				unset($this->CI->session->roles);
			}
			$this->authUser();
		}

		# Authorize a user to access a resource by determining their user
		# roles and compare that list to the given role authorization for that page
		#	$netid: the user's netid
		#	$role: The required role needed to access this page.
		#		   The default is 'Admin'
		public function authUser() {


			//var_dump($this->CI->session);


			$netidStored = $_SERVER['REMOTE_USER'];
			//print $netidStored;
			if(!$netidStored) {
				echo 'REMOTE_USER isnt set correctly. Dying';
				die;
			} 
			 if (!isset($_SESSION['roles'])) {
				$userRoles = $this->getUserRoles($netidStored);

				//var_dump($userRoles);


				$this->CI->session->roles = $userRoles;
			}
			//var_dump($this->CI->session);

			return isset($_SESSION['roles']) && sizeof($_SESSION['roles']) > 0;
		}

		public function hasRole($role) {
			if(!isset($_SESSION['roles'])) {
				$this->authUser();
			}
			//var_dump($this->CI->session->roles);
			return in_array($role, $this->CI->session->roles);
		}

		# Very small wrapper for getting the netid of the person logged in
		# Could become more useful if we changed from UWAuth 
		public function getNetid() {
			return isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : false;
		}

		# Gets the user's assigned roles from the database. If the user
		# has 0 roles assigned, returns false
		#	$netid: the user's netid
		private function getUserRoles() {

			$netid = $this->CI->db->escape($this->getNetid());
			$query =
			"	
			SELECT UserTypeName FROM UserType ut
				JOIN UserRole ur ON ut.UserTypeID = ur.UserTypeID
				JOIN User u ON u.UserID = ur.UserID
			WHERE u.netid = {$netid} AND AdminIsActive = 1
			";
			$results = $this->CI->db->query($query)->result_array();

			# Transform the results into a simpler array for later
			$roles = array();
			foreach($results as $result) {
				array_push($roles, $result['UserTypeName']);
			}

			return $roles;
		}

	}
?>