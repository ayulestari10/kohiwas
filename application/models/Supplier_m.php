<?php 

class Supplier_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'suplier';
		$this->data['primary_key']	= 'id_suplier';
	}
}