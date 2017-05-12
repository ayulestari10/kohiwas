<?php 
class Anggota_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name']	= 'anggota';
		$this->data['primary_key']	= 'id_anggota';
	}
}