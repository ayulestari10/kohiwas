<?php  

class Buku_besar_m extends MY_Model{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name']	= 'buku_besar';
		$this->data['primary_key']	= 'id_buku_besar';
	}
}

?>