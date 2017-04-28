<?php  

class Web extends CI_Controller{

	function index(){
		$this->load->view('home');
	}

	function dashboard(){
		$data = [
			'title'		=> 'Admin',
			'content'	=> 'dashboard'
		];
		$this->load->view('admin/templates', $data);	
	}
}


?>