<?php
class DatabaseTestCont extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('DatabaseTestModel');
		$this->load->helper('url_helper');
	}

	# doesn't work
	public function index() {
		$this->load->library('form_validation');
		$this->load->helper('form');

		$this->load->view('database_test_view');
		$this->load->view('get_from_table_form');
		$this->load->view('footer');
	}

	# Gets all the table names
	public function tables() {
		
		$data['tables'] = $this->DatabaseTestModel->getAllTables();
		$this->load->view('database_test_view');
		$this->load->view('db_tables', $data);
		$this->load->view('footer');
	}

	public function getAllCities() {
		$data['cities'] = $this->DatabaseTestModel->getAllCities();
		$this->load->view('database_test_view');
		$this->load->view('db_cities', $data);
		$this->load->view('footer');
	}

	public function query() {
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('select', 'SELECT', 'required');
		$this->form_validation->set_rules('from', 'FROM', 'required');

		if($this->form_validation->run() === FALSE) {
			$this->load->view('database_test_view');
			$this->load->view('get_from_table_form');
			$this->load->view('footer');
		} else {
			$data['results'] = $this->DatabaseTestModel->getFromTable();
			$this->load->view('database_test_view');
			$this->load->view('get_from_table_form');
			$this->load->view('db_results', $data);
			$this->load->view('footer');
		}
	}

	public function testReport() {

		$data['results'] = $this->DatabaseTestModel->getReneApps();

		$num = 0;
		
		for($i = 0; $i < count($data['results']); $i++) {
			$result = $data['results'][$i];
			//var_dump($result);
			foreach($result as $key => $value) {
				$out[$i][$key] = $value;
			}
		}

		$output = fopen("php://output",'w') or die("Can't open php://output");
		header("Content-Type:application/csv"); 
		header("Content-Disposition:attachment;filename=report.csv"); 
		fputcsv($output, array('PermitType','AppID','SubmitDate', 'EventName', 'EventStart', 'EventEnd', 
			'ApplicantLastName', 'ApplicantFirstName', 'ApplicantEmail', 'Organization', 'AdvisorID', 'AdvisorNetID', 'AdvisorLastName', 'AdvisorFirstName'));
		foreach($out as $line) {
		    fputcsv($output, $line);
		}
		fclose($output) or die("Can't close php://output");
	}

	public function runAppStatsReport() {
		$from = $this->input->get('from');
		$to = $this->input->get('to');

		$data = $this->DatabaseTestModel->getAppStats($from, $to);

		$output = fopen("php://output",'w') or die("Can't open php://output"); 
		header("Content-Type:application/csv"); 
		header("Content-Disposition:attachment;filename=AppStatsReport.csv");
		$num = 0;
		$csv = array();
		foreach($data as $key => $value) {
			$result = $data[$key];
			//var_dump($result);
			$this->makeCSV($key, $result, $output);
		}
		//var_dump($out);



		fclose($output) or die("Can't close php://output");
	}

	private function makeCSV($title, $metric, $output) {
		//echo 'making csv';
		fputcsv($output, array($title));
		for($i = 0; $i < count($metric); $i++) {
			$values = array('');

			foreach($metric[$i] as $key => $value) {
				array_push($values, $value);
			}
			fputcsv($output, $values);
		}
		fputcsv($output, array());

	}
}
?>