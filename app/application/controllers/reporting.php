<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporting extends CI_Controller{
		
	public function __construct(){
		parent::__construct();
		
		$this->load->database();
		
		$this->admin_meta->protectPage(array('admin'));
	
		$this->load->model('Report_model');
		$this->load->helper('common');
		
		//$this->load->library('application_details_generator', array('mode' => 1));
	}
	
	public function index(){
		//$apps = $this->db->query('SELECT id, eventName FROM Applications')->result_array();
		$this->load->view('header', $this->admin_meta->getHeader('Reporting'));
		$this->load->view('reporting/all');
		$this->load->view('sidebar', $this->admin_meta->getSidebar('reporting'));
	}

	public function runOpenRSOAppReport() {

		$data['results'] = $this->Report_model->runOpenRSOAppReport();

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
		header("Content-Disposition:attachment;filename=OpenRSOAppReport.csv"); 
		fputcsv($output, array('PermitType','AppID','SubmitDate', 'EventName', 'EventStart', 'EventEnd', 
			'ApplicantLastName', 'ApplicantFirstName', 'ApplicantEmail', 'Organization', 'AdvisorID', 'AdvisorNetID', 'AdvisorLastName', 'AdvisorFirstName'));
		foreach($out as $line) {
		    fputcsv($output, $line);
		}
		fclose($output) or die("Can't close php://output");
	}

}