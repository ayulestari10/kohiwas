<?php  

class Jurnal_umum_m extends MY_Model{
	public function __construct()
	{
		parent::__construct();
		
		$this->data['table_name']	= 'jurnal_umum';
		$this->data['primary_key']	= 'id_jurnal';
	}
}

?>