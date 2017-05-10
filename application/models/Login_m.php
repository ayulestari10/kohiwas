<?php 

class Login_m extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		
		$this->data['table_name']	= 'user';
		$this->data['primary_key']	= 'username';
		
		$this->load->model('log_login_m');
	}

	public function login($data)
	{
		$result = $this->get_row($data);
		if (isset($result))
		{
			$log = [
				'username'	=> $result->username,
				'tanggal'	=> date('Y-m-d'),
				'waktu'		=> date('H:i:s')
			];
			$this->log_login_m->insert($log);
			$this->session->set_userdata([
				'username'	=> $result->username,
				'id_role'	=> $result->id_role
			]);
			return TRUE;
		}

		return FALSE;
	}
}