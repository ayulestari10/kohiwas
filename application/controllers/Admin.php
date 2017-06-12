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
				redirect('admin/data_anggota');
				exit;	
			}
			
			$this->anggota_m->insert($this->data['anggota']);
			$this->flashmsg('Data anggota berhasil disimpan');
			redirect('admin/data_anggota');
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
			redirect('admin/data_anggota');
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

	// 1
	public function data_simpanan()
	{
		$this->load->model('anggota_m');
		$this->load->model('simpanan_m');
		$this->load->model('jurnal_umum_m');
		$this->load->model('buku_besar_m');
		
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

			$this->load->model('jurnal_umum_m');
			$check_jurnal_umum = $this->jurnal_umum_m->get_row(['id_aktivitas' => 1, 'tgl' => $this->data['simpanan']['tgl_simpanan']]);
			if (isset($check_jurnal_umum))
			{
				$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
					'debit' 	=> $check_jurnal_umum->debit + $this->data['simpanan']['simpanan_wajib'],
					'kredit' 	=> $check_jurnal_umum->kredit + $this->data['simpanan']['simpanan_wajib']
				]);
			}
			else
			{
				$this->data['entri'] = [
					'tgl'			=> $this->data['simpanan']['tgl_simpanan'],
					'ket'			=> 'Simpanan Wajib',
					'debit'			=> $this->data['simpanan']['simpanan_wajib'],
					'kredit'		=> $this->data['simpanan']['simpanan_wajib'],
					'id_aktivitas'	=> 1
				];
				$this->jurnal_umum_m->insert($this->data['entri']);
			}

			$check_jurnal_umum = $this->jurnal_umum_m->get_row(['id_aktivitas' => 4, 'tgl' => $this->data['simpanan']['tgl_simpanan']]);
			if (isset($check_jurnal_umum))
			{
				$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
					'debit' 	=> $check_jurnal_umum->debit + $this->data['simpanan']['simpanan_sukarela'],
					'kredit' 	=> $check_jurnal_umum->kredit + $this->data['simpanan']['simpanan_sukarela']
				]);
			}
			else
			{
				$this->data['entri'] = [
					'tgl'			=> $this->data['simpanan']['tgl_simpanan'],
					'ket'			=> 'Simpanan Sukarela',
					'debit'			=> $this->data['simpanan']['simpanan_sukarela'],
					'kredit'		=> $this->data['simpanan']['simpanan_sukarela'],
					'id_aktivitas'	=> 4
				];
				$this->jurnal_umum_m->insert($this->data['entri']);
			}

			$this->load->model('buku_besar_m');
			$temp = $this->buku_besar_m->get_last_row(['tgl <=' => $this->data['simpanan']['tgl_simpanan']], 'tgl');
			if (isset($temp))
				$this->data['saldo_debit'] 	= $temp->saldo_debit;
			if (!isset($this->data['saldo_debit']))
				$this->data['saldo_debit'] = 0;
			if (isset($temp))
				$this->data['saldo_kredit']	= $temp->saldo_kredit;
			if (!isset($this->data['saldo_kredit']))
				$this->data['saldo_kredit'] = 0;
			if (isset($this->data['saldo_kredit']) && $this->data['saldo_kredit'] > 0)
			{
				$this->data['saldo_kredit'] -= $this->data['simpanan']['simpanan_wajib'];
				if ($this->data['saldo_kredit'] < 0)
				{
					$this->data['saldo_debit'] 	= $this->data['saldo_kredit'] * (-1);
					$this->data['saldo_kredit'] = 0;
				}
			}
			else if (isset($this->data['saldo_debit']) && $this->data['saldo_debit'] >= 0)
			{
				$this->data['saldo_debit'] += $this->data['simpanan']['simpanan_wajib'];
			}

			$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $this->data['simpanan']['tgl_simpanan'], 'id_aktivitas' => 1]);
			$id_buku_besar = -1;
			if (isset($check_buku_besar))
			{
				if ($check_buku_besar->saldo_kredit > 0)
				{
					$check_buku_besar->saldo_kredit -= $this->data['simpanan']['simpanan_wajib'];
					if ($check_buku_besar->saldo_kredit < 0)
					{
						$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
						$check_buku_besar->saldo_kredit = 0;
					}
				}
				else
				{
					$check_buku_besar->saldo_debit += $this->data['simpanan']['simpanan_wajib'];
				}

				$data = [
					'debit'			=> $check_buku_besar->debit + $this->data['simpanan']['simpanan_wajib'],
					'saldo_kredit'	=> $check_buku_besar->saldo_kredit,
					'saldo_debit'	=> $check_buku_besar->saldo_debit
				];

				$this->buku_besar_m->update($check_buku_besar->id_buku_besar, $data);
				$id_buku_besar = $check_buku_besar->id_buku_besar;
			}
			else
			{
				$this->data['entri'] = [
					'tgl'			=> $this->data['simpanan']['tgl_simpanan'],
					'ket'			=> 'Simpanan Wajib',
					'ref'			=> '103',
					'debit'			=> $this->data['simpanan']['simpanan_wajib'],
					'kredit'		=> 0,
					'saldo_debit'	=> $this->data['saldo_debit'],
					'saldo_kredit'	=> $this->data['saldo_kredit'],
					'id_aktivitas'	=> 1 
				];

				$this->buku_besar_m->insert($this->data['entri']);
				$id_buku_besar = $this->db->insert_id();
			}

			$check_buku_besar = $this->buku_besar_m->get(['tgl' => $this->data['simpanan']['tgl_simpanan'], 'id_aktivitas !=' => 1, 'id_buku_besar >' => $id_buku_besar]);

			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_kredit > 0)
				{
					$data['saldo_kredit'] = $saldo_kredit - $this->data['simpanan']['simpanan_wajib'];
					if ($data['saldo_kredit'] < 0)
					{
						$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
						$data['saldo_kredit'] = 0;
					}
				}
				else
					$data['saldo_debit'] = $saldo_debit + $this->data['simpanan']['simpanan_wajib'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $this->data['simpanan']['tgl_simpanan']]);

			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_kredit > 0)
				{
					$data['saldo_kredit'] = $saldo_kredit - $this->data['simpanan']['simpanan_wajib'];
					if ($data['saldo_kredit'] < 0)
					{
						$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
						$data['saldo_kredit'] = 0;
					}
				}
				else
					$data['saldo_debit'] = $saldo_debit + $this->data['simpanan']['simpanan_wajib'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$temp = $this->buku_besar_m->get_last_row(['tgl <=' => $this->data['simpanan']['tgl_simpanan']], 'tgl');
			if (isset($temp))
				$this->data['saldo_debit'] 	= $temp->saldo_debit;
			if (!isset($this->data['saldo_debit']))
				$this->data['saldo_debit'] = 0;
			if (isset($temp))
				$this->data['saldo_kredit']	= $temp->saldo_kredit;
			if (!isset($this->data['saldo_kredit']))
				$this->data['saldo_kredit'] = 0;
			if (isset($this->data['saldo_kredit']) && $this->data['saldo_kredit'] > 0)
			{
				$this->data['saldo_kredit'] -= $this->data['simpanan']['simpanan_sukarela'];
				if ($this->data['saldo_kredit'] < 0)
				{
					$this->data['saldo_debit'] 	= $this->data['saldo_kredit'] * (-1);
					$this->data['saldo_kredit'] = 0;
				}
			}
			else if (isset($this->data['saldo_debit']) && $this->data['saldo_debit'] > 0)
			{
				$this->data['saldo_debit'] += $this->data['simpanan']['simpanan_sukarela'];
			}

			$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $this->data['simpanan']['tgl_simpanan'], 'id_aktivitas' => 4]);
			$id_buku_besar = -1;
			if (isset($check_buku_besar))
			{
				if ($check_buku_besar->saldo_kredit > 0)
				{
					$check_buku_besar->saldo_kredit -= $this->data['simpanan']['simpanan_sukarela'];
					if ($check_buku_besar->saldo_kredit < 0)
					{
						$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
						$check_buku_besar->saldo_kredit = 0;
					}
				}
				else
				{
					$check_buku_besar->saldo_debit += $this->data['simpanan']['simpanan_sukarela'];
				}

				$data = [
					'debit'			=> $check_buku_besar->debit + $this->data['simpanan']['simpanan_sukarela'],
					'saldo_kredit'	=> $check_buku_besar->saldo_kredit,
					'saldo_debit'	=> $check_buku_besar->saldo_debit
				];

				$this->buku_besar_m->update($check_buku_besar->id_buku_besar, $data);
				$id_buku_besar = $check_buku_besar->id_buku_besar;
			}
			else
			{
				$this->data['entri'] = [
					'tgl'			=> $this->data['simpanan']['tgl_simpanan'],
					'ket'			=> 'Simpanan Sukarela',
					'ref'			=> '104',
					'debit'			=> $this->data['simpanan']['simpanan_sukarela'],
					'kredit'		=> 0,
					'saldo_debit'	=> $this->data['saldo_debit'],
					'saldo_kredit'	=> $this->data['saldo_kredit'],
					'id_aktivitas'	=> 4
				];

				$this->buku_besar_m->insert($this->data['entri']);
				$id_buku_besar = $this->db->insert_id();
			}

			$check_buku_besar = $this->buku_besar_m->get(['tgl' => $this->data['simpanan']['tgl_simpanan'], 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);

			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_kredit > 0)
				{
					$data['saldo_kredit'] = $saldo_kredit - $this->data['simpanan']['simpanan_sukarela'];
					if ($data['saldo_kredit'] < 0)
					{
						$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
						$data['saldo_kredit'] = 0;
					}
				}
				else
					$data['saldo_debit'] = $saldo_debit + $this->data['simpanan']['simpanan_sukarela'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $this->data['simpanan']['tgl_simpanan']]);

			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_kredit > 0)
				{
					$data['saldo_kredit'] = $saldo_kredit - $this->data['simpanan']['simpanan_sukarela'];
					if ($data['saldo_kredit'] < 0)
					{
						$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
						$data['saldo_kredit'] = 0;
					}
				}
				else
					$data['saldo_debit'] = $saldo_debit + $this->data['simpanan']['simpanan_sukarela'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$this->flashmsg('Data simpanan berhasil disimpan');
			redirect('admin/data_simpanan');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_simpanan'))
		{
			$simpanan_bfr = $this->simpanan_m->get_row(['id_simpanan' => $this->POST('edit_id_simpanan')]);

			$simpanan_wajib_bfr 	= $simpanan_bfr->simpanan_wajib;
			$simpanan_sukarela_bfr 	= $simpanan_bfr->simpanan_sukarela;
			$tanggal_bfr 			= $simpanan_bfr->tgl_simpanan;
			$simpanan_wajib_aft 	= $this->POST('edit_simpanan_wajib');
			$simpanan_sukarela_aft 	= $this->POST('edit_simpanan_sukarela');
			$tanggal_aft 			= $this->POST('edit_tgl_simpanan');

			$this->data['simpanan'] = [
				'id_anggota'		=> $this->POST('edit_id_anggota'),
				'tgl_simpanan'		=> $this->POST('edit_tgl_simpanan'),
				'simpanan_wajib'	=> $this->POST('edit_simpanan_wajib'),
				'simpanan_sukarela'	=> $this->POST('edit_simpanan_sukarela')
			];
			$this->simpanan_m->update($this->POST('edit_id_simpanan'), $this->data['simpanan']);

			$this->load->model('jurnal_umum_m');
			$this->load->model('buku_besar_m');

			if ($tanggal_aft != $tanggal_bfr && $simpanan_wajib_aft != $simpanan_wajib_bfr)
			{
				$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 1]);
				if (isset($check_jurnal_umum_bfr))
				{
					$debit = $check_jurnal_umum_bfr->debit - $simpanan_wajib_bfr;
					if ($debit <= 0)
						$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
					else
					{
						$this->jurnal_umum_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 1
						], [
							'debit'			=> $debit
						]);
					}
				}

				$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
					'tgl'			=> $tanggal_aft,
					'id_aktivitas'	=> 1
				]);

				if (isset($check_jurnal_umum_aft))
				{
					$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
						'debit'	=> $check_jurnal_umum_aft->debit + $simpanan_wajib_aft
					]);
				}
				else
				{
					$this->data['entri'] = [
						'tgl'			=> $tanggal_aft,
						'ket'			=> 'Simpanan Wajib',
						'kredit'		=> 0,
						'debit'			=> $simpanan_wajib_aft,
						'id_aktivitas'	=> 1
					];
					$this->jurnal_umum_m->insert($this->data['entri']);
				}

				$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 4]);
				if (isset($check_jurnal_umum_bfr))
				{
					$debit = $check_jurnal_umum_bfr->debit - $simpanan_sukarela_bfr;
					if ($debit <= 0)
						$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
					else
					{
						$this->jurnal_umum_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						], [
							'debit'			=> $debit
						]);
					}
				}

				$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
					'tgl'			=> $tanggal_aft,
					'id_aktivitas'	=> 4
				]);

				if (isset($check_jurnal_umum_aft))
				{
					$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
						'debit'	=> $check_jurnal_umum_aft->debit + $simpanan_sukarela_aft
					]);
				}
				else
				{
					$this->data['entri'] = [
						'tgl'			=> $tanggal_aft,
						'ket'			=> 'Simpanan Sukarela',
						'kredit'		=> 0,
						'debit'			=> $simpanan_sukarela_aft,
						'id_aktivitas'	=> 4
					];
					$this->jurnal_umum_m->insert($this->data['entri']);
				}

				$check_buku_besar_bfr = $this->buku_besar_m->get_row([
					'tgl'			=> $tanggal_bfr,
					'id_aktivitas'	=> 1
				]);
				if ($tanggal_aft > $tanggal_bfr && $simpanan_wajib_aft > $simpanan_wajib_bfr)
				{
					// case 5
					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_wajib_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_debit > 0)
					{
						$data['saldo_debit'] -= $simpanan_wajib_bfr;
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += $simpanan_wajib_bfr;

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 1
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 1,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $simpanan_wajib_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $simpanan_wajib_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;
							}	
						}
						else
							$check_buku_besar->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);
						
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $simpanan_wajib_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;

						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar <' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $simpanan_wajib_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $simpanan_wajib_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $simpanan_wajib_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $simpanan_wajib_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 1
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $simpanan_wajib_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 1
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 1, 'id_buku_besar >' => $id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				
					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 5.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;
									}	
								}
								else
									$check_buku_besar->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;

								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'saldo_kredit'	=> 0,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 5.2
							$data = [];
							$data['debit'] 		= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);

							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_debit > 0)
								{
									$check_buku_besar->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($check_buku_besar->saldo_debit < 0)
									{
										$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
										$check_buku_besar->saldo_debit = 0;	
									}
								}
								else
									$check_buku_besar->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);				
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_kredit'	=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
					else
					{
						// case 5.3
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);

						if (isset($check_buku_besar_bfr))
						{
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}
						}

						$check_buku_besar_bfr = $this->buku_besar_m->get([
							'tgl'				=> $tanggal_bfr,
							'id_aktivitas !='	=> 4,
							'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
						]);
						if (isset($check_buku_besar_bfr))
						{
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}
						}

						$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
						foreach ($check_buku_besar_itr as $row)
						{
							$data = [];
							$data['saldo_debit']	= $row->saldo_debit;
							$data['saldo_kredit']	= $row->saldo_kredit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
						$id_buku_besar = -1;
						if (isset($check_buku_besar))
						{
							$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
								'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft
							]);
							$id_buku_besar = $check_buku_besar->id_buku_besar;
							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_aft,
								'id_aktivitas !='	=> 4,
								'id_buku_besar <'	=> $id_buku_besar
							]);
							if (isset($check_buku_besar_bfr))
							{
								foreach ($check_buku_besar_bfr as $row)
								{
									$data = [];
									$data['saldo_debit']	= $row->saldo_debit;
									$data['saldo_kredit']	= $row->saldo_kredit;
									if ($row->saldo_debit > 0)
									{
										$data['saldo_debit'] -= $simpanan_sukarela_bfr;
										if ($data['saldo_debit'] < 0)
										{
											$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
											$data['saldo_debit'] = 0;
										}
									}
									else
										$data['saldo_kredit'] += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, $data);
								}
							}
						}
						else
						{
							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							$id = -1;
							if (!isset($last_row))
								$id = 1;
							else
								$id = $last_row->id_buku_besar;
							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= $simpanan_sukarela_bfr;
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}

							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							if (isset($last_row))
							{
								$saldo_debit 	= 0;
								$saldo_kredit 	= 0;
								if ($last_row->saldo_kredit > 0)
								{
									$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_bfr;
									if ($saldo_kredit < 0)
									{
										$saldo_debit = $saldo_kredit * (-1);
										$saldo_kredit = 0;
									}
								}
								else
									$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_bfr;

								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_bfr,
									'kredit'		=> 0,
									'saldo_debit'	=> $saldo_debit,
									'saldo_kredit'	=> $saldo_kredit,
									'id_aktivitas'	=> 4
								];
							}
							else
							{
								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_bfr,
									'kredit'		=> 0,
									'saldo_debit'	=> $simpanan_sukarela_bfr,
									'saldo_kredit'	=> 0,
									'id_aktivitas'	=> 4
								];
							}
							$this->buku_besar_m->insert($this->data['entri']);
							$id_buku_besar = $this->db->insert_id();
						}
					}
				}
				else if ($tanggal_aft > $tanggal_bfr && $simpanan_wajib_aft < $simpanan_wajib_bfr)
				{
					// case 6
					$data = [];
					$data['debit'] 		= $check_buku_besar_bfr->debit - $simpanan_wajib_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_debit > 0)
					{
						$data['saldo_debit'] -= $simpanan_wajib_bfr;
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += $simpanan_wajib_bfr;

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 1
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 1,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);

					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $simpanan_wajib_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $simpanan_wajib_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar <' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $simpanan_wajib_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);

						if ($check_buku_besar->saldo_debit > 0)
						{
							$check_buku_besar->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($check_buku_besar->saldo_debit < 0)
							{
								$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
								$check_buku_besar->saldo_debit = 0;	
							}
						}
						else
							$check_buku_besar->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);				
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $simpanan_wajib_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $simpanan_wajib_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $simpanan_wajib_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $simpanan_wajib_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 1
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_kredit'	=> 0,
								'saldo_debit'	=> $simpanan_wajib_aft,
								'id_aktivitas'	=> 1
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 1, 'id_buku_besar >' => $id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($simpanan_wajib_bfr - $simpanan_wajib_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($simpanan_wajib_bfr - $simpanan_wajib_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				
					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($simpanan_wajib_bfr - $simpanan_wajib_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($simpanan_wajib_bfr - $simpanan_wajib_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 6.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;
									}	
								}
								else
									$check_buku_besar->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;

								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'saldo_kredit'	=> 0,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 6.2
							$data = [];
							$data['debit'] 		= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);

							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_debit > 0)
								{
									$check_buku_besar->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($check_buku_besar->saldo_debit < 0)
									{
										$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
										$check_buku_besar->saldo_debit = 0;	
									}
								}
								else
									$check_buku_besar->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);				
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_kredit'	=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
					else
					{
						// case 6.3
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);

						if (isset($check_buku_besar_bfr))
						{
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}
						}

						$check_buku_besar_bfr = $this->buku_besar_m->get([
							'tgl'				=> $tanggal_bfr,
							'id_aktivitas !='	=> 4,
							'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
						]);
						if (isset($check_buku_besar_bfr))
						{
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}
						}

						$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
						foreach ($check_buku_besar_itr as $row)
						{
							$data = [];
							$data['saldo_debit']	= $row->saldo_debit;
							$data['saldo_kredit']	= $row->saldo_kredit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
						$id_buku_besar = -1;
						if (isset($check_buku_besar))
						{
							// $last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft, 'id_aktivitas !=' => 4], 'tgl');

							// if ($last_row->saldo_kredit > 0)
							// {
							// 	$last_row->saldo_kredit -= $simpanan_sukarela_aft;
							// 	if ($last_row->saldo_kredit < 0)
							// 	{
							// 		$last_row->saldo_debit = $last_row->saldo_kredit * (-1);
							// 		$last_row->saldo_kredit = 0;	
							// 	}
							// }
							// else
							// 	$last_row->saldo_debit += $simpanan_sukarela_aft;

							// $check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

							// if ($last_row->saldo_kredit > 0)
							// {
							// 	$last_row->saldo_kredit -= $check_buku_besar->debit;
							// 	if ($last_row->saldo_kredit < 0)
							// 	{
							// 		$last_row->saldo_debit = $last_row->saldo_kredit * (-1);
							// 		$last_row->saldo_kredit = 0;	
							// 	}
							// }
							// else
							// 	$last_row->saldo_debit += $check_buku_besar->debit;

							$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
								'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
								// 'saldo_debit'	=> $last_row->saldo_debit,
								// 'saldo_kredit'	=> $last_row->saldo_kredit
							]);
							$id_buku_besar = $check_buku_besar->id_buku_besar;

							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $id_buku_besar, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= $simpanan_sukarela_bfr;
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							$id = -1;
							if (!isset($last_row))
								$id = 1;
							else
								$id = $last_row->id_buku_besar;
							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= $simpanan_sukarela_bfr;
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}

							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							if (isset($last_row))
							{
								$saldo_debit 	= 0;
								$saldo_kredit 	= 0;
								if ($last_row->saldo_kredit > 0)
								{
									$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_bfr;
									if ($saldo_kredit < 0)
									{
										$saldo_debit = $saldo_kredit * (-1);
										$saldo_kredit = 0;
									}
								}
								else
									$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_bfr;

								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_bfr,
									'kredit'		=> 0,
									'saldo_debit'	=> $saldo_debit,
									'saldo_kredit'	=> $saldo_kredit,
									'id_aktivitas'	=> 4
								];
							}
							else
							{
								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_bfr,
									'kredit'		=> 0,
									'saldo_debit'	=> $simpanan_sukarela_bfr,
									'saldo_kredit'	=> 0,
									'id_aktivitas'	=> 4
								];
							}
							$this->buku_besar_m->insert($this->data['entri']);
							$id_buku_besar = $this->db->insert_id();
						}
					}
				}
				else if ($tanggal_aft < $tanggal_bfr && $simpanan_wajib_aft > $simpanan_wajib_bfr)
				{
					// case 7
					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_wajib_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_kredit > 0)
					{
						$data['saldo_kredit'] -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 1
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 1,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $simpanan_wajib_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $simpanan_wajib_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $simpanan_wajib_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $simpanan_wajib_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $simpanan_wajib_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $simpanan_wajib_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);

						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= $simpanan_wajib_aft;
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;	
							}
						}
						else
							$check_buku_besar->saldo_debit += $simpanan_wajib_aft;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $simpanan_wajib_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $simpanan_wajib_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $simpanan_wajib_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $simpanan_wajib_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $simpanan_wajib_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 1
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_kredit'	=> 0,
								'saldo_debit'	=> $simpanan_wajib_aft,
								'id_aktivitas'	=> 1
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 1]);
					if (!isset($check_buku_besar))
						$check_buku_besar = $this->buku_besar_m->get_last_row(['tgl' => $tanggal_bfr]);
					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'id_aktivitas !=' => 1]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 7.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;	
									}
								}
								else
									$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '103',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_kredit'	=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_buku_besar >' => $id_buku_besar, 'id_aktivitas !=' => 4]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 7.2
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_kredit']	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_kredit']	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;
									}	
								}
								else
									$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (!isset($last_row))
									$id_buku_besar = 1;
								else
									$id_buku_besar = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '103',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'saldo_kredit'	=> 0,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get_row([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							]);
							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
					else
					{
						// case 7.3
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);

						$data = [];
						$data['debit'] 	= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
						if ($data['debit'] <= 0)
							$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
						else
						{
							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							], $data);
						}

						$check_buku_besar_bfr = $this->buku_besar_m->get([
							'tgl'				=> $tanggal_bfr,
							'id_aktivitas !='	=> 4,
							'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
						]);
						foreach ($check_buku_besar_bfr as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
						foreach ($check_buku_besar_itr as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
						$id_buku_besar = -1;
						if (isset($check_buku_besar))
						{
							if ($check_buku_besar->saldo_kredit > 0)
							{
								$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
								if ($check_buku_besar->saldo_kredit < 0)
								{
									$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
									$check_buku_besar->saldo_kredit = 0;
								}
							}
							else
								$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;
							$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
								'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
								'saldo_debit'	=> $check_buku_besar->saldo_debit,
								'saldo_kredit'	=> $check_buku_besar->saldo_kredit
							]);
							$id_buku_besar = $check_buku_besar->id_buku_besar;

							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								$data = [];
								$data['saldo_kredit'] 	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}
						}
						else
						{
							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							$id = -1;
							if (!isset($last_row))
								$id = 1;
							else
								$id = $last_row->id_buku_besar;
							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								$data = [];
								$data['saldo_kredit'] 	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							if (isset($last_row))
							{
								$saldo_debit 	= 0;
								$saldo_kredit 	= 0;
								if ($last_row->saldo_kredit > 0)
								{
									$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
									if ($saldo_kredit < 0)
									{
										$saldo_debit = $saldo_kredit * (-1);
										$saldo_kredit = 0;
									}
								}
								else
									$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_aft,
									'kredit'		=> 0,
									'saldo_debit'	=> $saldo_debit,
									'saldo_kredit'	=> $saldo_kredit,
									'id_aktivitas'	=> 4
								];
							}
							else
							{
								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_aft,
									'kredit'		=> 0,
									'saldo_debit'	=> $simpanan_sukarela_aft,
									'saldo_kredit'	=> 0,
									'id_aktivitas'	=> 4
								];
							}
							$this->buku_besar_m->insert($this->data['entri']);
							$id_buku_besar = $this->db->insert_id();
						}
					}
				}
				else
				{
					// case 8
					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_wajib_bfr;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$temp_id = $check_buku_besar_bfr->id_buku_besar;
					if ($check_buku_besar_bfr->saldo_debit > 0)
					{
						$data['saldo_debit'] -= ($simpanan_wajib_bfr - $simpanan_wajib_aft);
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += ($simpanan_wajib_bfr - $simpanan_wajib_aft);

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 1
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 1,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_kredit']	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $simpanan_wajib_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $simpanan_wajib_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_kredit']	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $simpanan_wajib_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $simpanan_wajib_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $simpanan_wajib_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $simpanan_wajib_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);

						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= $simpanan_wajib_aft;
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;
							}	
						}
						else
							$check_buku_besar->saldo_debit += $simpanan_wajib_aft;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $simpanan_wajib_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (!isset($last_row))
							$id_buku_besar = 1;
						else
							$id_buku_besar = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $simpanan_wajib_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $simpanan_wajib_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $simpanan_wajib_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $simpanan_wajib_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 1
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $simpanan_wajib_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 1
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get_row([
						'tgl'			=> $tanggal_bfr,
						'id_aktivitas'	=> 1
					]);
					if (!isset($check_buku_besar_bfr))
						$check_buku_besar_bfr = $this->buku_besar_m->get_last_row(['tgl' => $tanggal_bfr, 'id_buku_besar <' => $temp_id]);
					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_aktivitas !=' => 1, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($simpanan_wajib_bfr - $simpanan_wajib_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($simpanan_wajib_bfr - $simpanan_wajib_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($simpanan_wajib_bfr - $simpanan_wajib_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($simpanan_wajib_bfr - $simpanan_wajib_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 8.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							$temp_id = $check_buku_besar_bfr->id_buku_besar;
							if ($check_buku_besar_bfr->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;	
									}
								}
								else
									$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '103',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_kredit'	=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_buku_besar >' => $temp_id, 'id_aktivitas !=' => 4]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 8.2
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$temp_id = $check_buku_besar_bfr->id_buku_besar;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_kredit']	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_kredit']	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;
									}	
								}
								else
									$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (!isset($last_row))
									$id_buku_besar = 1;
								else
									$id_buku_besar = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '103',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'saldo_kredit'	=> 0,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $temp_id]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
					else
					{
						// case 8.3
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);

						$data = [];
						$data['debit'] 	= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
						if ($data['debit'] <= 0)
							$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
						else
						{
							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							], $data);
						}

						$check_buku_besar_bfr = $this->buku_besar_m->get([
							'tgl'				=> $tanggal_bfr,
							'id_aktivitas !='	=> 4,
							'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
						]);
						foreach ($check_buku_besar_bfr as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
						foreach ($check_buku_besar_itr as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
						$id_buku_besar = -1;
						if (isset($check_buku_besar))
						{
							if ($check_buku_besar->saldo_kredit > 0)
							{
								$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
								if ($check_buku_besar->saldo_kredit < 0)
								{
									$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
									$check_buku_besar->saldo_kredit = 0;
								}
							}
							else
								$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;
							$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
								'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
								'saldo_debit'	=> $check_buku_besar->saldo_debit,
								'saldo_kredit'	=> $check_buku_besar->saldo_kredit
							]);
							$id_buku_besar = $check_buku_besar->id_buku_besar;

							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								$data = [];
								$data['saldo_kredit'] 	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}
						}
						else
						{
							// $last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							// $id = -1;
							// if (!isset($last_row))
							// 	$id = 1;
							// else
							// 	$id = $last_row->id_buku_besar;
							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							if (isset($last_row))
							{
								$saldo_debit 	= 0;
								$saldo_kredit 	= 0;
								if ($last_row->saldo_kredit > 0)
								{
									$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
									if ($saldo_kredit < 0)
									{
										$saldo_debit = $saldo_kredit * (-1);
										$saldo_kredit = 0;
									}
								}
								else
									$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_aft,
									'kredit'		=> 0,
									'saldo_debit'	=> $saldo_debit,
									'saldo_kredit'	=> $saldo_kredit,
									'id_aktivitas'	=> 4
								];
							}
							else
							{
								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_aft,
									'kredit'		=> 0,
									'saldo_debit'	=> $simpanan_sukarela_aft,
									'saldo_kredit'	=> 0,
									'id_aktivitas'	=> 4
								];
							}
							$this->buku_besar_m->insert($this->data['entri']);
							$id_buku_besar = $this->db->insert_id();

							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								$data = [];
								$data['saldo_kredit'] 	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}
						}
					}
				}
			}
			else
			{
				$check_buku_besar_bfr = $this->buku_besar_m->get_row([
					'tgl'			=> $tanggal_bfr,
					'id_aktivitas'	=> 1
				]);
				if ($tanggal_aft > $tanggal_bfr)
				{
					// case 1
					$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row(['id_aktivitas' => 1, 'tgl' => $tanggal_bfr]);
					if (isset($check_jurnal_umum_bfr))
					{
						$debit = $check_jurnal_umum_bfr->debit - $simpanan_wajib_bfr;
						if ($debit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 1
							], [
								'debit'			=> $debit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 1
					]);

					if (isset($check_jurnal_umum_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'debit'		=> $check_jurnal_umum_aft->debit + $simpanan_wajib_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Simpanan Wajib',
							'debit'			=> $simpanan_wajib_aft,
							'kredit'		=> 0,
							'id_aktivitas'	=> 1
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}

					$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row(['id_aktivitas' => 4, 'tgl' => $tanggal_bfr]);
					if (isset($check_jurnal_umum_bfr))
					{
						$debit = $check_jurnal_umum_bfr->debit - $simpanan_sukarela_bfr;
						if ($debit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 1
							], [
								'debit'			=> $debit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 1
					]);

					if (isset($check_jurnal_umum_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'debit'		=> $check_jurnal_umum_aft->debit + $simpanan_sukarela_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Simpanan Sukarela',
							'debit'			=> $simpanan_sukarela_aft,
							'kredit'		=> 0,
							'id_aktivitas'	=> 4
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}

					if (isset($check_buku_besar_bfr))
					{
						$data = [];
						$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_wajib_bfr;
						$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
						$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
						if ($check_buku_besar_bfr->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $simpanan_wajib_bfr;

						if ($data['debit'] <= 0)
							$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
						else
						{
							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 1
							], $data);
						}
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 1,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					if (isset($check_buku_besar_bfr))
					{
						foreach ($check_buku_besar_bfr as $row)
						{
							$data = [];
							$data['saldo_debit']	= $row->saldo_debit;
							$data['saldo_kredit']	= $row->saldo_kredit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_wajib_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $simpanan_wajib_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft, 'id_buku_besar <' => $check_buku_besar->id_buku_besar], 'tgl');

						if ($last_row->saldo_kredit > 0)
						{
							$last_row->saldo_kredit -= $simpanan_wajib_aft;
							if ($last_row->saldo_kredit < 0)
							{
								$last_row->saldo_debit = $last_row->saldo_kredit * (-1);
								$last_row->saldo_kredit = 0;	
							}
						}
						else
							$last_row->saldo_debit += $simpanan_wajib_aft;

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $simpanan_wajib_aft,
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;

						$check_buku_besar_bfr = $this->buku_besar_m->get([
							'tgl'				=> $tanggal_aft,
							'id_aktivitas !='	=> 1,
							'id_buku_besar <'	=> $id_buku_besar
						]);
						foreach ($check_buku_besar_bfr as $row)
						{
							$data = [];
							$data['saldo_debit']	= $row->saldo_debit;
							$data['saldo_kredit']	= $row->saldo_kredit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_wajib_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $simpanan_wajib_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $simpanan_wajib_bfr;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $simpanan_wajib_bfr;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_bfr,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 1
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_bfr,
								'kredit'		=> 0,
								'saldo_debit'	=> $simpanan_wajib_bfr,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 1
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 1.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;
									}	
								}
								else
									$check_buku_besar->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;

								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'saldo_kredit'	=> 0,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 1.2
							$data = [];
							$data['debit'] 		= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);

							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								// $last_row = $this->buku_besar_m->get_last_row();
								// $id = -1;
								// if (!isset($last_row))
								// 	$id = 1;
								// else
								// 	$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_debit > 0)
								{
									$check_buku_besar->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($check_buku_besar->saldo_debit < 0)
									{
										$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
										$check_buku_besar->saldo_debit = 0;	
									}
								}
								else
									$check_buku_besar->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);				
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								$id = -1;
								if (!isset($last_row))
									$id = 1;
								else
									$id = $last_row->id_buku_besar;
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <=' => $id, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_debit > 0)
									{
										$row->saldo_debit -= $simpanan_sukarela_bfr;
										if ($row->saldo_debit < 0)
										{
											$row->saldo_kredit = $row->saldo_debit * (-1);
											$row->saldo_debit = 0;
										}
									}
									else
										$row->saldo_kredit += $simpanan_sukarela_bfr;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_kredit'	=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
					else
					{
						// case 1.3
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);

						if (isset($check_buku_besar_bfr))
						{
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}
						}

						$check_buku_besar_bfr = $this->buku_besar_m->get([
							'tgl'				=> $tanggal_bfr,
							'id_aktivitas !='	=> 4,
							'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
						]);
						if (isset($check_buku_besar_bfr))
						{
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}
						}

						$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
						foreach ($check_buku_besar_itr as $row)
						{
							$data = [];
							$data['saldo_debit']	= $row->saldo_debit;
							$data['saldo_kredit']	= $row->saldo_kredit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
						$id_buku_besar = -1;
						if (isset($check_buku_besar))
						{
							$last_row = $this->buku_besar_m->get_last_row(['tgl <' => $tanggal_aft], 'tgl');

							if ($last_row->saldo_kredit > 0)
							{
								$last_row->saldo_kredit -= $simpanan_sukarela_aft;
								if ($last_row->saldo_kredit < 0)
								{
									$last_row->saldo_debit = $last_row->saldo_kredit * (-1);
									$last_row->saldo_kredit = 0;	
								}
							}
							else
								$last_row->saldo_debit += $simpanan_sukarela_aft;

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

							if ($last_row->saldo_kredit > 0)
							{
								$last_row->saldo_kredit -= $check_buku_besar->debit;
								if ($last_row->saldo_kredit < 0)
								{
									$last_row->saldo_debit = $last_row->saldo_kredit * (-1);
									$last_row->saldo_kredit = 0;	
								}
							}
							else
								$last_row->saldo_debit += $check_buku_besar->debit;

							$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
								'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
								'saldo_debit'	=> $last_row->saldo_debit,
								'saldo_kredit'	=> $last_row->saldo_kredit
							]);
							$id_buku_besar = $check_buku_besar->id_buku_besar;
						}
						else
						{
							$last_row = $this->buku_besar_m->get_last_row();
							$id = -1;
							if (!isset($last_row))
								$id = 1;
							else
								$id = $last_row->id_buku_besar;
							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= $simpanan_sukarela_bfr;
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}

							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							if (isset($last_row))
							{
								$saldo_debit 	= 0;
								$saldo_kredit 	= 0;
								if ($last_row->saldo_kredit > 0)
								{
									$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_bfr;
									if ($saldo_kredit < 0)
									{
										$saldo_debit = $saldo_kredit * (-1);
										$saldo_kredit = 0;
									}
								}
								else
									$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_bfr;

								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_bfr,
									'kredit'		=> 0,
									'saldo_debit'	=> $saldo_debit,
									'saldo_kredit'	=> $saldo_kredit,
									'id_aktivitas'	=> 4
								];
							}
							else
							{
								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_bfr,
									'kredit'		=> 0,
									'saldo_debit'	=> $simpanan_sukarela_bfr,
									'saldo_kredit'	=> 0,
									'id_aktivitas'	=> 4
								];
							}
							$this->buku_besar_m->insert($this->data['entri']);
							$id_buku_besar = $this->db->insert_id();
						}
					}
				}
				else if ($tanggal_aft < $tanggal_bfr)
				{
					// case 2
					$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row(['id_aktivitas' => 1, 'tgl' => $tanggal_bfr]);
					if (isset($check_jurnal_umum_bfr))
					{
						$debit = $check_jurnal_umum_bfr->debit - $simpanan_wajib_bfr;
						if ($debit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 1
							], [
								'debit'		=> $debit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 1
					]);

					if (isset($check_jurnal_umum_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'debit'	=> $check_jurnal_umum_aft->debit + $simpanan_wajib_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Simpanan Wajib',
							'debit'			=> $simpanan_wajib_aft,
							'kredit'		=> 0,
							'id_aktivitas'	=> 1
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}

					$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row(['id_aktivitas' => 4, 'tgl' => $tanggal_bfr]);
					if (isset($check_jurnal_umum_bfr))
					{
						$debit = $check_jurnal_umum_bfr->debit - $simpanan_sukarela_bfr;
						if ($debit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							], [
								'debit'		=> $debit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 4
					]);

					if (isset($check_jurnal_umum_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'debit'	=> $check_jurnal_umum_aft->debit + $simpanan_sukarela_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Simpanan Sukarela',
							'debit'			=> $simpanan_sukarela_aft,
							'kredit'		=> 0,
							'id_aktivitas'	=> 4
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}


					$data = [];
					$data['debit'] 	= $check_buku_besar_bfr->debit - $simpanan_wajib_bfr;
					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 1
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 1,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_kredit'] 	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $simpanan_wajib_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_kredit'] 	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $simpanan_wajib_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $simpanan_wajib_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 1]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= $simpanan_wajib_aft;
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;
							}
						}
						else
							$check_buku_besar->saldo_debit += $simpanan_wajib_aft;
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $simpanan_wajib_aft,
							'saldo_debit'	=> $check_buku_besar->saldo_debit,
							'saldo_kredit'	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;

						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_wajib_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $simpanan_wajib_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $simpanan_wajib_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 1
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Simpanan Wajib',
								'ref'			=> '103',
								'debit'			=> $simpanan_wajib_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $simpanan_wajib_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 1
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();

						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_wajib_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_wajib_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 2.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$id_buku_besar = $check_buku_besar_bfr->id_buku_besar;
							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar <'	=> $id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;
									}	
								}
								else
									$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;
								
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;

								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'saldo_kredit'	=> 0,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 2.2
							$data = [];
							$data['debit'] 		= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$id_buku_besar = $check_buku_besar_bfr->id_buku_besar;

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
							]);

							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar_itr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_aft;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_aft;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
							$id_buku_besar = -1;
							if (isset($check_buku_besar))
							{
								$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
								foreach ($check_buku_besar as $row)
								{
									if ($row->saldo_kredit > 0)
									{
										$row->saldo_kredit -= $simpanan_sukarela_aft;
										if ($row->saldo_kredit < 0)
										{
											$row->saldo_debit = $row->saldo_kredit * (-1);
											$row->saldo_kredit = 0;
										}
									}
									else
										$row->saldo_debit += $simpanan_sukarela_aft;

									$this->buku_besar_m->update($row->id_buku_besar, [
										'saldo_kredit'	=> $row->saldo_kredit,
										'saldo_debit'	=> $row->saldo_debit
									]);
								}

								$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);

								if ($check_buku_besar->saldo_kredit > 0)
								{
									$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
									if ($check_buku_besar->saldo_kredit < 0)
									{
										$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
										$check_buku_besar->saldo_kredit = 0;	
									}
								}
								else
									$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;				
								$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
									'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
									'saldo_debit' 	=> $check_buku_besar->saldo_debit,
									'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
								]);
								$id_buku_besar = $check_buku_besar->id_buku_besar;
							}
							else
							{
								$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
								if (isset($last_row))
								{
									$saldo_debit 	= 0;
									$saldo_kredit 	= 0;
									if ($last_row->saldo_kredit > 0)
									{
										$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
										if ($saldo_kredit < 0)
										{
											$saldo_debit = $saldo_kredit * (-1);
											$saldo_kredit = 0;
										}
									}
									else
										$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_debit'	=> $saldo_debit,
										'saldo_kredit'	=> $saldo_kredit,
										'id_aktivitas'	=> 4
									];
								}
								else
								{
									$this->data['entri'] = [
										'tgl'			=> $tanggal_aft,
										'ket'			=> 'Simpanan Sukarela',
										'ref'			=> '104',
										'debit'			=> $simpanan_sukarela_aft,
										'kredit'		=> 0,
										'saldo_kredit'	=> 0,
										'saldo_debit'	=> $simpanan_sukarela_aft,
										'id_aktivitas'	=> 4
									];
								}
								$this->buku_besar_m->insert($this->data['entri']);
								$id_buku_besar = $this->db->insert_id();
							}

							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
					else
					{
						// case 2.3
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);

						$data = [];
						$data['debit'] 	= $check_buku_besar_bfr->debit - $simpanan_sukarela_bfr;
						if ($data['debit'] <= 0)
							$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
						else
						{
							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							], $data);
						}

						$check_buku_besar_bfr = $this->buku_besar_m->get([
							'tgl'				=> $tanggal_bfr,
							'id_aktivitas !='	=> 4,
							'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
						]);
						foreach ($check_buku_besar_bfr as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
						foreach ($check_buku_besar_itr as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $simpanan_sukarela_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 4]);
						$id_buku_besar = -1;
						if (isset($check_buku_besar))
						{
							if ($check_buku_besar->saldo_kredit > 0)
							{
								$check_buku_besar->saldo_kredit -= $simpanan_sukarela_aft;
								if ($check_buku_besar->saldo_kredit < 0)
								{
									$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
									$check_buku_besar->saldo_kredit = 0;
								}
							}
							else
								$check_buku_besar->saldo_debit += $simpanan_sukarela_aft;
							$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
								'debit'			=> $check_buku_besar->debit + $simpanan_sukarela_aft,
								'saldo_debit'	=> $check_buku_besar->saldo_debit,
								'saldo_kredit'	=> $check_buku_besar->saldo_kredit
							]);
							$id_buku_besar = $check_buku_besar->id_buku_besar;

							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								$data = [];
								$data['saldo_kredit'] 	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}
						}
						else
						{
							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							$id = -1;
							if (!isset($last_row))
								$id = 1;
							else
								$id = $last_row->id_buku_besar;
							$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 4, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								$data = [];
								$data['saldo_kredit'] 	= $row->saldo_kredit;
								$data['saldo_debit']	= $row->saldo_debit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= $simpanan_sukarela_bfr;
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += $simpanan_sukarela_bfr;

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
							if (isset($last_row))
							{
								$saldo_debit 	= 0;
								$saldo_kredit 	= 0;
								if ($last_row->saldo_kredit > 0)
								{
									$saldo_kredit = $last_row->saldo_kredit - $simpanan_sukarela_aft;
									if ($saldo_kredit < 0)
									{
										$saldo_debit = $saldo_kredit * (-1);
										$saldo_kredit = 0;
									}
								}
								else
									$saldo_debit = $last_row->saldo_debit + $simpanan_sukarela_aft;

								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_aft,
									'kredit'		=> 0,
									'saldo_debit'	=> $saldo_debit,
									'saldo_kredit'	=> $saldo_kredit,
									'id_aktivitas'	=> 4
								];
							}
							else
							{
								$this->data['entri'] = [
									'tgl'			=> $tanggal_aft,
									'ket'			=> 'Simpanan Sukarela',
									'ref'			=> '104',
									'debit'			=> $simpanan_sukarela_aft,
									'kredit'		=> 0,
									'saldo_debit'	=> $simpanan_sukarela_aft,
									'saldo_kredit'	=> 0,
									'id_aktivitas'	=> 4
								];
							}
							$this->buku_besar_m->insert($this->data['entri']);
							$id_buku_besar = $this->db->insert_id();
						}
					}
				}
				else if ($simpanan_wajib_aft > $simpanan_wajib_bfr)
				{
					// case 3
					$check_jurnal_umum = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 1]);
					$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
						'debit'	=> $check_jurnal_umum->debit + ($simpanan_wajib_aft - $simpanan_wajib_bfr)
					]);

					$data = [];
					$data['debit']			= $check_buku_besar_bfr->debit + ($simpanan_wajib_aft - $simpanan_wajib_bfr);
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($data['saldo_kredit'] > 0)
					{
						$data['saldo_kredit'] -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

					$this->buku_besar_m->update($check_buku_besar_bfr->id_buku_besar, $data);

					$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar, 'tgl' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($simpanan_wajib_aft - $simpanan_wajib_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 3.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit + ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							], $data);

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 3.2
							$data = [];
							$data['debit'] 		= $check_buku_besar_bfr->debit - ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);

							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get_row([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							]);
							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
				}
				else
				{
					// case 4
					$check_jurnal_umum = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 1]);
					$debit = $check_jurnal_umum->debit + ($simpanan_wajib_aft - $simpanan_wajib_bfr);
					if ($debit <= 0)
						$this->jurnal_umum_m->delete($check_jurnal_umum->id_jurnal);
					else
						$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
							'debit'	=> $check_jurnal_umum->debit + ($simpanan_wajib_aft - $simpanan_wajib_bfr)
						]);

					$data = [];
					$data['debit']			= $check_buku_besar_bfr->debit + ($simpanan_wajib_aft - $simpanan_wajib_bfr);
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($data['saldo_kredit'] > 0)
					{
						$data['saldo_kredit'] -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += ($simpanan_wajib_aft - $simpanan_wajib_bfr);

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
						$this->buku_besar_m->update($check_buku_besar_bfr->id_buku_besar, $data);

					$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 1, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar, 'tgl' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($simpanan_wajib_bfr - $simpanan_wajib_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($simpanan_wajib_bfr - $simpanan_wajib_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($simpanan_wajib_bfr - $simpanan_wajib_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($simpanan_wajib_bfr - $simpanan_wajib_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					// ========================================================================

					if ($simpanan_sukarela_aft != $simpanan_sukarela_bfr)
					{
						$check_buku_besar_bfr = $this->buku_besar_m->get_row([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 4
						]);
						if ($simpanan_sukarela_aft > $simpanan_sukarela_bfr)
						{
							// case 4.1
							$data = [];
							$data['debit'] 			= $check_buku_besar_bfr->debit + ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							], $data);

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);
							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_kredit > 0)
								{
									$data['saldo_kredit'] -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($data['saldo_kredit'] < 0)
									{
										$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
										$data['saldo_kredit'] = 0;
									}
								}
								else
									$data['saldo_debit'] += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_kredit > 0)
								{
									$row->saldo_kredit -= ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);
									if ($row->saldo_kredit < 0)
									{
										$row->saldo_debit = $row->saldo_kredit * (-1);
										$row->saldo_kredit = 0;
									}
								}
								else
									$row->saldo_debit += ($simpanan_sukarela_aft - $simpanan_sukarela_bfr);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
						else
						{
							// case 4.2
							$data = [];
							$data['debit'] 		= $check_buku_besar_bfr->debit - ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
							$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
							$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
							if ($check_buku_besar_bfr->saldo_debit > 0)
							{
								$data['saldo_debit'] -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

							if ($data['debit'] <= 0)
								$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
							else
							{
								$this->buku_besar_m->update_where([
									'tgl'			=> $tanggal_bfr,
									'id_aktivitas'	=> 4
								], $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get([
								'tgl'				=> $tanggal_bfr,
								'id_aktivitas !='	=> 4,
								'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
							]);

							foreach ($check_buku_besar_bfr as $row)
							{
								$data = [];
								$data['saldo_debit']	= $row->saldo_debit;
								$data['saldo_kredit']	= $row->saldo_kredit;
								if ($row->saldo_debit > 0)
								{
									$data['saldo_debit'] -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($data['saldo_debit'] < 0)
									{
										$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
										$data['saldo_debit'] = 0;
									}
								}
								else
									$data['saldo_kredit'] += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, $data);
							}

							$check_buku_besar_bfr = $this->buku_besar_m->get_row([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 4
							]);
							$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 4, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						
							$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
							foreach ($check_buku_besar as $row)
							{
								if ($row->saldo_debit > 0)
								{
									$row->saldo_debit -= ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);
									if ($row->saldo_debit < 0)
									{
										$row->saldo_kredit = $row->saldo_debit * (-1);
										$row->saldo_debit = 0;
									}
								}
								else
									$row->saldo_kredit += ($simpanan_sukarela_bfr - $simpanan_sukarela_aft);

								$this->buku_besar_m->update($row->id_buku_besar, [
									'saldo_kredit'	=> $row->saldo_kredit,
									'saldo_debit'	=> $row->saldo_debit
								]);
							}
						}
					}
				}
			}

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
			$simpanan_bfr = $this->simpanan_m->get_row(['id_simpanan' => $this->POST('id_simpanan')]);
			$this->simpanan_m->delete($this->POST('id_simpanan'));
			$tanggal = $simpanan_bfr->tgl_simpanan;

			$this->load->model('jurnal_umum_m');
			$check_jurnal_umum = $this->jurnal_umum_m->get(['tgl' => $tanggal]);
			foreach ($check_jurnal_umum as $row)
			{
				$row->debit -= ($row->id_aktivitas == 1) ? $simpanan_bfr->simpanan_wajib : $simpanan_bfr->simpanan_sukarela;
				$row->kredit -= ($row->id_aktivitas == 1) ? $simpanan_bfr->simpanan_wajib : $simpanan_bfr->simpanan_sukarela;

				if ($row->debit <= 0 or $row->kredit <= 0)
					$this->jurnal_umum_m->delete_by(['id_jurnal' => $row->id_jurnal]);
				else
					$this->jurnal_umum_m->update($row->id_jurnal, [
						'debit'		=> $row->debit,
						'kredit'	=> $row->kredit
					]);
			}
			
			$debit = $simpanan_bfr->simpanan_wajib + $simpanan_bfr->simpanan_sukarela;
			
			$this->load->model('buku_besar_m');
			$buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal, 'id_aktivitas' => 1]);
			$buku_besar->debit -= $simpanan_bfr->simpanan_wajib;
			if ($buku_besar->debit <= 0)
				$this->buku_besar_m->delete($buku_besar->id_buku_besar);
			else
			{
				if ($buku_besar->saldo_kredit > 0)
					$buku_besar->saldo_kredit += $simpanan_bfr->simpanan_wajib;
				else
				{
					$buku_besar->saldo_debit -= $simpanan_bfr->simpanan_wajib;
					if ($buku_besar->saldo_debit < 0)
					{
						$buku_besar->saldo_kredit = $buku_besar->saldo_debit * (-1);
						$buku_besar->saldo_debit = 0;
					}
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'debit'			=> $buku_besar->debit,
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);

				$buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal, 'id_aktivitas' => 4]);
				if ($buku_besar->saldo_kredit > 0)
					$buku_besar->saldo_kredit += $simpanan_bfr->simpanan_wajib;
				else
				{
					$buku_besar->saldo_debit -= $simpanan_bfr->simpanan_wajib;
					if ($buku_besar->saldo_debit < 0)
					{
						$buku_besar->saldo_kredit = $buku_besar->saldo_debit * (-1);
						$buku_besar->saldo_debit = 0;
					}
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);
			}

			$buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal, 'id_aktivitas' => 4]);
			$buku_besar->debit -= $simpanan_bfr->simpanan_wajib;
			if ($buku_besar->debit <= 0)
				$this->buku_besar_m->delete($buku_besar->id_buku_besar);
			else
			{
				if ($buku_besar->saldo_kredit > 0)
					$buku_besar->saldo_kredit += $simpanan_bfr->simpanan_wajib;
				else
				{
					$buku_besar->saldo_debit -= $simpanan_bfr->simpanan_wajib;
					if ($buku_besar->saldo_debit < 0)
					{
						$buku_besar->saldo_kredit = $buku_besar->saldo_debit * (-1);
						$buku_besar->saldo_debit = 0;
					}
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'debit'			=> $buku_besar->debit,
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);
			}

			$buku_besar_s = $this->buku_besar_m->get(['tgl' => $tanggal, 'id_aktivitas !=' => 4, 'id_aktivitas !=' => 1]);
			
			foreach ($buku_besar_s as $buku_besar)
			{
				if ($buku_besar->saldo_debit > 0)
				{
					$buku_besar->saldo_debit -= $simpanan_bfr->simpanan_wajib + $simpanan_bfr->simpanan_sukarela;
					if ($buku_besar->saldo_debit < 0)
					{
						$buku_besar->saldo_kredit = $buku_besar->saldo_debit * (-1);
						$buku_besar->saldo_debit = 0;
					}
				}
				else
				{
					$buku_besar->saldo_kredit += $simpanan_bfr->simpanan_wajib + $simpanan_bfr->simpanan_sukarela;
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);
			}

			$buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal]);
			foreach ($buku_besar as $row)
			{
				if ($row->saldo_debit > 0)
				{
					$row->saldo_debit -= $simpanan_bfr->simpanan_wajib + $simpanan_bfr->simpanan_sukarela;
					if ($row->saldo_debit < 0)
					{
						$row->saldo_kredit = $row->saldo_debit * (-1);
						$row->saldo_debit = 0;
					}
				}
				else
				{
					$row->saldo_kredit += $simpanan_bfr->simpanan_wajib + $simpanan_bfr->simpanan_sukarela;
				}

				$this->buku_besar_m->update($row->id_buku_besar, [
					'saldo_debit'	=> $row->saldo_debit,
					'saldo_kredit'	=> $row->saldo_kredit
				]);
			}

			exit;
		}

		$this->data['title'] 	= "Data Simpanan | KOHIWAS";
		$this->data['content']	= "admin/data_simpanan";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['simpanan']	= $this->simpanan_m->get_by_order('id_simpanan', 'ASC');
		$this->template($this->data);
	}

	// 1
	public function data_pinjaman()
	{
		$this->load->model('anggota_m');
		$this->load->model('pinjaman_m');
		$this->load->model('jurnal_umum_m');
		$this->load->model('buku_besar_m');
		
		if ($this->POST('simpan'))
		{
			$bunga 		= 0.01 * $this->POST('jlh_pinjaman');
			$total 		= $bunga + $this->POST('jlh_pinjaman');
			$angsuran 	= $total/$this->POST('lama_pinjaman');

			$this->data['pinjaman'] = [
				'id_anggota'		=> $this->POST('id_anggota'),
				'tgl_pinjaman'		=> $this->POST('tgl_pinjaman'),
				'jlh_pinjaman'		=> $this->POST('jlh_pinjaman'),
				'bunga'				=> $bunga,
				'ttl_pinjaman'		=> $total,
				'lama_pinjaman'		=> $this->POST('lama_pinjaman'),
				'angsuran'			=> $angsuran
			];
			
			$this->pinjaman_m->insert($this->data['pinjaman']);

			$this->load->model('jurnal_umum_m');
			$check_jurnal_umum = $this->jurnal_umum_m->get_row(['id_aktivitas' => 2, 'tgl' => $this->data['pinjaman']['tgl_pinjaman']]);
			if (isset($check_jurnal_umum))
			{
				$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
					'kredit' 	=> $check_jurnal_umum->kredit + $this->data['pinjaman']['jlh_pinjaman']
				]);
			}
			else
			{
				$this->data['entri'] = [
					'tgl'			=> $this->data['pinjaman']['tgl_pinjaman'],
					'ket'			=> 'Pinjaman',
					'debit'			=> 0,
					'kredit'		=> $this->data['pinjaman']['jlh_pinjaman'],
					'id_aktivitas'	=> 2
				];
				$this->jurnal_umum_m->insert($this->data['entri']);
			}

			$this->load->model('buku_besar_m');
			$temp = $this->buku_besar_m->get_last_row(['tgl <' => $this->data['pinjaman']['tgl_pinjaman']], 'tgl');
			if (isset($temp))
				$this->data['saldo_debit'] 	= $temp->saldo_debit;
			if (!isset($this->data['saldo_debit']))
				$this->data['saldo_debit'] = 0;
			if (isset($temp))
				$this->data['saldo_kredit']	= $temp->saldo_kredit;
			if (!isset($this->data['saldo_kredit']))
				$this->data['saldo_kredit'] = 0;
			if (isset($this->data['saldo_debit']) && $this->data['saldo_debit'] > 0)
			{
				$this->data['saldo_debit'] -= $this->data['pinjaman']['jlh_pinjaman'];
				if ($this->data['saldo_debit'] < 0)
				{
					$this->data['saldo_kredit'] = $this->data['saldo_debit'] * (-1);
					$this->data['saldo_debit'] 	= 0;
				}
			}
			else if (isset($this->data['saldo_kredit']) && $this->data['saldo_kredit'] >= 0)
			{
				$this->data['saldo_kredit'] += $this->data['pinjaman']['jlh_pinjaman'];
			}

			$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $this->data['pinjaman']['tgl_pinjaman'], 'id_aktivitas' => 2]);
			$id_buku_besar = -1;
			if (isset($check_buku_besar))
			{
				if ($check_buku_besar->saldo_debit > 0)
				{
					$check_buku_besar->saldo_debit -= $this->data['pinjaman']['jlh_pinjaman'];
					if ($check_buku_besar->saldo_debit < 0)
					{
						$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
						$check_buku_besar->saldo_debit = 0;
					}
				}
				else
				{
					$check_buku_besar->saldo_kredit += $this->data['pinjaman']['jlh_pinjaman'];
				}

				$data = [
					'kredit'		=> $check_buku_besar->kredit + $this->data['pinjaman']['jlh_pinjaman'],
					'saldo_kredit'	=> $check_buku_besar->saldo_kredit,
					'saldo_debit'	=> $check_buku_besar->saldo_debit
				];

				$this->buku_besar_m->update($check_buku_besar->id_buku_besar, $data);
				$id_buku_besar = $check_buku_besar->id_buku_besar;
			}
			else
			{
				$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $this->data['pinjaman']['tgl_pinjaman']], 'tgl');
				if (isset($last_row))
				{
					if ($last_row->saldo_debit > 0)
					{
						$last_row->saldo_debit -= $this->data['pinjaman']['jlh_pinjaman'];
						if ($last_row->saldo_debit < 0)
						{
							$last_row->saldo_kredit = $last_row->saldo_debit * (-1);
							$last_row->saldo_debit = 0;
						}
					}
					else
						$last_row->saldo_kredit += $this->data['pinjaman']['jlh_pinjaman'];


					$this->data['entri'] = [
						'tgl'			=> $this->data['pinjaman']['tgl_pinjaman'],
						'ket'			=> 'Pinjaman',
						'ref'			=> '105',
						'debit'			=> 0,
						'kredit'		=> $this->data['pinjaman']['jlh_pinjaman'],
						'saldo_debit'	=> $last_row->saldo_debit,
						'saldo_kredit'	=> $last_row->saldo_kredit,
						'id_aktivitas'	=> 2
					];

					$this->buku_besar_m->insert($this->data['entri']);
				}
				else
				{
					$this->data['entri'] = [
						'tgl'			=> $this->data['pinjaman']['tgl_pinjaman'],
						'ket'			=> 'Pinjaman',
						'ref'			=> '105',
						'debit'			=> 0,
						'kredit'		=> $this->data['pinjaman']['jlh_pinjaman'],
						'saldo_debit'	=> 0,
						'saldo_kredit'	=> $this->data['pinjaman']['jlh_pinjaman'],
						'id_aktivitas'	=> 2
					];

					$this->buku_besar_m->insert($this->data['entri']);
				}
				$id_buku_besar = $this->db->insert_id();
			}

			// update data pada tgl yg sama namun id_aktivitas yg berbeda
			$check_buku_besar = $this->buku_besar_m->get(['tgl' => $this->data['pinjaman']['tgl_pinjaman'], 'id_aktivitas !=' => 2, 'id_buku_besar >' => $id_buku_besar]);
			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_debit > 0)
				{
					$data['saldo_debit'] = $saldo_debit - $this->data['pinjaman']['jlh_pinjaman'];
					if ($data['saldo_debit'] < 0)
					{
						$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
						$data['saldo_debit'] = 0;
					}
				}
				else
					$data['saldo_kredit'] = $saldo_kredit + $this->data['pinjaman']['jlh_pinjaman'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $this->data['pinjaman']['tgl_pinjaman']]);
			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_debit > 0)
				{
					$data['saldo_debit'] = $saldo_debit - $this->data['pinjaman']['jlh_pinjaman'];
					if ($data['saldo_debit'] < 0)
					{
						$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
						$data['saldo_debit'] = 0;
					}
				}
				else
					$data['saldo_kredit'] = $saldo_kredit + $this->data['pinjaman']['jlh_pinjaman'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$this->flashmsg('Data pinjaman berhasil disimpan');
			redirect('admin/data_pinjaman');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_pinjaman'))
		{

			$edit_bunga 	= 0.01 * $this->POST('edit_jlh_pinjaman');
			$edit_total 	= $edit_bunga + $this->POST('edit_jlh_pinjaman');
			$edit_angsuran 	= $edit_total/$this->POST('edit_lama_pinjaman');

			$pinjaman_bfr = $this->pinjaman_m->get_row(['id_pinjaman' => $this->POST('edit_id_pinjaman')]);

			$jlh_pinjaman_bfr 	= $pinjaman_bfr->jlh_pinjaman;
			$tanggal_bfr 		= $pinjaman_bfr->tgl_pinjaman;
			$jlh_pinjaman_aft 	= $this->POST('edit_jlh_pinjaman');
			$tanggal_aft 		= $this->POST('edit_tgl_pinjaman');

			$this->data['pinjaman'] = [
				'id_anggota'		=> $this->POST('edit_id_anggota'),
				'tgl_pinjaman'		=> $this->POST('edit_tgl_pinjaman'),
				'jlh_pinjaman'		=> $this->POST('edit_jlh_pinjaman'),
				'bunga'				=> $edit_bunga,
				'ttl_pinjaman'		=> $edit_total,
				'lama_pinjaman'		=> $this->POST('edit_lama_pinjaman'),
				'angsuran'			=> $edit_angsuran
			];

			$this->pinjaman_m->update($this->POST('edit_id_pinjaman'), $this->data['pinjaman']);

			$this->load->model('jurnal_umum_m');
			$this->load->model('buku_besar_m');

			$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row([
				'tgl'			=> $tanggal_bfr,
				'id_aktivitas'	=> 2
			]);

			if ($tanggal_aft != $tanggal_bfr && $jlh_pinjaman_aft != $jlh_pinjaman_bfr)
			{
				if (isset($check_jurnal_umum_bfr))
				{
					$kredit = $check_jurnal_umum_bfr->kredit - $jlh_pinjaman_aft;
					if ($kredit <= 0)
						$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
					else
					{
						$this->jurnal_umum_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 2
						], [
							'kredit'		=> $kredit
						]);
					}
				}

				$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
					'tgl'			=> $tanggal_aft,
					'id_aktivitas'	=> 2
				]);

				if (isset($check_jurnal_umum_aft))
				{
					$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
						'kredit'	=> $check_jurnal_umum_aft->kredit + $jlh_pinjaman_aft
					]);
				}
				else
				{
					$this->data['entri'] = [
						'tgl'			=> $tanggal_aft,
						'ket'			=> 'Pinjaman',
						'kredit'		=> $jlh_pinjaman_aft,
						'debit'			=> 0,
						'id_aktivitas'	=> 2
					];
					$this->jurnal_umum_m->insert($this->data['entri']);
				}

				$check_buku_besar_bfr = $this->buku_besar_m->get_row([
					'tgl'			=> $tanggal_bfr,
					'id_aktivitas'	=> 2
				]);
				if ($tanggal_aft > $tanggal_bfr && $jlh_pinjaman_aft > $jlh_pinjaman_bfr)
				{
					// case 5
					$data = [];
					$data['kredit'] 		= $check_buku_besar_bfr->kredit - $jlh_pinjaman_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_kredit > 0)
					{
						$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += $jlh_pinjaman_bfr;

					if ($data['kredit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 2
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 2,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_pinjaman_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_pinjaman_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						if ($check_buku_besar->saldo_debit > 0)
						{
							$check_buku_besar->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($check_buku_besar->saldo_debit < 0)
							{
								$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
								$check_buku_besar->saldo_debit = 0;
							}	
						}
						else
							$check_buku_besar->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
						
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'kredit'		=> $check_buku_besar->kredit + $jlh_pinjaman_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;

						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar <' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_pinjaman_bfr;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_pinjaman_bfr;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_debit > 0)
							{
								$saldo_debit = $last_row->saldo_debit - $jlh_pinjaman_aft;
								if ($saldo_debit < 0)
								{
									$saldo_kredit = $saldo_debit * (-1);
									$saldo_debit = 0;
								}
							}
							else
								$saldo_kredit = $last_row->saldo_kredit + $jlh_pinjaman_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 2
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> 0,
								'saldo_kredit'	=> $jlh_pinjaman_aft,
								'id_aktivitas'	=> 2
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 2, 'id_buku_besar >' => $id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				
					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
				else if ($tanggal_aft > $tanggal_bfr && $jlh_pinjaman_aft < $jlh_pinjaman_bfr)
				{
					// case 6
					$data = [];
					$data['kredit'] 		= $check_buku_besar_bfr->kredit - $jlh_pinjaman_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_kredit > 0)
					{
						$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += $jlh_pinjaman_bfr;

					if ($data['kredit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 2
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 2,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);

					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_pinjaman_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_pinjaman_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						// $last_row = $this->buku_besar_m->get_last_row();
						// $id = -1;
						// if (!isset($last_row))
						// 	$id = 1;
						// else
						// 	$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar <' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_pinjaman_bfr;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);

						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;	
							}
						}
						else
							$check_buku_besar->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);				
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'kredit'		=> $check_buku_besar->kredit + $jlh_pinjaman_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_pinjaman_bfr;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_debit > 0)
							{
								$saldo_debit = $last_row->saldo_debit - $jlh_pinjaman_aft;
								if ($saldo_debit < 0)
								{
									$saldo_kredit = $saldo_debit * (-1);
									$saldo_debit = 0;
								}
							}
							else
								$saldo_kredit = $last_row->saldo_kredit + $jlh_pinjaman_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 2
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_kredit'	=> $jlh_pinjaman_aft,
								'saldo_debit'	=> 0,
								'id_aktivitas'	=> 2
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 2, 'id_buku_besar >' => $id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				
					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
				else if ($tanggal_aft < $tanggal_bfr && $jlh_pinjaman_aft > $jlh_pinjaman_bfr)
				{
					// case 7
					$data = [];
					$data['kredit'] 		= $check_buku_besar_bfr->kredit - $jlh_pinjaman_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_debit > 0)
					{
						$data['saldo_debit'] -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

					if ($data['kredit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 2
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 2,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_pinjaman_aft;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_pinjaman_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_pinjaman_aft;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_pinjaman_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						// $last_row = $this->buku_besar_m->get_last_row();
						// $id = -1;
						// if (!isset($last_row))
						// 	$id = 1;
						// else
						// 	$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_pinjaman_aft;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_pinjaman_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);

						if ($check_buku_besar->saldo_debit > 0)
						{
							$check_buku_besar->saldo_debit -= $jlh_pinjaman_aft;
							if ($check_buku_besar->saldo_debit < 0)
							{
								$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
								$check_buku_besar->saldo_debit = 0;	
							}
						}
						else
							$check_buku_besar->saldo_kredit += $jlh_pinjaman_aft;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'kredit'		=> $check_buku_besar->kredit + $jlh_pinjaman_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_pinjaman_aft;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_pinjaman_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_debit > 0)
							{
								$saldo_debit = $last_row->saldo_debit - $jlh_pinjaman_aft;
								if ($saldo_debit < 0)
								{
									$saldo_kredit = $saldo_debit * (-1);
									$saldo_debit = 0;
								}
							}
							else
								$saldo_kredit = $last_row->saldo_kredit + $jlh_pinjaman_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 2
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_kredit'	=> $jlh_pinjaman_aft,
								'saldo_debit'	=> 0,
								'id_aktivitas'	=> 2
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_buku_besar >' => $id_buku_besar, 'id_aktivitas !=' => 2]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
				else
				{
					// case 8
					$data = [];
					$data['kredit'] 		= $check_buku_besar_bfr->kredit - $jlh_pinjaman_bfr;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					if ($check_buku_besar_bfr->saldo_kredit > 0)
					{
						$data['saldo_kredit'] -= ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);

					if ($data['kredit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 2
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 2,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_kredit']	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_pinjaman_aft;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_pinjaman_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_kredit']	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_pinjaman_aft;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_pinjaman_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						// $last_row = $this->buku_besar_m->get_last_row();
						// if (!isset($last_row))
						// 	$id_buku_besar = 1;
						// else
						// 	$id_buku_besar = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $check_buku_besar->id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_pinjaman_aft;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_pinjaman_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);

						if ($check_buku_besar->saldo_debit > 0)
						{
							$check_buku_besar->saldo_debit -= $jlh_pinjaman_aft;
							if ($check_buku_besar->saldo_debit < 0)
							{
								$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
								$check_buku_besar->saldo_debit = 0;
							}	
						}
						else
							$check_buku_besar->saldo_kredit += $jlh_pinjaman_aft;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'kredit'		=> $check_buku_besar->kredit + $jlh_pinjaman_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						if (!isset($last_row))
							$id_buku_besar = 1;
						else
							$id_buku_besar = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_pinjaman_aft;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_pinjaman_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_debit > 0)
							{
								$saldo_debit = $last_row->saldo_debit - $jlh_pinjaman_aft;
								if ($saldo_debit < 0)
								{
									$saldo_kredit = $saldo_debit * (-1);
									$saldo_debit = 0;
								}
							}
							else
								$saldo_kredit = $last_row->saldo_kredit + $jlh_pinjaman_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 2
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> 0,
								'saldo_kredit'	=> $jlh_pinjaman_aft,
								'id_aktivitas'	=> 2
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get_row([
						'tgl'			=> $tanggal_bfr,
						'id_aktivitas'	=> 2
					]);
					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_bfr, 'id_aktivitas !=' => 2, 'id_buku_besar >' => $id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
			}
			else
			{
				$check_buku_besar_bfr = $this->buku_besar_m->get_row([
					'tgl'			=> $tanggal_bfr,
					'id_aktivitas'	=> 2
				]);
				if ($tanggal_aft > $tanggal_bfr)
				{
					// case 1
					if (isset($check_jurnal_umum_bfr))
					{
						$kredit = $check_jurnal_umum_bfr->kredit - $jlh_pinjaman_bfr;
						if ($kredit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 2
							], [
								'kredit'		=> $kredit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 2
					]);

					if (isset($check_simpanan_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'kredit'	=> $check_jurnal_umum_aft->kredit + $jlh_pinjaman_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Pinjaman',
							'debit'			=> 0,
							'kredit'		=> $jlh_pinjaman_aft,
							'id_aktivitas'	=> 2
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}

					if (isset($check_buku_besar_bfr))
					{
						$data = [];
						$data['kredit'] 		= $check_buku_besar_bfr->kredit - $jlh_pinjaman_bfr;
						$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
						$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
						if ($check_buku_besar_bfr->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_pinjaman_bfr;

						if ($data['kredit'] <= 0)
							$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
						else
						{
							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 2
							], $data);
						}
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 2,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					if (isset($check_buku_besar_bfr))
					{
						foreach ($check_buku_besar_bfr as $row)
						{
							$data = [];
							$data['saldo_debit']	= $row->saldo_debit;
							$data['saldo_kredit']	= $row->saldo_kredit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_pinjaman_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <' => $tanggal_aft], 'tgl');

						if ($last_row->saldo_debit > 0)
						{
							$last_row->saldo_debit -= $jlh_pinjaman_aft;
							if ($last_row->saldo_debit < 0)
							{
								$last_row->saldo_kredit = $last_row->saldo_debit * (-1);
								$last_row->saldo_debit = 0;	
							}
						}
						else
							$last_row->saldo_kredit += $jlh_pinjaman_aft;

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);

						if ($last_row->saldo_debit > 0)
						{
							$last_row->saldo_debit -= $check_buku_besar->kredit;
							if ($last_row->saldo_debit < 0)
							{
								$last_row->saldo_kredit = $last_row->saldo_debit * (-1);
								$last_row->saldo_debit = 0;	
							}
						}
						else
							$last_row->saldo_kredit += $check_buku_besar->kredit;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'kredit'		=> $check_buku_besar->kredit + $jlh_pinjaman_aft,
							'saldo_debit'	=> $last_row->saldo_debit,
							'saldo_kredit'	=> $last_row->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_pinjaman_bfr;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_debit > 0)
							{
								$saldo_debit = $last_row->saldo_debit - $jlh_pinjaman_bfr;
								if ($saldo_debit < 0)
								{
									$saldo_kredit = $saldo_debit * (-1);
									$saldo_debit = 0;
								}
							}
							else
								$saldo_kredit = $last_row->saldo_kredit + $jlh_pinjaman_bfr;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_bfr,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 2
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_bfr,
								'saldo_debit'	=> 0,
								'saldo_kredit'	=> $jlh_pinjaman_bfr,
								'id_aktivitas'	=> 2
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}
				}
				else if ($tanggal_aft < $tanggal_bfr)
				{
					// case 2
					if (isset($check_jurnal_umum_bfr))
					{
						$kredit = $check_jurnal_umum_bfr->kredit - $jlh_pinjaman_bfr;
						if ($kredit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 2
							], [
								'kredit'		=> $kredit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 2
					]);

					if (isset($check_simpanan_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'kredit'	=> $check_jurnal_umum_aft->kredit + $jlh_pinjaman_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Pinjaman',
							'debit'			=> 0,
							'kredit'		=> $jlh_pinjaman_aft,
							'id_aktivitas'	=> 2
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}

					$data = [];
					$data['kredit'] 	= $check_buku_besar_bfr->kredit - $jlh_pinjaman_bfr;
					if ($data['kredit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 2
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 2,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_kredit'] 	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_pinjaman_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_kredit'] 	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_pinjaman_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_pinjaman_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 2]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						if ($check_buku_besar->saldo_debit > 0)
						{
							$check_buku_besar->saldo_debit -= $jlh_pinjaman_aft;
							if ($check_buku_besar->saldo_debit < 0)
							{
								$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
								$check_buku_besar->saldo_debit = 0;
							}
						}
						else
							$check_buku_besar->saldo_kredit += $jlh_pinjaman_aft;
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'kredit'		=> $check_buku_besar->kredit + $jlh_pinjaman_aft,
							'saldo_debit'	=> $check_buku_besar->saldo_debit,
							'saldo_kredit'	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;

						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $jlh_pinjaman_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $jlh_pinjaman_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $jlh_pinjaman_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_debit > 0)
							{
								$saldo_debit = $last_row->saldo_debit - $jlh_pinjaman_aft;
								if ($saldo_debit < 0)
								{
									$saldo_kredit = $saldo_debit * (-1);
									$saldo_debit = 0;
								}
							}
							else
								$saldo_kredit = $last_row->saldo_kredit + $jlh_pinjaman_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 2
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Pinjaman',
								'ref'			=> '105',
								'debit'			=> 0,
								'kredit'		=> $jlh_pinjaman_aft,
								'saldo_debit'	=> 0,
								'saldo_kredit'	=> $jlh_pinjaman_aft,
								'id_aktivitas'	=> 2
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}
				}
				else if ($jlh_dibayar_aft > $jlh_dibayar_bfr)
				{
					// case 3
					$check_jurnal_umum = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 2]);
					$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
						'kredit'	=> $check_jurnal_umum->kredit + ($jlh_pinjaman_aft - $jlh_pinjaman_bfr)
					]);

					$data = [];
					$data['kredit']			= $check_buku_besar_bfr->kredit + ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($data['saldo_debit'] > 0)
					{
						$data['saldo_debit'] -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

					$this->buku_besar_m->update($check_buku_besar_bfr->id_buku_besar, $data);

					$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar, 'tgl' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}
				}
				else
				{
					// case 4
					$check_jurnal_umum = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 2]);
					$kredit = $check_jurnal_umum->kredit + ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
					if ($kredit <= 0)
						$this->jurnal_umum_m->delete($check_jurnal_umum->id_jurnal);
					else
						$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
							'kredit'	=> $check_jurnal_umum->kredit + ($jlh_pinjaman_aft - $jlh_pinjaman_bfr)
						]);

					$data = [];
					$data['kredit']			= $check_buku_besar_bfr->kredit + ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($data['saldo_debit'] > 0)
					{
						$data['saldo_debit'] -= ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += ($jlh_pinjaman_aft - $jlh_pinjaman_bfr);

					if ($data['kredit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
						$this->buku_besar_m->update($check_buku_besar_bfr->id_buku_besar, $data);

					$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 2, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar, 'tgl' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_pinjaman_bfr - $jlh_pinjaman_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}
				}
			}

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
			$pinjaman_bfr = $this->pinjaman_m->get_row(['id_pinjaman' => $this->POST('id_pinjaman')]);
			$this->pinjaman_m->delete($this->POST('id_pinjaman'));
			$tanggal = $pinjaman_bfr->tgl_pinjaman;

			$this->load->model('jurnal_umum_m');
			$check_jurnal_umum = $this->jurnal_umum_m->get(['tgl' => $tanggal]);
			foreach ($check_jurnal_umum as $row)
			{
				$row->kredit -= $pinjaman_bfr->jlh_pinjaman;

				if ($row->kredit <= 0)
					$this->jurnal_umum_m->delete_by(['id_jurnal' => $row->id_jurnal]);
				else
					$this->jurnal_umum_m->update($row->id_jurnal, [
						'kredit'	=> $row->kredit
					]);
			}
			
			$kredit = $pinjaman_bfr->jlh_pinjaman;
			
			$this->load->model('buku_besar_m');
			$buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal, 'id_aktivitas' => 2]);
			$buku_besar->kredit -= $pinjaman_bfr->jlh_pinjaman;
			if ($buku_besar->kredit <= 0)
				$this->buku_besar_m->delete($buku_besar->id_buku_besar);
			else
			{
				if ($buku_besar->saldo_debit > 0)
					$buku_besar->saldo_debit += $pinjaman_bfr->jlh_pinjaman;
				else
				{
					$buku_besar->saldo_kredit -= $pinjaman_bfr->jlh_pinjaman;
					if ($buku_besar->saldo_kredit < 0)
					{
						$buku_besar->saldo_debit = $buku_besar->saldo_kredit * (-1);
						$buku_besar->saldo_kredit = 0;
					}
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'kredit'		=> $buku_besar->kredit,
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);
			}

			$buku_besar_s = $this->buku_besar_m->get(['tgl' => $tanggal, 'id_aktivitas !=' => 2]);
			
			foreach ($buku_besar_s as $buku_besar)
			{
				if ($buku_besar->saldo_kredit > 0)
				{
					$buku_besar->saldo_kredit -= $pinjaman_bfr->jlh_pinjaman;
					if ($buku_besar->saldo_kredit < 0)
					{
						$buku_besar->saldo_debit = $buku_besar->saldo_kredit * (-1);
						$buku_besar->saldo_kredit = 0;
					}
				}
				else
				{
					$buku_besar->saldo_debit += $pinjaman_bfr->jlh_pinjaman;
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);
			}

			$buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal]);
			foreach ($buku_besar as $row)
			{
				if ($row->saldo_kredit > 0)
				{
					$row->saldo_kredit -= $pinjaman_bfr->jlh_pinjaman;
					if ($row->saldo_kredit < 0)
					{
						$row->saldo_debit = $row->saldo_kredit * (-1);
						$row->saldo_kredit = 0;
					}
				}
				else
				{
					$row->saldo_debit += $pinjaman_bfr->jlh_pinjaman;
				}

				$this->buku_besar_m->update($row->id_buku_besar, [
					'saldo_debit'	=> $row->saldo_debit,
					'saldo_kredit'	=> $row->saldo_kredit
				]);
			}
			exit;
		}

		$this->data['title'] 	= "Data Pinjaman | KOHIWAS";
		$this->data['content']	= "admin/data_pinjaman";
		$this->data['anggota']	= $this->anggota_m->get_by_order('id_anggota', 'DESC');
		$this->data['pinjaman']	= $this->pinjaman_m->get_by_order('id_pinjaman', 'DESC');
		$this->template($this->data);
	}

	// 1
	public function data_angsuran()
	{
		$this->load->model('angsuran_m');
		$this->load->model('pinjaman_m');
		$this->load->model('jurnal_umum_m');
		$this->load->model('buku_besar_m');
		
		if ($this->POST('simpan'))
		{
			$cek_data_angsuran = $this->angsuran_m->get(['id_pinjaman' => $this->POST('id_pinjaman')]);
			if(!$cek_data_angsuran){
				$data_pinjaman = $this->pinjaman_m->get_row(['id_pinjaman' => $this->POST('id_pinjaman')]);
				$sisa_angsuran = $data_pinjaman->jlh_pinjaman - $this->POST('jlh_dibayar');
			}
			else{
				$data_angsuran2= $this->angsuran_m->get_by_order_limit('id_angsuran','DESC', ['id_pinjaman' => $this->POST('id_pinjaman')]);
				$sisa_angsuran = $data_angsuran2->sisa_angsuran - $this->POST('jlh_dibayar');	
			}

			$this->data['angsuran'] = [
				'id_pinjaman'		=> $this->POST('id_pinjaman'),
				'tgl_angsuran'		=> $this->POST('tgl_angsuran'),
				'jlh_dibayar'		=> $this->POST('jlh_dibayar'),
				'sisa_angsuran'		=> $sisa_angsuran
			];

			$this->angsuran_m->insert($this->data['angsuran']);

			$this->load->model('jurnal_umum_m');
			$check_jurnal_umum = $this->jurnal_umum_m->get_row(['id_aktivitas' => 3, 'tgl' => $this->data['angsuran']['tgl_angsuran']]);
			if (isset($check_jurnal_umum))
			{
				$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
					'debit' 	=> $check_jurnal_umum->debit + $this->data['angsuran']['jlh_dibayar']
				]);
			}
			else
			{
				$this->data['entri'] = [
					'tgl'			=> $this->data['angsuran']['tgl_angsuran'],
					'ket'			=> 'Angsuran',
					'debit'			=> $this->data['angsuran']['jlh_dibayar'],
					'kredit'		=> 0,
					'id_aktivitas'	=> 3
				];
				$this->jurnal_umum_m->insert($this->data['entri']);
			}

			$this->load->model('buku_besar_m');
			$temp = $this->buku_besar_m->get_last_row(['tgl <' => $this->data['angsuran']['tgl_angsuran']], 'tgl');
			if (isset($temp))
				$this->data['saldo_debit'] 	= $temp->saldo_debit;
			if (!isset($this->data['saldo_debit']))
				$this->data['saldo_debit'] = 0;
			if (isset($temp))
				$this->data['saldo_kredit']	= $temp->saldo_kredit;
			if (!isset($this->data['saldo_kredit']))
				$this->data['saldo_kredit'] = 0;
			if (isset($this->data['saldo_kredit']) && $this->data['saldo_kredit'] > 0)
			{
				$this->data['saldo_kredit'] -= $this->data['angsuran']['jlh_dibayar'];
				if ($this->data['saldo_kredit'] < 0)
				{
					$this->data['saldo_debit'] = $this->data['saldo_kredit'] * (-1);
					$this->data['saldo_kredit'] 	= 0;
				}
			}
			else if (isset($this->data['saldo_debit']) && $this->data['saldo_debit'] >= 0)
			{
				$this->data['saldo_debit'] += $this->data['angsuran']['jlh_dibayar'];
			}

			$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $this->data['angsuran']['tgl_angsuran'], 'id_aktivitas' => 3]);
			if (isset($check_buku_besar))
			{
				if ($check_buku_besar->saldo_kredit > 0)
				{
					$check_buku_besar->saldo_kredit -= $this->data['angsuran']['jlh_dibayar'];
					if ($check_buku_besar->saldo_kredit < 0)
					{
						$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
						$check_buku_besar->saldo_kredit = 0;
					}
				}
				else
				{
					$check_buku_besar->saldo_debit += $this->data['angsuran']['jlh_dibayar'];
				}

				$data = [
					'debit'		=> $check_buku_besar->debit + $this->data['angsuran']['jlh_dibayar'],
					'saldo_kredit'	=> $check_buku_besar->saldo_kredit,
					'saldo_debit'	=> $check_buku_besar->saldo_debit
				];

				$this->buku_besar_m->update($check_buku_besar->id_buku_besar, $data);
			}
			else
			{
				$this->data['entri'] = [
					'tgl'			=> $this->data['angsuran']['tgl_angsuran'],
					'ket'			=> 'Angsuran',
					'ref'			=> '106',
					'debit'			=> $this->data['angsuran']['jlh_dibayar'],
					'kredit'		=> 0,
					'saldo_debit'	=> $this->data['saldo_debit'],
					'saldo_kredit'	=> $this->data['saldo_kredit'],
					'id_aktivitas'	=> 3
				];

				$this->buku_besar_m->insert($this->data['entri']);
			}

			// update data pada tgl yg sama namun id_aktivitas yg berbeda
			$check_buku_besar = $this->buku_besar_m->get(['tgl' => $this->data['angsuran']['tgl_angsuran'], 'id_aktivitas !=' => 3]);
			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_kredit > 0)
				{
					$data['saldo_kredit'] = $saldo_kredit - $this->data['angsuran']['jlh_dibayar'];
					if ($data['saldo_kredit'] < 0)
					{
						$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
						$data['saldo_kredit'] = 0;
					}
				}
				else
					$data['saldo_debit'] = $saldo_debit + $this->data['angsuran']['jlh_dibayar'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $this->data['angsuran']['tgl_angsuran']]);
			foreach ($check_buku_besar as $row)
			{
				$saldo_debit = $row->saldo_debit;
				$saldo_kredit = $row->saldo_kredit;
				$data = [];

				if ($saldo_kredit > 0)
				{
					$data['saldo_kredit'] = $saldo_kredit - $this->data['angsuran']['jlh_dibayar'];
					if ($data['saldo_kredit'] < 0)
					{
						$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
						$data['saldo_kredit'] = 0;
					}
				}
				else
					$data['saldo_debit'] = $saldo_debit + $this->data['angsuran']['jlh_dibayar'];

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$this->flashmsg('Data angsuran berhasil disimpan');
			redirect('admin/data_angsuran');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_angsuran'))
		{
			$angsuran_bfr = $this->angsuran_m->get_row(['id_angsuran' => $this->POST('edit_id_angsuran')]);

			$jlh_dibayar_bfr 	= $angsuran_bfr->jlh_dibayar;
			$tanggal_bfr 		= $angsuran_bfr->tgl_angsuran;
			$jlh_dibayar_aft 	= $this->POST('edit_jlh_dibayar');
			$tanggal_aft 		= $this->POST('edit_tgl_angsuran');

			$this->data['angsuran'] = [
				'id_pinjaman'		=> $this->POST('edit_id_pinjaman'),
				'tgl_angsuran'		=> $this->POST('edit_tgl_angsuran'),
				'jlh_dibayar'		=> $this->POST('edit_jlh_dibayar'),
				'sisa_angsuran'		=> $this->POST('edit_sisa_angsuran')
			];

			$this->angsuran_m->update($this->POST('edit_id_angsuran'), $this->data['angsuran']);

			$this->load->model('jurnal_umum_m');
			$this->load->model('buku_besar_m');

			$check_jurnal_umum_bfr = $this->jurnal_umum_m->get_row([
				'tgl'			=> $tanggal_bfr,
				'id_aktivitas'	=> 3
			]);

			if ($tanggal_aft != $tanggal_bfr && $jlh_dibayar_aft != $jlh_dibayar_bfr)
			{
				if (isset($check_jurnal_umum_bfr))
				{
					$debit = $check_jurnal_umum_bfr->debit - $jlh_dibayar_bfr;
					if ($debit <= 0)
						$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
					else
					{
						$this->jurnal_umum_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 3
						], [
							'debit'			=> $debit
						]);
					}
				}

				$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
					'tgl'			=> $tanggal_aft,
					'id_aktivitas'	=> 3
				]);

				if (isset($check_jurnal_umum_aft))
				{
					$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
						'debit'	=> $check_jurnal_umum_aft->debit + $jlh_dibayar_aft
					]);
				}
				else
				{
					$this->data['entri'] = [
						'tgl'			=> $tanggal_aft,
						'ket'			=> 'Angsuran',
						'debit'			=> $jlh_dibayar_aft,
						'kredit'		=> 0,
						'id_aktivitas'	=> 3
					];
					$this->jurnal_umum_m->insert($this->data['entri']);
				}

				$check_buku_besar_bfr = $this->buku_besar_m->get_row([
					'tgl'			=> $tanggal_bfr,
					'id_aktivitas'	=> 3
				]);
				if ($tanggal_aft > $tanggal_bfr && $jlh_dibayar_aft > $jlh_dibayar_bfr)
				{
					// case 5
					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $jlh_dibayar_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_debit > 0)
					{
						$data['saldo_debit'] -= $jlh_dibayar_bfr;
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += $jlh_dibayar_bfr;

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 3
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 3,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_dibayar_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_dibayar_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;
							}	
						}
						else
							$check_buku_besar->saldo_debit += ($jlh_dibayar_aft - $jlh_dibayar_bfr);
						
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $jlh_dibayar_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;

						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar <' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_dibayar_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_dibayar_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $jlh_dibayar_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $jlh_dibayar_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 3
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $jlh_dibayar_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 3
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 3, 'id_buku_besar >' => $id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				
					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
				else if ($tanggal_aft > $tanggal_bfr && $jlh_dibayar_aft < $jlh_dibayar_bfr)
				{
					// case 6
					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $jlh_dibayar_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_debit > 0)
					{
						$data['saldo_debit'] -= $jlh_dibayar_bfr;
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += $jlh_dibayar_bfr;

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 3
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 3,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_dibayar_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_dibayar_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_dibayar_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);

						if ($check_buku_besar->saldo_debit > 0)
						{
							$check_buku_besar->saldo_debit += ($jlh_dibayar_aft - $jlh_dibayar_bfr);
							if ($check_buku_besar->saldo_debit < 0)
							{
								$check_buku_besar->saldo_kredit = $check_buku_besar->saldo_debit * (-1);
								$check_buku_besar->saldo_debit = 0;	
							}
						}
						else
							$check_buku_besar->saldo_kredit -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);				
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $jlh_dibayar_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_dibayar_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $jlh_dibayar_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $jlh_dibayar_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 3
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $jlh_dibayar_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 3
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tanggal_aft, 'id_aktivitas !=' => 3, 'id_buku_besar >' => $id_buku_besar]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_dibayar_bfr - $jlh_dibayar_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_dibayar_bfr - $jlh_dibayar_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				
					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_dibayar_bfr - $jlh_dibayar_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_dibayar_bfr - $jlh_dibayar_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
				else if ($tanggal_aft < $tanggal_bfr && $jlh_dibayar_aft > $jlh_dibayar_bfr)
				{
					// case 7
					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $jlh_dibayar_bfr;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($check_buku_besar_bfr->saldo_kredit > 0)
					{
						$data['saldo_kredit'] -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 3
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 3,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_dibayar_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_dibayar_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_dibayar_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_dibayar_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_dibayar_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_dibayar_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);

						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= $jlh_dibayar_aft;
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;	
							}
						}
						else
							$check_buku_besar->saldo_debit += $jlh_dibayar_aft;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $jlh_dibayar_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_dibayar_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_dibayar_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $jlh_dibayar_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $jlh_dibayar_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 3
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $jlh_dibayar_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 3
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
				else
				{
					// case 8
					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $jlh_dibayar_bfr;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					if ($check_buku_besar_bfr->saldo_debit > 0)
					{
						$data['saldo_debit'] -= ($jlh_dibayar_bfr - $jlh_dibayar_aft);
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
						$data['saldo_kredit'] += ($jlh_dibayar_bfr - $jlh_dibayar_aft);

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 3
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 3,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_kredit']	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_dibayar_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_dibayar_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_kredit']	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_dibayar_aft;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_dibayar_aft;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$last_row = $this->buku_besar_m->get_last_row();
						if (!isset($last_row))
							$id_buku_besar = 1;
						else
							$id_buku_besar = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_dibayar_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_dibayar_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);

						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= $jlh_dibayar_aft;
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;
							}	
						}
						else
							$check_buku_besar->saldo_debit += $jlh_dibayar_aft;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $jlh_dibayar_aft,
							'saldo_debit' 	=> $check_buku_besar->saldo_debit,
							'saldo_kredit' 	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						if (!isset($last_row))
							$id_buku_besar = 1;
						else
							$id_buku_besar = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit -= $jlh_dibayar_aft;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit += $jlh_dibayar_aft;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $jlh_dibayar_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $jlh_dibayar_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 3
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $jlh_dibayar_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 3
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_dibayar_bfr - $jlh_dibayar_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_dibayar_bfr - $jlh_dibayar_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_kredit'	=> $row->saldo_kredit,
							'saldo_debit'	=> $row->saldo_debit
						]);
					}
				}
			}
			else
			{
				$check_buku_besar_bfr = $this->buku_besar_m->get_row([
					'tgl'			=> $tanggal_bfr,
					'id_aktivitas'	=> 3
				]);
				if ($tanggal_aft > $tanggal_bfr)
				{
					// case 1
					if (isset($check_jurnal_umum_bfr))
					{
						$debit = $check_jurnal_umum_bfr->debit - $jlh_dibayar_bfr;
						if ($debit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 3
							], [
								'debit'			=> $debit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 3
					]);

					if (isset($check_simpanan_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'debit'	=> $check_jurnal_umum_aft->debit + $jlh_dibayar_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Angsuran',
							'debit'			=> $jlh_dibayar_aft,
							'kredit'		=> 0,
							'id_aktivitas'	=> 3
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}

					if (isset($check_buku_besar_bfr))
					{
						$data = [];
						$data['debit'] 			= $check_buku_besar_bfr->debit - $jlh_dibayar_bfr;
						$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
						$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
						if ($check_buku_besar_bfr->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_dibayar_bfr;

						if ($data['debit'] <= 0)
							$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
						else
						{
							$this->buku_besar_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 3
							], $data);
						}
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 3,
						'id_buku_besar >'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					if (isset($check_buku_besar_bfr))
					{
						foreach ($check_buku_besar_bfr as $row)
						{
							$data = [];
							$data['saldo_debit']	= $row->saldo_debit;
							$data['saldo_kredit']	= $row->saldo_kredit;
							if ($row->saldo_debit > 0)
							{
								$data['saldo_debit'] -= $jlh_dibayar_bfr;
								if ($data['saldo_debit'] < 0)
								{
									$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
									$data['saldo_debit'] = 0;
								}
							}
							else
								$data['saldo_kredit'] += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr, 'tgl <' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_debit']	= $row->saldo_debit;
						$data['saldo_kredit']	= $row->saldo_kredit;
						if ($row->saldo_debit > 0)
						{
							$data['saldo_debit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_debit'] < 0)
							{
								$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
								$data['saldo_debit'] = 0;
							}
						}
						else
							$data['saldo_kredit'] += $jlh_dibayar_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_dibayar_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <' => $tanggal_aft], 'tgl');

						if ($last_row->saldo_kredit > 0)
						{
							$last_row->saldo_kredit -= $jlh_dibayar_aft;
							if ($last_row->saldo_kredit < 0)
							{
								$last_row->saldo_debit = $last_row->saldo_kredit * (-1);
								$last_row->saldo_kredit = 0;	
							}
						}
						else
							$last_row->saldo_debit += $jlh_dibayar_aft;

						$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);

						if ($last_row->saldo_kredit > 0)
						{
							$last_row->saldo_kredit -= $check_buku_besar->debit;
							if ($last_row->saldo_kredit < 0)
							{
								$last_row->saldo_debit = $last_row->saldo_kredit * (-1);
								$last_row->saldo_kredit = 0;	
							}
						}
						else
							$last_row->saldo_debit += $check_buku_besar->debit;

						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $jlh_dibayar_aft,
							'saldo_debit'	=> $last_row->saldo_debit,
							'saldo_kredit'	=> $last_row->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row();
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar <' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $jlh_dibayar_bfr;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, [
								'saldo_kredit'	=> $row->saldo_kredit,
								'saldo_debit'	=> $row->saldo_debit
							]);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $jlh_dibayar_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $jlh_dibayar_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 3
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $jlh_dibayar_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 3
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}
				}
				else if ($tanggal_aft < $tanggal_bfr)
				{
					// case 2
					if (isset($check_jurnal_umum_bfr))
					{
						$debit = $check_jurnal_umum_bfr->debit - $jlh_dibayar_bfr;
						if ($debit <= 0)
							$this->jurnal_umum_m->delete($check_jurnal_umum_bfr->id_jurnal);
						else
						{
							$this->jurnal_umum_m->update_where([
								'tgl'			=> $tanggal_bfr,
								'id_aktivitas'	=> 3
							], [
								'debit'			=> $debit
							]);
						}
					}

					$check_jurnal_umum_aft = $this->jurnal_umum_m->get_row([
						'tgl'			=> $tanggal_aft,
						'id_aktivitas'	=> 3
					]);

					if (isset($check_simpanan_aft))
					{
						$this->jurnal_umum_m->update($check_jurnal_umum_aft->id_jurnal, [
							'debit'	=> $check_jurnal_umum_aft->debit + $jlh_dibayar_aft
						]);
					}
					else
					{
						$this->data['entri'] = [
							'tgl'			=> $tanggal_aft,
							'ket'			=> 'Angsuran',
							'debit'			=> $jlh_dibayar_aft,
							'kredit'		=> 0,
							'id_aktivitas'	=> 3
						];
						$this->jurnal_umum_m->insert($this->data['entri']);
					}

					$data = [];
					$data['debit'] 			= $check_buku_besar_bfr->debit - $jlh_dibayar_bfr;

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
					{
						$this->buku_besar_m->update_where([
							'tgl'			=> $tanggal_bfr,
							'id_aktivitas'	=> 3
						], $data);
					}

					$check_buku_besar_bfr = $this->buku_besar_m->get([
						'tgl'				=> $tanggal_bfr,
						'id_aktivitas !='	=> 3,
						'id_buku_besar <'	=> $check_buku_besar_bfr->id_buku_besar
					]);
					foreach ($check_buku_besar_bfr as $row)
					{
						$data = [];
						$data['saldo_kredit'] 	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_dibayar_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar_itr = $this->buku_besar_m->get(['tgl <' => $tanggal_bfr, 'tgl >' => $tanggal_aft]);
					foreach ($check_buku_besar_itr as $row)
					{
						$data = [];
						$data['saldo_kredit'] 	= $row->saldo_kredit;
						$data['saldo_debit']	= $row->saldo_debit;
						if ($row->saldo_kredit > 0)
						{
							$data['saldo_kredit'] -= $jlh_dibayar_bfr;
							if ($data['saldo_kredit'] < 0)
							{
								$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
								$data['saldo_kredit'] = 0;
							}
						}
						else
							$data['saldo_debit'] += $jlh_dibayar_bfr;

						$this->buku_besar_m->update($row->id_buku_besar, $data);
					}

					$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal_aft, 'id_aktivitas' => 3]);
					$id_buku_besar = -1;
					if (isset($check_buku_besar))
					{
						if ($check_buku_besar->saldo_kredit > 0)
						{
							$check_buku_besar->saldo_kredit -= $jlh_dibayar_aft;
							if ($check_buku_besar->saldo_kredit < 0)
							{
								$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
								$check_buku_besar->saldo_kredit = 0;
							}
						}
						else
							$check_buku_besar->saldo_debit += $jlh_dibayar_aft;
						$this->buku_besar_m->update($check_buku_besar->id_buku_besar, [
							'debit'			=> $check_buku_besar->debit + $jlh_dibayar_aft,
							'saldo_debit'	=> $check_buku_besar->saldo_debit,
							'saldo_kredit'	=> $check_buku_besar->saldo_kredit
						]);
						$id_buku_besar = $check_buku_besar->id_buku_besar;

						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $id_buku_besar, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $jlh_dibayar_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}
					}
					else
					{
						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						$id = -1;
						if (!isset($last_row))
							$id = 1;
						else
							$id = $last_row->id_buku_besar;
						$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $id, 'tgl' => $tanggal_aft]);
						foreach ($check_buku_besar as $row)
						{
							$data = [];
							$data['saldo_kredit'] 	= $row->saldo_kredit;
							$data['saldo_debit']	= $row->saldo_debit;
							if ($row->saldo_kredit > 0)
							{
								$data['saldo_kredit'] -= $jlh_dibayar_bfr;
								if ($data['saldo_kredit'] < 0)
								{
									$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
									$data['saldo_kredit'] = 0;
								}
							}
							else
								$data['saldo_debit'] += $jlh_dibayar_bfr;

							$this->buku_besar_m->update($row->id_buku_besar, $data);
						}

						$last_row = $this->buku_besar_m->get_last_row(['tgl <=' => $tanggal_aft], 'tgl');
						if (isset($last_row))
						{
							$saldo_debit 	= 0;
							$saldo_kredit 	= 0;
							if ($last_row->saldo_kredit > 0)
							{
								$saldo_kredit = $last_row->saldo_kredit - $jlh_dibayar_aft;
								if ($saldo_kredit < 0)
								{
									$saldo_debit = $saldo_kredit * (-1);
									$saldo_kredit = 0;
								}
							}
							else
								$saldo_debit = $last_row->saldo_debit + $jlh_dibayar_aft;

							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $saldo_debit,
								'saldo_kredit'	=> $saldo_kredit,
								'id_aktivitas'	=> 3
							];
						}
						else
						{
							$this->data['entri'] = [
								'tgl'			=> $tanggal_aft,
								'ket'			=> 'Angsuran',
								'ref'			=> '106',
								'debit'			=> $jlh_dibayar_aft,
								'kredit'		=> 0,
								'saldo_debit'	=> $jlh_dibayar_aft,
								'saldo_kredit'	=> 0,
								'id_aktivitas'	=> 3
							];
						}
						$this->buku_besar_m->insert($this->data['entri']);
						$id_buku_besar = $this->db->insert_id();
					}
				}
				else if ($jlh_dibayar_aft > $jlh_dibayar_bfr)
				{
					// case 3
					$check_jurnal_umum = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 3]);
					$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
						'debit'	=> $check_jurnal_umum->debit + ($jlh_dibayar_aft - $jlh_dibayar_bfr)
					]);

					$data = [];
					$data['debit']			= $check_buku_besar_bfr->debit + ($jlh_dibayar_aft - $jlh_dibayar_bfr);
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($data['saldo_kredit'] > 0)
					{
						$data['saldo_kredit'] -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

					$this->buku_besar_m->update($check_buku_besar_bfr->id_buku_besar, $data);

					$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar, 'tgl' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_kredit > 0)
						{
							$row->saldo_kredit -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
							if ($row->saldo_kredit < 0)
							{
								$row->saldo_debit = $row->saldo_kredit * (-1);
								$row->saldo_kredit = 0;
							}
						}
						else
							$row->saldo_debit += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}
				}
				else
				{
					// case 4
					$check_jurnal_umum = $this->jurnal_umum_m->get_row(['tgl' => $tanggal_bfr, 'id_aktivitas' => 3]);
					$debit = $check_jurnal_umum->debit + ($jlh_dibayar_aft - $jlh_dibayar_bfr);
					if ($debit <= 0)
						$this->jurnal_umum_m->delete($check_jurnal_umum->id_jurnal);
					else
						$this->jurnal_umum_m->update($check_jurnal_umum->id_jurnal, [
							'debit'	=> $check_jurnal_umum->debit + ($jlh_dibayar_aft - $jlh_dibayar_bfr)
						]);

					$data = [];
					$data['debit']			= $check_buku_besar_bfr->debit + ($jlh_dibayar_aft - $jlh_dibayar_bfr);
					$data['saldo_debit']	= $check_buku_besar_bfr->saldo_debit;
					$data['saldo_kredit']	= $check_buku_besar_bfr->saldo_kredit;
					if ($data['saldo_kredit'] > 0)
					{
						$data['saldo_kredit'] -= ($jlh_dibayar_aft - $jlh_dibayar_bfr);
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] += ($jlh_dibayar_aft - $jlh_dibayar_bfr);

					if ($data['debit'] <= 0)
						$this->buku_besar_m->delete($check_buku_besar_bfr->id_buku_besar);
					else
						$this->buku_besar_m->update($check_buku_besar_bfr->id_buku_besar, $data);

					$check_buku_besar = $this->buku_besar_m->get(['id_aktivitas !=' => 3, 'id_buku_besar >' => $check_buku_besar_bfr->id_buku_besar, 'tgl' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_dibayar_bfr - $jlh_dibayar_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_dibayar_bfr - $jlh_dibayar_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}

					$check_buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal_bfr]);
					foreach ($check_buku_besar as $row)
					{
						if ($row->saldo_debit > 0)
						{
							$row->saldo_debit -= ($jlh_dibayar_bfr - $jlh_dibayar_aft);
							if ($row->saldo_debit < 0)
							{
								$row->saldo_kredit = $row->saldo_debit * (-1);
								$row->saldo_debit = 0;
							}
						}
						else
							$row->saldo_kredit += ($jlh_dibayar_bfr - $jlh_dibayar_aft);

						$this->buku_besar_m->update($row->id_buku_besar, [
							'saldo_debit'	=> $row->saldo_debit,
							'saldo_kredit'	=> $row->saldo_kredit
						]);
					}
				}

			}


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
			$this->data['angsuran']->dropdown = form_dropdown('edit_id_pinjaman', $temp, $this->data['angsuran']->id_pinjaman, ['class' => 'form-control']);
			echo json_encode($this->data['angsuran']);
			exit;
		}

		if ($this->POST('delete') && $this->POST('id_angsuran'))
		{
			$angsuran_bfr = $this->angsuran_m->get_row(['id_angsuran' => $this->POST('id_angsuran')]);
			$this->angsuran_m->delete($this->POST('id_angsuran'));
			$tanggal = $angsuran_bfr->tgl_angsuran;

			$this->load->model('jurnal_umum_m');
			$check_jurnal_umum = $this->jurnal_umum_m->get(['tgl' => $tanggal]);
			foreach ($check_jurnal_umum as $row)
			{
				$row->debit -= $angsuran_bfr->jlh_dibayar;

				if ($row->debit <= 0)
					$this->jurnal_umum_m->delete_by(['id_jurnal' => $row->id_jurnal]);
				else
					$this->jurnal_umum_m->update($row->id_jurnal, [
						'debit'	=> $row->debit
					]);
			}
			
			$debit = $angsuran_bfr->jlh_dibayar;
			
			$this->load->model('buku_besar_m');
			$buku_besar = $this->buku_besar_m->get_row(['tgl' => $tanggal, 'id_aktivitas' => 3]);
			$buku_besar->debit -= $angsuran_bfr->jlh_dibayar;
			if ($buku_besar->debit <= 0)
				$this->buku_besar_m->delete($buku_besar->id_buku_besar);
			else
			{
				if ($buku_besar->saldo_kredit > 0)
					$buku_besar->saldo_kredit += $angsuran_bfr->jlh_dibayar;
				else
				{
					$buku_besar->saldo_debit -= $angsuran_bfr->jlh_dibayar;
					if ($buku_besar->saldo_debit < 0)
					{
						$buku_besar->saldo_kredit = $buku_besar->saldo_debit * (-1);
						$buku_besar->saldo_debit = 0;
					}
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'debit'			=> $buku_besar->debit,
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);
			}

			$buku_besar_s = $this->buku_besar_m->get(['tgl' => $tanggal, 'id_aktivitas !=' => 3]);
			
			foreach ($buku_besar_s as $buku_besar)
			{
				if ($buku_besar->saldo_debit > 0)
				{
					$buku_besar->saldo_debit -= $angsuran_bfr->jlh_dibayar;
					if ($buku_besar->saldo_debit < 0)
					{
						$buku_besar->saldo_kredit = $buku_besar->saldo_debit * (-1);
						$buku_besar->saldo_debit = 0;
					}
				}
				else
				{
					$buku_besar->saldo_kredit += $angsuran_bfr->jlh_dibayar;
				}

				$this->buku_besar_m->update($buku_besar->id_buku_besar, [
					'saldo_debit'	=> $buku_besar->saldo_debit,
					'saldo_kredit'	=> $buku_besar->saldo_kredit
				]);
			}

			$buku_besar = $this->buku_besar_m->get(['tgl >' => $tanggal]);
			foreach ($buku_besar as $row)
			{
				if ($row->saldo_debit > 0)
				{
					$row->saldo_debit -= $angsuran_bfr->jlh_dibayar;
					if ($row->saldo_debit < 0)
					{
						$row->saldo_kredit = $row->saldo_debit * (-1);
						$row->saldo_debit = 0;
					}
				}
				else
				{
					$row->saldo_kredit += $angsuran_bfr->jlh_dibayar;
				}

				$this->buku_besar_m->update($row->id_buku_besar, [
					'saldo_debit'	=> $row->saldo_debit,
					'saldo_kredit'	=> $row->saldo_kredit
				]);
			}
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