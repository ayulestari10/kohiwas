<?php  

class Web extends CI_Controller{

	function index(){
		$this->load->view('home');
	}

	function dashboard(){
		$data = [
			'title'		=> 'Admin',
			'content'	=> 'dashboard'
		];
		$this->load->view('admin/templates', $data);	
	}

	function data_supplier(){
		$data = [
			'title'		=> 'Admin',
			'content'	=> 'admin/data_supplier'
		];
		$this->load->view('admin/templates', $data);	
	}

	function data_bahanBaku(){
		$data = [
			'title'		=> 'Admin',
			'content'	=> 'admin/bahan_baku'
		];
		$this->load->view('admin/templates', $data);	
	}

	function data_bahanBakuMin(){
		$data = [
			'title'		=> 'Admin',
			'content'	=> 'admin/bahan_baku_min'
		];
		$this->load->view('admin/templates', $data);	
	}	

	function data_unit(){
		$data = [
			'title'		=> 'Admin',
			'content'	=> 'admin/data_unit'
		];
		$this->load->view('admin/templates', $data);	
	}	

	function data_permintaan(){
		$data = [
			'title'		=> 'Admin',
			'content'	=> 'operator_unit/data_permintaan'
		];
		$this->load->view('admin/templates', $data);	
	}	

	function laporan_supplier(){
		$this->load->view('laporan/dataSupplier');
	}

	function laporan_bahanBaku(){
		$this->load->view('laporan/dataBahanBaku');
	}

	function laporan_pembelian(){
		$this->load->view('laporan/dataPembelian');
	}

	function laporan_unit(){
		$this->load->view('laporan/dataUnit');
	}

	function laporan_permintaan(){
		$this->load->view('laporan/dataPermintaan');
	}
}


?>