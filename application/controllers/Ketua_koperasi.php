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
        $this->load->model('jurnal_umum_m');
        $this->load->model('buku_besar_m');
	}

	public function index(){
        $this->data['title']        = 'Ketua Koperasi';
        $this->data['content']      = 'ketua_koperasi/dashboard'; 
        $this->data['anggota']   	= $this->anggota_m->get();
        $this->data['angsuran']     = $this->angsuran_m->get();
        $this->data['pinjaman']     = $this->pinjaman_m->get();
        $this->data['simpanan']   	= $this->simpanan_m->get();
        $this->data['jurnal_umum']  = $this->jurnal_umum_m->get();
        $this->data['buku_besar']   = $this->buku_besar_m->get();
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

	public function data_pinjaman(){
		$this->data['title'] 	= "Data Pinjaman | KOHIWAS";
		$this->data['content']	= "ketua_koperasi/data_pinjaman";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['pinjaman']	= $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');
		$this->template($this->data);
	}

	public function data_angsuran(){
		$this->data['title'] 	= "Data Angsuran | KOHIWAS";
		$this->data['content']	= "ketua_koperasi/data_angsuran";
		$this->data['angsuran']	= $this->angsuran_m->get_by_order('id_angsuran', 'DESC');
		$this->data['pinjaman']	= $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');
		$this->template($this->data);
	}

	public function data_jurnal(){
		$this->data['title'] 		= "Jurnal Umum | KOHIWAS";
		$this->data['content']		= "ketua_koperasi/jurnal";
		$this->data['jurnal_umum']	= $this->jurnal_umum_m->get_by_order('id_jurnal', 'DESC');
		$this->template($this->data);	
	}

	public function data_bukuBesar(){
		$this->data['title'] 	= "Buku Besar | KOHIWAS";
		$this->data['content']	= "ketua_koperasi/buku_besar";
		$this->data['buku_besar']	= $this->buku_besar_m->get_by_order('tgl', 'ASC');
		$this->template($this->data);	
	}

	public function cetakAnggota(){
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$html = $this->load->view('laporan/dataAnggota', $this->data, true);
		$pdfFilePath = 'Laporan Data Anggota.pdf';
    	$this->load->library('m_pdf');
    	$this->m_pdf->pdf->WriteHTML($html);
    	$this->m_pdf->pdf->Output($pdfFilePath, "D");
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

	public function cetakPinjaman(){
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['pinjaman']	= $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');
		$html = $this->load->view('laporan/dataPinjaman', $this->data, true);
    	$pdfFilePath = 'Laporan Data Pinjaman.pdf';
    	$this->load->library('m_pdf');
    	$this->m_pdf->pdf->WriteHTML($html);
    	$this->m_pdf->pdf->Output($pdfFilePath, "D");		
	}

	public function cetakAngsuran(){
		$this->data['angsuran']	= $this->angsuran_m->get_by_order('id_angsuran', 'DESC');
		$this->data['pinjaman']	= $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');

		$html = $this->load->view('laporan/dataAngsuran', $this->data, true);
    	$pdfFilePath = 'Laporan Data Pinjaman.pdf';
    	$this->load->library('m_pdf');
    	$this->m_pdf->pdf->WriteHTML($html);
    	$this->m_pdf->pdf->Output($pdfFilePath, "D");	
	}

	public function cetakJurnal(){
		$this->data['jurnal_umum']	= $this->jurnal_umum_m->get_by_order('id_jurnal', 'DESC');

		$html = $this->load->view('laporan/dataJurnalUmum', $this->data, true);
    	$pdfFilePath = 'Jurnal Umum.pdf';
    	$this->load->library('m_pdf');
    	$this->m_pdf->pdf->WriteHTML($html);
    	$this->m_pdf->pdf->Output($pdfFilePath, "D");
	}

	public function cetakBukuBesar(){
		$this->data['buku_besar']	= $this->buku_besar_m->get_by_order('id_buku_besar', 'DESC');

		$html = $this->load->view('laporan/dataBukuBesar', $this->data, true);
    	$pdfFilePath = 'Buku Besar.pdf';
    	$this->load->library('m_pdf');
    	$this->m_pdf->pdf->WriteHTML($html);
    	$this->m_pdf->pdf->Output($pdfFilePath, "D");	
	}
}

?>