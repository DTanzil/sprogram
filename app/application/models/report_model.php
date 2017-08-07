<?php
# CREATED 3/6/17 by JSHILL
class Report_model extends CI_Model {
	
	/*
	 * This model handles all report queries to the database. It serves the
	 * Report controller.
	 */

	#constructor. Load the database library
	public function __construct() {
		$this->load->database();
	}

	public function runOpenRSOAppReport() {

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

}
?>