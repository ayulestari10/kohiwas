<?php 

class Login extends MY_Controller
{
	private $data = [];

	public function __construct()
	{
		parent::__construct();
		$username = $this->session->userdata('username');
		$this->dump($username);
		$x = $this->session->all_userdata();
		$this->dump($x);
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

			$login_session = $this->login_m->login($this->data);
			$this->session->set_userdata([
				'username'	=> $login_session->username,
				'id_role'	=> $login_session->id_role
			]);
			redirect('login');
			exit;
		}

		$this->load->view('home');
	}
}