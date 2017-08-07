<?php
class DatabaseTestModel extends CI_Model {
	
	/*
	 * This model is a test bed. You can use it to interact with the database
	 * for tests and such.
	 */

	#constructor. Load the database library
	public function __construct() {
		$this->load->database();
	}

	# Returns all tables in the database
	# SELECT * FROM 
	public function getAllTables() {
		$query = 
			'SHOW TABLES';
		$results = $this->db->query($query);
		return $results->result();
	}

	# Returns 100 results from StateProvince
	# SELECT * FROM StateProvince LIMIT 100
	public function getAllCities() {
		$query =
			'SELECT * FROM City 
			 LIMIT 100';
		 $results = $this->db->query($query);
		 return $results->result();
	}

	public function getFromTable() {
		echo 'getFromTable()';
		$this->load->helper('url');

		$select = $this->input->post('select');
		$from = $this->input->post('from');

		$query = 
			"SELECT {$select}
			 FROM {$from}
			 LIMIT 100
			";
		$results = $this->db->query($query);
		return $results->result_array();
	}

	public function getReneApps() {

		$query =		
			"SELECT DISTINCT pt.abbr AS PermitType, app.id AS AppID, app.dateApplied AS SubmitDate,
				app.eventName AS EventName, ei.dateFrom AS EventStart, ei.dateTo AS EventEnd, 
				c.nameLast AS ApplicantLastName, c.nameFirst AS ApplicantFirstName, c.email AS ApplicantEmail, 
			    app.org_name AS Organization,
			    ad.id AS AdvisorID, ad.netid AS AdvisorNetID, c1.nameLast AS AdvisorLastName, c1.nameFirst AS AdvisorFirstName
			FROM Applications app
				JOIN AppApprovals p ON app.id = p.idApplication
				JOIN EventInstances ei ON app.id = ei.idApplication
			    JOIN Admins ad ON app.idAdvisor = ad.id
			    JOIN AdminTypeRelations atr ON ad.id = atr.idAdmin
			    JOIN AdminTypes atype ON atr.idAdminType = atype.id
			    JOIN Contacts c ON app.idApplicant = c.id
			    JOIN Contacts c1 ON ad.idContact = c1.id
			    JOIN PermitTypes pt ON app.idPermitType = pt.id
			WHERE app.org_affiliation = 'rso/asuw/gpss'
				AND atype.name = 'sponsor'
				AND app.idSponsor = 5
			    AND app.dateApplied > '2017-1-1'
			    AND app.dateDecision IS NULL
			    AND p.sig_step1 = ''
			ORDER BY pt.name, dateApplied DESC
			";
		$results = $this->db->query($query);
		return $results->result_array();
	}

	public function getAppStats($from, $to) {
		$query =
			"SELECT 'all' AS Category, COUNT(a.id) FROM Applications a
				JOIN AppApprovals ap ON a.id = ap.idApplication
				JOIN ApprovalStatuses astat ON astat.id = ap.idStepStatus
				JOIN PermitTypes pt ON pt.id = a.idPermitType
			    WHERE a.eventName NOT LIKE '%test%'
				AND a.eventName NOT LIKE '%popcorn%'
			UNION
			SELECT pt.category, COUNT(a.id) FROM Applications a
				JOIN AppApprovals ap ON a.id = ap.idApplication
				JOIN ApprovalStatuses astat ON astat.id = ap.idStepStatus
				JOIN PermitTypes pt ON pt.id = a.idPermitType
			WHERE a.eventName NOT LIKE '%test%'
				AND a.eventName NOT LIKE '%popcorn%'
			GROUP BY pt.category
			";
		$results = $this->db->query($query);
		$stats['All Submitted Requests'] = $results->result_object();

		for($i = $from; $i <= $to; $i++) {
			$year = $i;
			$type = 'UUF';
			$query =
				"CALL AppStatsForFiscalYear({$year}, '{$type}')
				";
			$results = $this->db->query($query);
			$stats["{$type} Requests for Fiscal Year " . $year] = $results->result_object();

			$type = 'ALC';
			$query =
				"CALL AppStatsForFiscalYear({$year}, '{$type}')
				";
			$results = $this->db->query($query);
			$stats["{$type} Requests for Fiscal Year " . $year] = $results->result_object();
		}
		

		return $stats;
	}
}
?>