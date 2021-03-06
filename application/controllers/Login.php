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
					redirect('ketua_koperasi');
					break;
				case 2:
					redirect('admin');
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
				// 'username'	=> $this->POST('username'),
				'password'	=> md5($this->POST('password')),
				'id_role'	=> $this->POST('role')
			];

			$this->login_m->login($this->data);

			redirect('login');
			exit;
		}

		$this->load->model('role_m');
		$this->data['role'] = $this->role_m->get();
		$this->load->view('home', $this->data);
	}
}
