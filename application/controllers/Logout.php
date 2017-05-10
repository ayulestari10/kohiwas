<?php 

class Logout extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('id_role');
		redirect('login');
		exit;
	}
}