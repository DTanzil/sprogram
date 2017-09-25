<?php
/**
 * Handles views for applications
 * 
 */
class Applications extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('Applications_model');
		$this->load->model('Admin_model');

		$this->load->library('authorize');
		//$this->load->library('session');
		$this->load->library('approval');
		$this->load->library('Mailer');

		$this->load->helper('assets');
		$this->load->helper('html');
		$this->load->helper('url');
	}

	/**
	 * Show all apps, across all approval states
	 */
	public function index() {
		$head['title'] = "Manage Applications";

		$header['mode'] = 'Application';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		$content['apps'] = $this->Applications_model->getPendingApps();
		//echo "manage_applications/{$viewType}.php";
		$content['view'] = "manage_applications/index.php";
		$content['description'] = "Manage Applications";

		$js = array('views/pagination.js');
		$css = array('views/pagination.css');
		$head['pageDependencies'] = getPageDependencies($js, $css);

		$sidebar = $this->Admin_model->getContact($this->authorize->getNetid());

		$data['head'] = $head;
		$data['header'] = $header;
		$data['content'] = $content;
		$data['sidebar'] = $sidebar;

		$this->security->xss_clean($data);
		$this->load->view('main', $data);

	}

	private function getContentForViewType($viewType) {
		$content['apps'] = $this->Applications_model->getAppsByStage($viewType);
		echo "manage_applications/{$viewType}.php";
		$content['view'] = "manage_applications/{$viewType}.php";
		$content['description'] = "Manage $viewType Applications";

		return $content;
	}

	public function view($appID) {

		$head['title'] = "Manage Applications";

		$header['mode'] = 'Application';
		$header['view'] = 'templates/header.php';
		$header['user'] = $this->Admin_model->getContact($this->authorize->getNetid());

		$content['netid'] = $this->authorize->getnetid();
		$content['details'] = $this->Applications_model->getDetailsForApp($appID);
		$content['venues'] = $this->Applications_model->getVenueDetails($appID);
		$content['users'] = $this->Applications_model->getUsersForapp($appID);
		$content['approvals']['sponsor'] = $this->approval->getApprovalsByType($appID, 'Sponsor');
		$content['approvals']['venue'] = $this->approval->getApprovalsByType($appID, 'VenueOperator');
		$content['approvals']['committee'] = $this->approval->getApprovalsByType($appID, 'Committee');
		$content['emails'] = $this->Applications_model->getEmailRecords($appID);
		$content['notes'] = $this->Applications_model->getNotes($appID);
		$content['view'] = "manage_applications/view/details.php";
		$content['description'] = "View Application Details";
		$content['openApprs'] = $this->approval->getOpenApprovals($content['details']['ApplicationID']);

		$js = array('views/pagination.js', 'views/manage_applications/details.js');
		$css = array('views/pagination.css', 'views/manage_applications/details.css');
		$head['pageDependencies'] = getPageDependencies($js, $css);

		$sidebar = $this->Admin_model->getContact('jshill');

		$data['head'] = $head;
		$data['header'] = $header;
		$data['content'] = $content;
		$data['sidebar'] = $sidebar;

		$this->security->xss_clean($data);
		$this->load->view('main', $data);
	}

	public function create() {
		file_put_contents(dirname(__FILE__) . '/log.txt', "******* New UUF ******\r\n", FILE_APPEND);
		$data = $_POST;
		if(!empty($data)) {
			file_put_contents(dirname(__FILE__) . '/log.txt', print_r($data, true), FILE_APPEND);
		}
	}

	/**
	 * AJAX endpoint
	 * Accepts $flag and $id paramters from an AJAX call
	 * Toggles the 'flagged' status of an application
	 * returns the application's 'flagged' status via JSON
	 */
	public function updateFlag() {
		$flag = $this->input->post('flag');
		$id = $this->input->post('id');
		$updatedFlag = $this->Applications_model->updateFlag($id, $flag);

		header("Content-Type:application/json"); 
		echo json_encode($updatedFlag->has_flag);
	}

	public function venueDecision() {
		header('Access-Control-Allow-Origin: *');

		$approvalID = $this->input->post('approvalID');
		$decision = $this->input->post('decision');
		$appID = $this->input->post('appID');
		$netID = $this->input->post('netID');
		$venueID = $this->input->post('venueID');

		$confirmation = $this->approval->updateVenueDecision($approvalID, $netID, $decision);


		# In the case where multiple operators are assigned to a venue, find their approvals and update them
		# to indicate another Venue Operator took an action on this venue
		$openVenueApprs = $this->approval->getOpenApprovalsByVenue($venueID, 'VenueOperator');
		foreach($openVenueApprs as $appr) {
			$this->approval->updateVenueDecision($appr['ApprovalID'], $netID, 'decided by other operator');
		}

		# If all venues have been decided upon, one way or the other, advance to Committee
		$openVenues = $this->approval->getOpenApprovalsByType($appID, 'VenueOperator');
		if(sizeof($openVenues) == 0) {
				$this->approval->advanceApplication($appID, 'committee');
		}

		header('Content-Type: application/json');
		echo json_encode($confirmation);
	}

	public function sponsorDecision() {
		//header('Access-Control-Allow-Origin: *');

		$approvalID = $this->input->post('approvalID');
		$decision = $this->input->post('decision');
		$appID = $this->input->post('appID');
		$netID = $this->input->post('netID');

		$confirmation = $this->approval->updateSponsorDecision($appID, $netID, $decision);

		if($decision == 'denied') {
			$this->approval->setStatus($appID, 'denied', $expReason);
			$this->approval->voidApprovals($appID);
		} else {
			$this->approval->advanceApplication($appID, 'venue');
		}

		//header('Content-Type: application/json');
		echo json_encode($confirmation);
	}

	public function committeeDecision() {
		//header('Access-Control-Allow-Origin: *');

		//$approvalID = $this->input->post('approvalID');
		$decision = $this->input->post('decision');
		$appID = $this->input->post('appID');

		$confirmation = $this->approval->updateCommitteeDecision($appID, 'jshill', $decision);

		if($decision == 'denied') {
			$this->approval->setStatus($appID, 'denied');
		} else {
			$this->approval->setStatus($appID, 'approved');
		}

		//header('Content-Type: application/json');
		echo json_encode($confirmation);
	}

	public function appDecision() {
		
	}

	public function sendEmail() {
		$rec = $_POST['rec'];
		$template = $_POST['template'];
		$appID = $_POST['appID'];

		$this->mailer->resend($rec, $template, $appID);
	}

	public function getTemplates() {
		$appID = $_POST['appID'];
		echo json_encode($this->mailer->getTemplates($appID));
	}

	public function sendReminder() {
		$appID = $_POST['appID'];
		$rec = $_POST['rec'];
		$template = $_POST['template'];

		$this->mailer->resend($rec, $template, $appID);
	}

	public function createNote() {
		$appID = $_POST['appID'];
		$noteText = $_POST['noteText'];

		$this->Applications_model->createNote($appID, $noteText);
	}

	public function expireApp() {
		$appID = $_POST['appID'];
		$expReason = $_POST['expReason'];

		$this->approval->setStatus($appID, 'expired', $expReason);
		$this->approval->voidApprovals($appID);
	}

	public function inactivate() {
		$appID = $_POST['appID'];
		$inactiveReason = $_POST['inactiveReason'];

		$this->approval->setStatus($appID, 'inactive', $inactiveReason);
		$this->approval->voidApprovals($appID);
	}

	public function test() {
		echo '<p>Creating App</p>';
		//create app
		//FOR UUF
		//applicant
		//	fname
		//	lname
		//	email
		//	phone
		//	? uwbox
		//org/aff
		//	aff-name
		//	street
		//	city
		//	state
		//	zip
		//	aff-type
		//		? advisor (IF RSO)
		//sponsor
		//	name
		//event
		//	name
		//	desc
		//	? prominant attendees
		//locations (foreach)
		//	blg name
		//	room name
		//	start
		//	end
		//requirements
		//	? public
		//	? asr
		//	? sound
		//		? sound desc
		//	? food
		//		? permit-status
		//	? political
		//	? parking
		//		? gatehouse
		//		? commuter services
		//		? bus parking
		//	? reg fee
		//		? amount
		//	? donations
		//			? donations desc
		//	? security
		//? sig
		//	
		$data = array(
			"Application" => array(
				"ApplicationType" => "uuf",
				"EventName" => "Test App",
				"EventDesc" => "Testing Testing, 1 2 3",
				"ProminantAttendees" => "Snoop Dogg",
				"TotalAttendees" => "",
				"IsPublic" => "1",
				"Alcohol" => "0",
				"AmpSound" => "1",
				"AmpSoundDesc" => "It lit",
				"FoodPermit" => "",
				"IsPolitical" => "0",
				"GatehouseParking" => "0",
				"ComuterServicesParking" => "1",
				"BusParking" => "0",
				"RegFeeAmount" => "100",
				"HasDonations" => "1",
				"DonationDesc" => "Donate to this awesome thing",
				"Security" => "0" 
			),
			"Admins" => array(
				array("netid" => "tpa2", "UserTypeName" => "Sponsor"),
				array("netid" => "com", "UserTypeName" => "Committee"),
				array("netid" => "", "UserTypeName" => "Advisor")
			),
			"Locations" => array(
				array("RoomName" => "120", "BuildingName" => "ACC", "EventStartDate" => "2017-03-01", "EventEndDate" => "2017-03-02"),
				array("RoomName" => "012", "BuildingName" => "ACC", "EventStartDate" => "2017-03-01", "EventEndDate" => "2017-03-02"),
			),
			"Applicant" => array(
				"UserFname" => "Stan",
				"UserLname" => "The Man",
				"UserEmail" => "applicant@test.com",
				"UserPhone" => "123456789",
				"UWBox" => "",
				//If not a UW Dept, we need address, otherwise we can look it up
				"AffiliationTypeName" => "UW",
				"AffiliationName" => "The HUB",
				"StreetAddress" => "1234 Street Ave",
				"City" => "Dank Town",
				"StateProvince" => "Washington",
				"Zip" => "12345"
			),
		);

		$id = $this->Applications_model->createApp($data['Application'], $data['Admins'], $data['Locations'], $data['Applicant']);
		echo '<p>Actions attached to application</p>';
		echo '<pre>';
		var_dump($this->mailer->getActionsForApp($id));
		echo '</pre>';
		//email->app($id, 'submitted')
		$this->mailer->performMailActionForApp($id, 'submitted');

		echo "<p> ID: {$id} </p>"; 
		echo '<p>App Status: ' . $this->approval->getStatus($id) . '</p>';
		//advance app to sponsor
		echo '<p>Advancing App to Sponsor';
		$error = $this->approval->advanceApplication($id, 'sponsor');
		if($error) { echo "<p> Error: " . $error . "</p>"; }
		echo '<p>App Status: ' . $this->approval->getStatus($id) . '</p>';
		echo "<p>Approvals for app</p>";
		echo "<pre>";
		$approvals = $this->approval->getApprovalsForApp($id);
		echo print_r($approvals);
		echo "</pre>";

		// //attempt advance to venue, should fail
		// echo '<p>Advancing App to Venue without approval (should fail)';
		// $error = $this->approval->advanceApplication($id, 'venue');
		// echo "<pre> Error: ";
		// print_r($error);
		// echo "</pre>";
		// echo '<p>App Status: ' . $this->approval->getStatus($id) . '</p>';

		// echo '<p>Approving Application Sponsorship and advancing';
		// //approve
		// $this->approval->updateSponsorDescision($id, 'jshill', 'approved');
		// $approvals = $this->approval->getApprovalsForApp($id);
		// echo "<p>Approvals for app</p>";
		// echo "<pre>";
		// echo print_r($approvals);
		// echo "</pre>";

		// $error = $this->approval->advanceApplication($id, 'venue');
		// echo "<pre> Error: ";
		// print_r($error);
		// echo "</pre>";

		// $venues = $this->Applications_model->getVenues($id);

		// // echo "<pre> venues: ";
		// // print_r($venues);
		// // echo "</pre>";
		// // foreach($venues as $venue) {
		// // 	$this->approval->createApproval($venue['VenueID'], 'VenueOperator');
		// // }

		// echo '<p>App Status: ' . $this->approval->getStatus($id) . '</p>';
		// $approvals = $this->approval->getApprovalsForApp($id);
		// echo "<p>Approvals for app</p>";
		// echo "<pre>";
		// echo print_r($approvals);
		// echo "</pre>";

		// //try to rollback
		// echo '<p>Attemping to roll back app to sponsor(should fail)</p>';
		// $error = $this->approval->advanceApplication($id, 'sponsor');
		// echo "<pre> Error: ";
		// print_r($error);
		// echo "</pre>";
		// echo '<p>App Status: ' . $this->approval->getStatus($id) . '</p>';

	}

	// array(1) { 
	// 	[0]=> array(27) { 
	// 		["ApprovalID"]=> string(1) "4" 
	// 		["ApprovalType"]=> string(1) "3" 
	// 		["ApprovalStartDate"]=> string(19) "2017-06-14 10:43:41" 
	// 		["ApprovalEndDate"]=> NULL 
	// 		["ApprovalSignature"]=> string(0) "" 
	// 		["VenueID"]=> string(2) "16" 
	// 		["Descision"]=> NULL 
	// 		["DescisionRemark"]=> NULL 
	// 		["UserRoleID"]=> string(4) "1244" 
	// 		["ApplicationID"]=> string(2) "31" 
	// 		["PermitID"]=> NULL 
	// 		["RoomID"]=> string(4) "1244" 
	// 		["ApplicationTypeID"]=> string(1) "1" 
	// 		["EventName"]=> string(8) "Test App" 
	// 		["EventDesc"]=> string(22) "Testing Testing, 1 2 3" 
	// 		["DateApplied"]=> string(19) "2017-06-14 10:43:41" 
	// 		["DateAccepted"]=> NULL 
	// 		["ProminantAttendees"]=> string(3) "-- " 
	// 		["RegDonDesc"]=> NULL 
	// 		["TotalAttendees"]=> string(1) "0" 
	// 		["AttendeesUnder21"]=> NULL 
	// 		["AttendeesMembers"]=> NULL 
	// 		["AmplifiedSoundDesc"]=> NULL 
	// 		["has_flag"]=> NULL 
	// 		["Status"]=> string(9) "submitted" 
	// 		["UserTypeID"]=> string(1) "3" 
	// 		["UserID"]=> string(3) "392" 
	// 	} 
	// }
}

?>