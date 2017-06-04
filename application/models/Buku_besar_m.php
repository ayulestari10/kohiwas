<?php  

class Buku_besar_m extends MY_Model{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name']	= 'buku_besar';
		$this->data['primary_key']	= 'id_buku_besar';
	}

	public function get_previous_row($col, $id, $ket){
		$query = $this->db->query('select * from '.$this->data['table_name'].' having '.$col.' <  (select '.$col.' from '.$this->data['table_name'].' where '.$col.' = '.$id.' and ket= "'.$ket.'") order by '.$col.' desc limit 1');

		return $query->row();
	}

	public function get_prev_row_cond(){
		$query = $this->db->query();

		return $query->row();
	}
}

?>