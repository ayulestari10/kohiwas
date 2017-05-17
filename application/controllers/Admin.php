<?php  

class Admin extends MY_Controller{
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
	}

	public function index()
	{
		$this->load->model('anggota_m');
        $this->load->model('angsuran_m');
        $this->load->model('pinjaman_m');
        $this->load->model('simpanan_m');
        $this->data['title']        = 'Admin';
        $this->data['content']      = 'admin/dashboard'; 
        $this->data['anggota']   	= $this->anggota_m->get();
        $this->data['angsuran']     = $this->angsuran_m->get();
        $this->data['pinjaman']     = $this->pinjaman_m->get();
        $this->data['simpanan']   	= $this->simpanan_m->get();
        $this->template($this->data,'admin');	
	}

	public function data_anggota(){
		$this->load->model('anggota_m');
		
		if ($this->POST('simpan'))
		{
			$this->data['anggota'] = [
				'nlp'			=> $this->POST('nlp'),
				'nama'			=> $this->POST('nama'),
				'tgl_mendaftar'	=> $this->POST('tgl_mendaftar'),
				'alamat'		=> $this->POST('alamat'),
				'simpanan_pokok'=> $this->POST('simpanan_pokok')
			];
			
			if (!$this->anggota_m->required_input(array_keys($this->data['anggota'])))
			{
				$this->flashmsg('Anda harus mengisi form dengan benar', 'danger');
				redirect('admin');
				exit;	
			}
			
			$this->anggota_m->insert($this->data['anggota']);
			$this->flashmsg('Data anggota berhasil disimpan');
			redirect('admin');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_anggota'))
		{
			$this->data['anggota'] = [
				'nlp'			=> $this->POST('edit_nlp'),
				'nama'			=> $this->POST('edit_nama'),
				'tgl_mendaftar'	=> $this->POST('edit_tgl_mendaftar'),
				'alamat'		=> $this->POST('edit_alamat'),
				'simpanan_pokok'=> $this->POST('edit_simpanan_pokok')
			];

			$this->anggota_m->update($this->POST('edit_id_anggota'), $this->data['anggota']);
			$this->flashmsg('Data anggota berhasil diperbarui');
			redirect('admin');
			exit;
		}
		
		if ($this->POST('get') && $this->POST('id_anggota'))
		{
			$this->data['anggota'] = $this->anggota_m->get_row(['id_anggota' => $this->POST('id_anggota')]);
			echo json_encode($this->data['anggota']);
			exit;
		}

		if ($this->POST('delete') && $this->POST('id_anggota'))
		{
			$this->anggota_m->delete($this->POST('id_anggota'));
			exit;
		}

		$this->data['title'] 	= "Data Anggota | KOHIWAS";
		$this->data['content']	= "admin/data_anggota";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->template($this->data);
	}

	public function data_simpanan()
	{
		$this->load->model('anggota_m');
		$this->load->model('simpanan_m');
		
		if ($this->POST('simpan'))
		{
			$this->data['simpanan'] = [
				'id_anggota'		=> $this->POST('id_anggota'),
				'tgl_simpanan'		=> $this->POST('tgl_simpanan'),
				'simpanan_wajib'	=> $this->POST('simpanan_wajib'),
				'simpanan_sukarela'	=> $this->POST('simpanan_sukarela')
			];
			
			if (!$this->simpanan_m->required_input(array_keys($this->data['simpanan'])))
			{
				$this->flashmsg('Anda harus mengisi form dengan benar', 'danger');
				redirect('admin/data_simpanan');
				exit;	
			}
			
			$this->simpanan_m->insert($this->data['simpanan']);
			$this->flashmsg('Data simpanan berhasil disimpan');
			redirect('admin/data_simpanan');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_simpanan'))
		{
			$this->data['simpanan'] = [
				'id_anggota'		=> $this->POST('edit_id_anggota'),
				'tgl_simpanan'		=> $this->POST('edit_tgl_simpanan'),
				'simpanan_wajib'	=> $this->POST('edit_simpanan_wajib'),
				'simpanan_sukarela'	=> $this->POST('edit_simpanan_sukarela')
			];

			$this->simpanan_m->update($this->POST('edit_id_simpanan'), $this->data['simpanan']);
			$this->flashmsg('Data simpanan berhasil diperbarui');
			redirect('admin/data_simpanan');
			exit;
		}
		
		if ($this->POST('get') && $this->POST('id_simpanan'))
		{
			$this->data['simpanan'] = $this->simpanan_m->get_row(['id_simpanan' => $this->POST('id_simpanan')]);
			$anggota = $this->anggota_m->get_by_order('id_anggota', 'DESC');
			$temp = [];
			foreach ($anggota as $row)
				$temp[$row->id_anggota] = $row->nama;
			$this->data['simpanan']->dropdown = form_dropdown('edit_id_anggota', $temp, $this->data['simpanan']->id_anggota, ['class' => 'form-control']);
			echo json_encode($this->data['simpanan']);
			exit;
		}

		if ($this->POST('delete') && $this->POST('id_simpanan'))
		{
			$this->simpanan_m->delete($this->POST('id_simpanan'));
			exit;
		}

		$this->data['title'] 	= "Data Simpanan | KOHIWAS";
		$this->data['content']	= "admin/data_simpanan";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['simpanan']	= $this->simpanan_m->get_by_order('id_simpanan', 'DESC');
		$this->template($this->data);
	}

	public function data_pinjaman()
	{
		$this->load->model('anggota_m');
		$this->load->model('pinjaman_m');
		
		if ($this->POST('simpan'))
		{
			$this->data['pinjaman'] = [
				'id_anggota'		=> $this->POST('id_anggota'),
				'tgl_pinjaman'		=> $this->POST('tgl_pinjaman'),
				'jlh_pinjaman'		=> $this->POST('jlh_pinjaman'),
				'bunga'				=> $this->POST('bunga'),
				'ttl_pinjaman'		=> $this->POST('ttl_pinjaman'),
				'lama_pinjaman'		=> $this->POST('lama_pinjaman'),
				'angsuran'			=> $this->POST('angsuran'),
				'status'			=> $this->POST('status')
			];

			if (!$this->pinjaman_m->required_input(array_keys($this->data['pinjaman'])))
			{
				$this->flashmsg('Anda harus mengisi form dengan benar', 'danger');
				redirect('admin/data_pinjaman');
				exit;	
			}
			
			$this->pinjaman_m->insert($this->data['pinjaman']);
			$this->flashmsg('Data pinjaman berhasil disimpan');
			redirect('admin/data_pinjaman');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_pinjaman'))
		{
			$this->data['pinjaman'] = [
				'id_anggota'		=> $this->POST('edit_id_anggota'),
				'tgl_pinjaman'		=> $this->POST('edit_tgl_pinjaman'),
				'jlh_pinjaman'		=> $this->POST('edit_jlh_pinjaman'),
				'bunga'				=> $this->POST('edit_bunga'),
				'ttl_pinjaman'		=> $this->POST('edit_ttl_pinjaman'),
				'lama_pinjaman'		=> $this->POST('edit_lama_pinjaman'),
				'angsuran'			=> $this->POST('edit_angsuran'),
				'status'			=> $this->POST('edit_status')
			];

			$this->pinjaman_m->update($this->POST('edit_id_pinjaman'), $this->data['pinjaman']);
			$this->flashmsg('Data pinjaman berhasil diperbarui');
			redirect('admin/data_pinjaman');
			exit;
		}
		
		if ($this->POST('get') && $this->POST('id_pinjaman'))
		{
			$this->data['pinjaman'] = $this->pinjaman_m->get_row(['id_pinjaman' => $this->POST('id_pinjaman')]);
			$anggota = $this->anggota_m->get_by_order('id_anggota', 'DESC');
			$temp = [];
			foreach ($anggota as $row)
				$temp[$row->id_anggota] = $row->nama;
			$this->data['pinjaman']->dropdown = form_dropdown('edit_id_anggota', $temp, $this->data['pinjaman']->id_anggota, ['class' => 'form-control']);
			echo json_encode($this->data['pinjaman']);
			exit;
		}

		if ($this->POST('delete') && $this->POST('id_pinjaman'))
		{
			$this->pinjaman_m->delete($this->POST('id_pinjaman'));
			exit;
		}

		$this->data['title'] 	= "Data Pinjaman | KOHIWAS";
		$this->data['content']	= "admin/data_pinjaman";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['pinjaman']	= $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');
		$this->template($this->data);
	}

	public function data_angsuran()
	{
		$this->load->model('angsuran_m');
		$this->load->model('pinjaman_m');
		
		if ($this->POST('simpan'))
		{
			$this->data['angsuran'] = [
				'id_pinjaman'		=> $this->POST('id_pinjaman'),
				'tgl_angsuran'		=> $this->POST('tgl_angsuran'),
				'jlh_dibayar'		=> $this->POST('jlh_dibayar'),
				'sisa_angsuran'		=> $this->POST('sisa_angsuran')
			];
			
			if (!$this->angsuran_m->required_input(array_keys($this->data['angsuran'])))
			{
				$this->flashmsg('Anda harus mengisi form dengan benar', 'danger');
				redirect('admin/data_angsuran');
				exit;	
			}
			
			$this->angsuran_m->insert($this->data['angsuran']);
			$this->flashmsg('Data angsuran berhasil disimpan');
			redirect('admin/data_angsuran');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_angsuran'))
		{
			$this->data['angsuran'] = [
				'id_pinjaman'		=> $this->POST('edit_id_pinjaman'),
				'tgl_angsuran'		=> $this->POST('edit_tgl_angsuran'),
				'jlh_dibayar'		=> $this->POST('edit_jlh_dibayar'),
				'sisa_angsuran'		=> $this->POST('edit_sisa_angsuran')
			];

			$this->angsuran_m->update($this->POST('edit_id_angsuran'), $this->data['angsuran']);
			$this->flashmsg('Data angsuran berhasil diperbarui');
			redirect('admin/data_angsuran');
			exit;
		}
		
		if ($this->POST('get') && $this->POST('id_angsuran'))
		{
			$this->data['angsuran'] = $this->angsuran_m->get_row(['id_angsuran' => $this->POST('id_angsuran')]);
			$pinjaman = $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');
			$temp = [];
			foreach ($pinjaman as $row)
				$temp[$row->id_pinjaman] = $row->id_pinjaman;
			$this->data['angsuran']->dropdown = form_dropdown('edit_id_pinjaman', $temp, $this->data['angsuran']->id_angsuran, ['class' => 'form-control']);
			echo json_encode($this->data['angsuran']);
			exit;
		}

		if ($this->POST('delete') && $this->POST('id_angsuran'))
		{
			$this->angsuran_m->delete($this->POST('id_angsuran'));
			exit;
		}

		$this->data['title'] 	= "Data Angsuran | KOHIWAS";
		$this->data['content']	= "admin/data_angsuran";
		$this->data['angsuran']	= $this->angsuran_m->get_by_order('id_angsuran', 'DESC');
		$this->data['pinjaman']	= $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');
		$this->template($this->data);
	}
}

?>