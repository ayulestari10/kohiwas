<?php 
class Log_login_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name']	= 'log_login';
		$this->data['primary_key']	= 'id_log_login';
	}
}