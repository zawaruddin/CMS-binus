<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
	private $_userID = '1';
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('data_model', 'data');
	}
	
	public function index()
	{	
		$code = 'FOOD6XXX';
		
		// get data SO
		$so = $this->data->getCourseStudentOutcome($code);
		
		// Jika data belum ada, get dari API
		if(empty($so)){
			// return dari API SO dan LObj
			$api_data	= $this->postRequest("get_lobj/{$code}");	
			
			// simpan SO dan LObj ke local database
			$this->data->insertData($this->_userID, $code, $api_data->data);
			
			// get data SO
			$so = $this->data->getCourseStudentOutcome($code);
		}
		
		$data = [];
		foreach($so as $s){
			
			// get data LObj untuk tiap-tiap SO
			$detail  = $this->data->getCourseLObj($s->courseStudentOutcomeId, $code);
			foreach($detail as $d){
				$s->LObj[] = $d;
			}
		}
		//$this->d($so); die;
		$result=$so;
		
		//echo '<pre>';
		//print_r($so); //die;
		
		// get data mapping LObj dan LO
		$LObj2LO = $this->data->getCourseLObj2LO($code); 
		
		//dump($so, 'data SO dan LObj'); 
		//dump($LObj2LO, 'data mapping LObj ke LO');
		
		if($result){	
			$data['log'] = $this->data->getLogQuery();
		}else {
			$data['log'] = null;
		}
		$data['msg'] 	 = $this->data->getMessage();
		$data['so'] = $result;
		$data['mapping']=$LObj2LO;
		$this->load->view('konten/cms', $data);
	}
	
	function saveData(){
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('courseStudentOutlineID', 'SO ID', 'required');
		$this->form_validation->set_rules('courseLObjID', 'LObj ID', 'required');
		
		if($this->form_validation->run() == FALSE){
			// return to view jika ada validasi yang error (belum diisi)
			echo 'pesan';
        } else {
			
		// contoh post data untuk map parameter ketiga saveStudentLearningOutcome(1,2,3), parameter 1,2 bisa diabaikan (diset null dulu)
		// $map = array(7 => 0, 8 => 2, 9 => 1);		=> key adalah courseLObj2LOID dan valuenya adalah weigthLO	
		$data['status'] = $this->data->saveStudentLearningOutcome($this->input->post('courseStudentOutlineID'), $this->input->post('courseLObjID'), $this->input->post('map'));
		$data['pesan']	= $this->data->getMessage();
		//$this->load->view('konten/cms', $data);
		}
	}
}