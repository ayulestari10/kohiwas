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
			$temp = $this->buku_besar_m->get_last_row(['tgl <' => $this->data['simpanan']['tgl_simpanan']]);
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

			$temp = $this->buku_besar_m->get_last_row(['tgl <=' => $this->data['simpanan']['tgl_simpanan']]);
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
			$simpanan_wajib_bfr = $simpanan_bfr->simpanan_wajib;
			$simpanan_sukarela_bfr = $simpanan_bfr->simpanan_sukarela;
			$tgl_simpanan_bfr = $simpanan_bfr->tgl_simpanan;
			$buku_besar_bfr = $this->buku_besar_m->get(['tgl' => $tgl_simpanan_bfr]);

			$this->data['simpanan'] = [
				'id_anggota'		=> $this->POST('edit_id_anggota'),
				'tgl_simpanan'		=> $this->POST('edit_tgl_simpanan'),
				'simpanan_wajib'	=> $this->POST('edit_simpanan_wajib'),
				'simpanan_sukarela'	=> $this->POST('edit_simpanan_sukarela')
			];
			$this->simpanan_m->update($this->POST('edit_id_simpanan'), $this->data['simpanan']);

			$simpanan_aft = $this->simpanan_m->get_row(['id_simpanan' => $this->POST('edit_id_simpanan')]);
			$simpanan_wajib_aft = $simpanan_aft->simpanan_wajib;
			$simpanan_sukarela_aft = $simpanan_aft->simpanan_sukarela;
			$tgl_simpanan_aft = $simpanan_aft->tgl_simpanan;

			$range_simpanan_wajib = $simpanan_wajib_bfr - $simpanan_wajib_aft;
			$range_simpanan_sukarela = $simpanan_sukarela_bfr - $simpanan_sukarela_aft;
			$range_simpanan = $range_simpanan_wajib + $range_simpanan_sukarela;

			// yg lama di-update dulu, jika habis maka hapus
			$jurnal_umum_bfr = $this->jurnal_umum_m->get(['tgl' => $tgl_simpanan_bfr]);
			foreach ($jurnal_umum_bfr as $row)
			{
				if ($row->id_aktivitas == 1)
				{
					if ($range_simpanan_wajib > 0) // berkurang
					{
						$row->debit += $this->POST('edit_simpanan_wajib');
						$row->kredit += $this->POST('edit_simpanan_wajib');
					}
					else
					{
						$row->debit -= $this->POST('edit_simpanan_wajib');
						$row->kredit -= $this->POST('edit_simpanan_wajib');	
					}

					if ($row->debit <= 0 || $row->kredit <= 0)
						$this->jurnal_umum_m->delete($row->id_jurnal);
					else
					{
						$this->jurnal_umum_m->update($row->id_jurnal, [
							'debit'	=> $row->debit,
							'kredit'=> $row->kredit
						]);
					}
				}
				else
				{
					if ($range_simpanan_wajib > 0) // berkurang
					{
						$row->debit += $this->POST('edit_simpanan_sukarela');
						$row->kredit += $this->POST('edit_simpanan_sukarela');
					}
					else
					{
						$row->debit -= $this->POST('edit_simpanan_sukarela');
						$row->kredit -= $this->POST('edit_simpanan_sukarela');	
					}

					if ($row->debit <= 0 || $row->kredit <= 0)
						$this->jurnal_umum_m->delete($row->id_jurnal);
					else
					{
						$this->jurnal_umum_m->update($row->id_jurnal, [
							'debit'	=> $row->debit,
							'kredit'=> $row->kredit
						]);
					}
				}
			}

			$check_jurnal_umum = $this->jurnal_umum_m->get(['tgl' => $this->POST('edit_tgl_simpanan')]);
			if (count($check_jurnal_umum) > 0)
			{
				// update yg ada
				foreach ($check_jurnal_umum as $row)
				{
					if ($row->id_aktivitas == 1)
					{
						$row->debit += $this->POST('edit_simpanan_wajib');
						$row->kredit += $this->POST('edit_simpanan_wajib');
						$this->jurnal_umum_m->update($row->id_jurnal, [
							'debit'	=> $row->debit,
							'kredit'=> $row->kredit
						]);
					}
					else
					{
						$row->debit += $this->POST('edit_simpanan_sukarela');
						$row->kredit += $this->POST('edit_simpanan_sukarela');
						$this->jurnal_umum_m->update($row->id_jurnal, [
							'debit'	=> $row->debit,
							'kredit'=> $row->kredit
						]);	
					}
				}
			}
			else
			{
				// create yg baru
				$wajib = [
					'ket'			=> 'Simpanan Wajib',
					'tgl'			=> $this->POST('edit_tgl_simpanan'),
					'debit'			=> $this->POST('edit_simpanan_wajib'),
					'kredit'		=> $this->POST('edit_simpanan_wajib'),
					'id_aktivitas'	=> 1
				];
				$this->jurnal_umum_m->insert($wajib);

				$sukarela = [
					'ket'			=> 'Simpanan Sukarela',
					'tgl'			=> $this->POST('edit_tgl_simpanan'),
					'debit'			=> $this->POST('edit_simpanan_sukarela'),
					'kredit'		=> $this->POST('edit_simpanan_sukarela'),
					'id_aktivitas'	=> 4
				];
				$this->jurnal_umum_m->insert($sukarela);
			}

			if ($tgl_simpanan_bfr != $tgl_simpanan_aft)
			{
				$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tgl_simpanan_bfr, 'id_aktivitas' => 1]);
			}

			$interval = [];
			if ($tgl_simpanan_bfr < $tgl_simpanan_aft)
				$interval = ['tgl >' => $tgl_simpanan_bfr, 'tgl <' => $tgl_simpanan_aft];
			else
				$interval = ['tgl <' => $tgl_simpanan_bfr, 'tgl >' => $tgl_simpanan_aft];

			$buku_besar = $this->buku_besar_m->get($interval);

			// update data pada range tanggal bfr dan aft
			foreach ($buku_besar as $row)
			{
				if ($range_simpanan > 0)
				{
					$data['saldo_debit'] = $row->saldo_debit;
					$data['saldo_kredit'] = $row->saldo_kredit;
					if ($data['saldo_debit'] > 0)
					{
						$data['saldo_debit'] -= $range_simpanan;
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
					else
					{
						$data['saldo_kredit'] += $range_simpanan;
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
				}
				else
				{
					$data['saldo_debit'] = $row->saldo_debit;
					$data['saldo_kredit'] = $row->saldo_kredit;
					if ($data['saldo_kredit'] > 0)
					{
						$data['saldo_kredit'] -= $range_simpanan;
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
					{
						$data['saldo_debit'] += $range_simpanan;
						if ($data['saldo_debit'] < 0)
						{
							$data['saldo_kredit'] = $data['saldo_debit'] * (-1);
							$data['saldo_debit'] = 0;
						}
					}
				}

				$this->buku_besar_m->update($row->id_buku_besar, $data);
			}

			$check_buku_besar = $this->buku_besar_m->get(['tgl' => $tgl_simpanan_aft, 'id_aktivitas !=' => 2, 'id_aktivitas !=' => 3]);
			if (isset($check_buku_besar))
			{
				foreach ($check_buku_besar as $row)
				{
					if ($row->aktivitas == 1)
					{
						if ($range_simpanan_wajib > 0)
						{
							if ($row->debit > 0)
							{
								$row->debit -= $range_simpanan_wajib;
								if ($row->debit < 0)
								{
									$row->kredit = $row->debit * (-1);
									$row->debit = 0;
								}
							}
							else
								$row->kredit += $range_simpanan_wajib;

							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $range_simpanan_wajib;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $range_simpanan_wajib;
						}
						else
						{
							if ($row->kredit > 0)
							{
								$row->kredit += $range_simpanan_wajib;
								if ($row->kredit < 0)
								{
									$row->debit = $row->kredit * (-1);
									$row->kredit = 0;
								}
							}
							else
								$row->debit -= $range_simpanan_wajib;
						
							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit += $range_simpanan_wajib;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit -= $range_simpanan_wajib;
						}
					}
					else
					{
						if ($range_simpanan_sukarela > 0)
						{
							if ($row->debit > 0)
							{
								$row->debit -= $range_simpanan_sukarela + $range_simpanan_wajib;
								if ($row->debit < 0)
								{
									$row->kredit = $row->debit * (-1);
									$row->debit = 0;
								}
							}
							else
								$row->kredit += $range_simpanan_sukarela + $range_simpanan_wajib;

							if ($row->saldo_debit > 0)
							{
								$row->saldo_debit -= $range_simpanan_sukarela + $range_simpanan_wajib;
								if ($row->saldo_debit < 0)
								{
									$row->saldo_kredit = $row->saldo_debit * (-1);
									$row->saldo_debit = 0;
								}
							}
							else
								$row->saldo_kredit += $range_simpanan_sukarela + $range_simpanan_wajib;
						}
						else
						{
							if ($row->kredit > 0)
							{
								$row->kredit += $range_simpanan_sukarela + $range_simpanan_wajib;
								if ($row->kredit < 0)
								{
									$row->debit = $row->kredit * (-1);
									$row->kredit = 0;
								}
							}
							else
								$row->debit -= $range_simpanan_sukarela + $range_simpanan_wajib;

							if ($row->saldo_kredit > 0)
							{
								$row->saldo_kredit += $range_simpanan_sukarela + $range_simpanan_wajib;
								if ($row->saldo_kredit < 0)
								{
									$row->saldo_debit = $row->saldo_kredit * (-1);
									$row->saldo_kredit = 0;
								}
							}
							else
								$row->saldo_debit -= $range_simpanan_sukarela + $range_simpanan_wajib;
						}
					}

					$this->buku_besar_m->update($row->id_buku_besar, [
						'debit'			=> $row->debit,
						'kredit'		=> $row->kredit,
						'saldo_debit'	=> $row->saldo_debit,
						'saldo_kredit'	=> $row->saldo_kredit
					]);
				}
			}
			else
			{

				// ============================================================================

				$temp = $this->buku_besar_m->get_last_row(['tgl <' => $tgl_simpanan_aft]);
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
					$this->data['saldo_kredit'] -= $this->POST('edit_simpanan_wajib');
					if ($this->data['saldo_kredit'] < 0)
					{
						$this->data['saldo_debit'] 	= $this->data['saldo_kredit'] * (-1);
						$this->data['saldo_kredit'] = 0;
					}
				}
				else if (isset($this->data['saldo_debit']) && $this->data['saldo_debit'] >= 0)
				{
					$this->data['saldo_debit'] += $this->POST('edit_simpanan_wajib');
				}

				$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $this->data['simpanan']['tgl_simpanan'], 'id_aktivitas' => 1]);
				if (isset($check_buku_besar))
				{
					if ($check_buku_besar->saldo_kredit > 0)
					{
						$check_buku_besar->saldo_kredit -= $this->POST('edit_simpanan_wajib');
						if ($check_buku_besar->saldo_kredit < 0)
						{
							$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
							$check_buku_besar->saldo_kredit = 0;
						}
					}
					else
					{
						$check_buku_besar->saldo_debit += $this->POST('edit_simpanan_wajib');
					}

					$data = [
						'debit'			=> $check_buku_besar->debit + $this->POST('edit_simpanan_wajib'),
						'saldo_kredit'	=> $check_buku_besar->saldo_kredit,
						'saldo_debit'	=> $check_buku_besar->saldo_debit
					];

					$this->buku_besar_m->update($check_buku_besar->id_buku_besar, $data);
				}
				else
				{
					$this->data['entri'] = [
						'tgl'			=> $tgl_simpanan_aft,
						'ket'			=> 'Simpanan Wajib',
						'ref'			=> '103',
						'debit'			=> $this->POST('edit_simpanan_wajib'),
						'kredit'		=> 0,
						'saldo_debit'	=> $this->data['saldo_debit'],
						'saldo_kredit'	=> $this->data['saldo_kredit'],
						'id_aktivitas'	=> 1 
					];

					$this->buku_besar_m->insert($this->data['entri']);
				}

				$check_buku_besar = $this->buku_besar_m->get(['tgl >=' => $tgl_simpanan_aft, 'id_aktivitas !=' => 1, 'id_aktivitas !=' => 4]);

				foreach ($check_buku_besar as $row)
				{
					$saldo_debit = $row->saldo_debit;
					$saldo_kredit = $row->saldo_kredit;
					$data = [];

					if ($saldo_kredit > 0)
					{
						$data['saldo_kredit'] = $saldo_kredit - $this->POST('edit_simpanan_wajib');
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] = $saldo_debit + $this->POST('edit_simpanan_wajib');

					$this->buku_besar_m->update($row->id_buku_besar, $data);
				}

				$temp = $this->buku_besar_m->get_last_row(['tgl <=' => $tgl_simpanan_aft, 'id_aktivitas !=' => 2, 'id_aktivitas !=' => 3]);
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
					$this->data['saldo_kredit'] -= $this->POST('edit_simpanan_sukarela');
					if ($this->data['saldo_kredit'] < 0)
					{
						$this->data['saldo_debit'] 	= $this->data['saldo_kredit'] * (-1);
						$this->data['saldo_kredit'] = 0;
					}
				}
				else if (isset($this->data['saldo_debit']) && $this->data['saldo_debit'] > 0)
				{
					$this->data['saldo_debit'] += $this->POST('edit_simpanan_sukarela');
				}

				$check_buku_besar = $this->buku_besar_m->get_row(['tgl' => $tgl_simpanan_aft, 'id_aktivitas' => 4]);
				if (isset($check_buku_besar))
				{
					if ($check_buku_besar->saldo_kredit > 0)
					{
						$check_buku_besar->saldo_kredit -= $this->POST('edit_simpanan_sukarela');
						if ($check_buku_besar->saldo_kredit < 0)
						{
							$check_buku_besar->saldo_debit = $check_buku_besar->saldo_kredit * (-1);
							$check_buku_besar->saldo_kredit = 0;
						}
					}
					else
					{
						$check_buku_besar->saldo_debit += $this->POST('edit_simpanan_sukarela');
					}

					$data = [
						'debit'			=> $check_buku_besar->debit + $this->POST('edit_simpanan_sukarela'),
						'saldo_kredit'	=> $check_buku_besar->saldo_kredit,
						'saldo_debit'	=> $check_buku_besar->saldo_debit
					];

					$this->buku_besar_m->update($check_buku_besar->id_buku_besar, $data);
				}
				else
				{
					$this->data['entri'] = [
						'tgl'			=> $tgl_simpanan_aft,
						'ket'			=> 'Simpanan Sukarela',
						'ref'			=> '104',
						'debit'			=> $this->POST('edit_simpanan_sukarela'),
						'kredit'		=> 0,
						'saldo_debit'	=> $this->data['saldo_debit'],
						'saldo_kredit'	=> $this->data['saldo_kredit'],
						'id_aktivitas'	=> 4
					];

					$this->buku_besar_m->insert($this->data['entri']);
				}

				$check_buku_besar = $this->buku_besar_m->get(['tgl >=' => $tgl_simpanan_aft, 'id_aktivitas !=' => 1, 'id_aktivitas !=' => 4]);

				foreach ($check_buku_besar as $row)
				{
					$saldo_debit = $row->saldo_debit;
					$saldo_kredit = $row->saldo_kredit;
					$data = [];

					if ($saldo_kredit > 0)
					{
						$data['saldo_kredit'] = $saldo_kredit - $this->POST('edit_simpanan_sukarela');
						if ($data['saldo_kredit'] < 0)
						{
							$data['saldo_debit'] = $data['saldo_kredit'] * (-1);
							$data['saldo_kredit'] = 0;
						}
					}
					else
						$data['saldo_debit'] = $saldo_debit + $this->POST('edit_simpanan_sukarela');

					$this->buku_besar_m->update($row->id_buku_besar, $data);
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
			$this->simpanan_m->delete($this->POST('id_simpanan'));
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

			$this->data['getPinjaman'] = $this->pinjaman_m->get_last_row();
			
			$this->data['entri1'] = [
				'id_relasi2'	=> $this->data['getPinjaman']->id_pinjaman,
				'tgl'			=> $this->data['pinjaman']['tgl_pinjaman'],
				'ket'			=> 'Pinjaman',
				'debit'			=> 0,
				'kredit'		=> $this->data['pinjaman']['jlh_pinjaman'],
			];

			$this->jurnal_umum_m->insert($this->data['entri1']);

			$cek = $this->buku_besar_m->get_last_row();

			if(!isset($cek)){
				$saldo_kredit 	= $this->POST('jlh_pinjaman');
				$saldo_debit 	= 0;
			}
			if(isset($cek)){
				$last_saldo_debit 	= $this->buku_besar_m->get_last_row()->saldo_debit;
				$saldo_debit		= $last_saldo_debit - $this->POST('jlh_pinjaman');
			}

			$this->data['entri2'] = [
				'id_relasi2'	=> $this->data['getPinjaman']->id_pinjaman,
				'tgl'			=> $this->data['pinjaman']['tgl_pinjaman'],
				'ket'			=> 'Pinjaman',
				'ref'			=> '105',
				'debit'			=> 0,
				'kredit'		=> $this->data['pinjaman']['jlh_pinjaman'],
				'saldo_debit'	=> $saldo_debit,
				'saldo_kredit'	=> 0 
			];
			
			$this->buku_besar_m->insert($this->data['entri2']);

			$this->flashmsg('Data pinjaman berhasil disimpan');
			redirect('admin/data_pinjaman');
			exit;
		}

		if ($this->POST('edit') && $this->POST('edit_id_pinjaman'))
		{

			$edit_bunga 	= 0.01 * $this->POST('edit_jlh_pinjaman');
			$edit_total 	= $edit_bunga + $this->POST('edit_jlh_pinjaman');
			$edit_angsuran 	= $edit_total/$this->POST('edit_lama_pinjaman');

			$this->data['pinjaman'] = [
				'id_anggota'		=> $this->POST('edit_id_anggota'),
				'tgl_pinjaman'		=> $this->POST('edit_tgl_pinjaman'),
				'jlh_pinjaman'		=> $this->POST('edit_jlh_pinjaman'),
				'bunga'				=> $edit_bunga,
				'ttl_pinjaman'		=> $edit_total,
				'lama_pinjaman'		=> $this->POST('edit_lama_pinjaman'),
				'angsuran'			=> $edit_angsuran
			];

			// update jurnal umum pinjaman
			$this->data['entri1'] = [
				'tgl'			=> $this->POST('edit_tgl_pinjaman'),
				'ket'			=> 'Pinjaman',
				'kredit'		=> $this->POST('edit_jlh_pinjaman')
			];

			// update buku besar pinjaman
			$cekNull = $this->buku_besar_m->get_last_row()->id_relasi2;
			echo $cekNull; exit;
			if($cekNull == NULL){
				$cek = $this->buku_besar_m->get_last_row();
				echo 'hay';
				exit;
			}
			else{
				$cek = $this->buku_besar_m->get_previous_row('id_relasi2', $this->POST('edit_id_pinjaman'), 'Pinjaman');
				echo 'hay2';
				exit;
			}
			
			if(!isset($cek)){
				$saldo_kredit 	= $this->POST('edit_jlh_pinjaman');
				$saldo_debit 	= 0;
			}
			if(isset($cek)){
				$prev_saldo_debit 	= $cek->saldo_debit;
				$saldo_debit		= $prev_saldo_debit - $this->POST('edit_jlh_pinjaman');
			}

			$this->data['entri2'] = [
				'tgl'			=> $this->POST('edit_tgl_pinjaman'),
				'ket'			=> 'Pinjaman',
				'ref'			=> '105',
				'debit'			=> 0,
				'kredit'		=> $this->POST('edit_jlh_pinjaman'),
				'saldo_debit'	=> $saldo_debit,
				'saldo_kredit'	=> 0 
			];

			$this->pinjaman_m->update($this->POST('edit_id_pinjaman'), $this->data['pinjaman']);
			$this->jurnal_umum_m->update_where(['id_relasi2' => $this->POST('edit_id_pinjaman')], $this->data['entri1']);
			$this->buku_besar_m->update_where(['id_relasi2' => $this->POST('edit_id_pinjaman')], $this->data['entri2']);

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

			$this->data['getAngsuran']	= $this->angsuran_m->get_last_row();
			
			$this->data['entri1'] = [
				'id_relasi3'	=> $this->data['getAngsuran']->id_angsuran,
				'tgl'			=> $this->data['angsuran']['tgl_angsuran'],
				'ket'			=> 'Angsuran',
				'debit'			=> $this->data['angsuran']['jlh_dibayar'],
				'kredit'		=> 0
			];

			$this->jurnal_umum_m->insert($this->data['entri1']);

			$cek = $this->buku_besar_m->get_last_row();

			if(!isset($cek)){
				$saldo_debit 	= $this->POST('jlh_dibayar');
			}
			if(isset($cek)){
				$last_saldo_debit 	= $this->buku_besar_m->get_last_row()->saldo_debit;
				$saldo_debit		= $last_saldo_debit + $this->POST('jlh_dibayar');
			}

			$this->data['entri2'] = [
				'id_relasi3'	=> $this->data['getAngsuran']->id_angsuran,
				'tgl'			=> $this->data['angsuran']['tgl_angsuran'],
				'ket'			=> 'Angsuran',
				'ref'			=> '106',
				'debit'			=> $this->data['angsuran']['jlh_dibayar'],
				'kredit'		=> 0,
				'saldo_debit'	=> $saldo_debit,
				'saldo_kredit'	=> 0 
			];
			
			$this->buku_besar_m->insert($this->data['entri2']);

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

			// update jurnal umum angsuran
			$this->data['entri1'] = [
				'tgl'			=> $this->POST('edit_tgl_angsuran'),
				'ket'			=> 'Angsuran',
				'debit'			=>  $this->POST('edit_jlh_dibayar'),
				'kredit'		=> 0
			];

			// update buku besar angsuran
			$cek = $this->buku_besar_m->get_previous_row('id_relasi3', $this->POST('edit_id_angsuran'));

			if(!isset($cek)){
				$saldo_debit 	= $this->POST('edit_jlh_dibayar');
			}
			if(isset($cek)){
				$prev_saldo_debit 	= $cek->saldo_debit;
				$saldo_debit		= $prev_saldo_debit + $this->POST('edit_jlh_dibayar');
			}

			$this->data['entri2'] = [
				'tgl'			=> $this->POST('edit_tgl_angsuran'),
				'ket'			=> 'Angsuran',
				'ref'			=> '106',
				'debit'			=> $this->POST('edit_jlh_dibayar'),
				'kredit'		=> 0,
				'saldo_debit'	=> $saldo_debit,
				'saldo_kredit'	=> 0 
			];

			$this->angsuran_m->update($this->POST('edit_id_angsuran'), $this->data['angsuran']);
			$this->jurnal_umum_m->update_where(['id_relasi3' => $this->POST('edit_id_angsuran')], $this->data['entri1']);
			$this->buku_besar_m->update_where(['id_relasi3' => $this->POST('edit_id_angsuran')], $this->data['entri2']);

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