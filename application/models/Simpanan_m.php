<?php 
class Simpanan_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name']	= 'simpanan';
		$this->data['primary_key']	= 'id_simpanan';
	}
}