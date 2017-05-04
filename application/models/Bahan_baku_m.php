<?php 

class Bahan_baku_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'bahan_baku';
		$this->data['primary_key']	= 'id_bahan_baku';
	}
}