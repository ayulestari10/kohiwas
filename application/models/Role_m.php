<?php 
class Role_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name']	= 'role';
		$this->data['primary_key']	= 'id_role';
	}
}