<?php  

class Ketua_koperasi extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->data['username'] = $this->session->userdata('username');
		if (!isset($this->data['username']))
		{
			redirect('login');
			exit;
		}
		
		$this->data['id_role'] = $this->session->userdata('id_role');
		if (!isset($this->data['id_role']) && $this->data['id_role'] != 2)
		{
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('id_role');
			redirect('login');
			exit;
		}

		$this->load->model('anggota_m');
        $this->load->model('angsuran_m');
        $this->load->model('pinjaman_m');
        $this->load->model('simpanan_m');
	}

	public function index(){
        $this->data['title']        = 'Ketua Koperasi';
        $this->data['content']      = 'ketua_koperasi/dashboard'; 
        $this->data['anggota']   	= $this->anggota_m->get();
        $this->data['angsuran']     = $this->angsuran_m->get();
        $this->data['pinjaman']     = $this->pinjaman_m->get();
        $this->data['simpanan']   	= $this->simpanan_m->get();
        $this->template($this->data,'admin');	
	}

	public function data_anggota(){
		$this->data['title'] 	= "Data Anggota | KOHIWAS";
		$this->data['content']	= "ketua_koperasi/data_anggota";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->template($this->data);
	}

	public function data_simpanan(){
		$this->data['title'] 	= "Data Simpanan | KOHIWAS";
		$this->data['content']	= "ketua_koperasi/data_simpanan";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['simpanan']	= $this->simpanan_m->get_by_order('id_simpanan', 'DESC');
		$this->template($this->data);
	}

	public function cetakAnggota(){
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->load->view('laporan/dataAnggota', $this->data);
	}

	public function cetakSimpanan(){
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['simpanan']	= $this->simpanan_m->get_by_order('id_simpanan', 'DESC');
		
		$html = $this->load->view('laporan/dataSimpanan', $this->data, true);
    	$pdfFilePath = 'Laporan Data Simpanan.pdf';
    	$this->load->library('m_pdf');
    	$this->m_pdf->pdf->WriteHTML($html);
    	$this->m_pdf->pdf->Output($pdfFilePath, "D");	
	}
}

?>