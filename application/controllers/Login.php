<?php 

class Login extends MY_Controller
{
	private $data = [];

	public function __construct()
	{
		parent::__construct();
		$username = $this->session->userdata('username');
		if (isset($username))
		{
			$this->data['id_role'] = $this->session->userdata('id_role');
			switch ($this->data['id_role'])
			{
				case 1:
					redirect('admin');
					break;
				case 3:
					redirect('operator');
					break;
			}

			exit;
		}

		$this->load->model('login_m');
	}

	public function index()
	{
		if ($this->POST('login-submit'))
		{
			$this->data = [
				'username'	=> $this->POST('username'),
				'password'	=> md5($this->POST('password'))
			];

			$this->login_m->login($this->data);
			
			redirect('login');
			exit;
		}

		$this->load->view('home');
	}
}