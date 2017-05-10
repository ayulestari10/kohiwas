<?php  

class Admin extends MY_Controller{
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->data['title'] 	= "Data Anggota | KOHIWAS";
		$this->data['content']	= "admin/data_anggota";
		$this->template($this->data);
	}

	public function dataSimpanan(){
		$this->data['title'] 	= "Data Simpanan | KOHIWAS";
		$this->data['content']	= "admin/data_simpanan";
		$this->template($this->data);
	}

	public function dataPinjaman(){
		$this->data['title'] 	= "Data Pinjaman | KOHIWAS";
		$this->data['content']	= "admin/data_pinjaman";
		$this->template($this->data);
	}

	public function dataAngsuran(){
		$this->data['title'] 	= "Data Angsuran | KOHIWAS";
		$this->data['content']	= "admin/data_angsuran";
		$this->template($this->data);
	}
}

?>