<?php
/**
 *
 */
class Operator extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->data['id_role'] = $this->session->userdata('id_role');
    if (!isset($this->data['id_role']))
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('id_role');
        redirect('login');
        exit;
    }
    if ($this->data['id_role'] != 2)
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('id_role');
        redirect('login');
        exit;
    }
  }

  public function index()
  {
    $data = [
			'title'		=> 'Admin',
			'content'	=> 'operator_unit/dashboard'
		];
    $this->template($data,'operator');
  }

  public function permintaan()
  {
    $this->load->model('unit_m');
    $this->load->model('permintaan_bahan_baku_m');
    $this->load->model('operator_unit_m');
    $op = $this->operator_unit_m->get_row(['username'=>$this->session->userdata('username')]);
    $permintaan_cond =[
      'approved' => 0,
      'id_unit' => $op->id_unit
    ];
    $this->data['permintaan'] = $this->permintaan_bahan_baku_m->get($permintaan_cond);

    if ($this->POST('simpan')) {
      $data_permintaan = [
        'nama' => $this->POST('nama'),
        'keterangan' => $this->POST('keterangan'),
        'tanggal_permintaan' => date("Y-m-d"),
        'id_unit' => $op->id_unit,
        'batas_waktu' => date("Y-m-d",strtotime($this->POST('batas_waktu')))
      ];
      $this->permintaan_bahan_baku_m->insert($data_permintaan);
      redirect('operator/permintaan');
    }

    if ($this->POST('edit')) {
      $op = $this->operator_unit_m->get_row(['username'=>$this->session->userdata('username')]);
      $data_edit = [
        'nama' => $this->POST('nama'),
        'keterangan' => $this->POST('keterangan'),
        'batas_waktu' => date("Y-m-d",strtotime($this->POST('batas_waktu')))
      ];
      $this->permintaan_bahan_baku_m->update($data_edit,$this->POST['id_permintaan']);
    }

    if ($this->POST('delete') && $this->POST('id_permintaan'))
    {
        $this->permintaan_bahan_baku_m->delete($this->POST('id_permintaan'));
        exit;
    }

    if ($this->POST('get') && $this->POST('id_permintaan'))
    {
        $data_permintaan = $this->permintaan_bahan_baku_m->get_row(['id_permintaan' => $this->POST('id_permintaan')]);
        echo json_encode($data_permintaan);
        exit;
    }

    $this->data['title'] = 'Daftar Permintaan';
    $this->data['content'] = 'operator_unit/permintaan';
    $this->template($this->data,'operator');
  }

  public function permintaan_disetujui()
  {
    $this->load->model('unit_m');
    $this->load->model('operator_unit_m');
    $this->load->model('permintaan_bahan_baku_m');
    $op = $this->operator_unit_m->get_row(['username'=>$this->session->userdata('username')]);

    $permintaan_cond =[
      'approved' => 1,
      'id_unit' => $op->id_unit
    ];
    $this->data['permintaan'] = $this->permintaan_bahan_baku_m->get($permintaan_cond);

    $this->data['title'] = 'Permintaan Disetujui';
    $this->data['content'] = 'operator_unit/verified';
    $this->template($this->data,'operator');
  }

  public function detail_permintaan()
  {
    $this->load->model('permintaan_bahan_baku_m');
    $this->load->model('detail_permintaan_m');
    $this->load->model('bahan_baku_m');
    $this->data['bahan'] = $this->bahan_baku_m->get();
    if ($this->POST('simpan')) {
      $data_detail =[
        'id_permintaan' => $this->POST('id_permintaan'),
        'id_bahan_baku' => $this->POST('id_bahan_baku'),
        'jumlah_permintaan' => $this->POST('jumlah_permintaan'),
        'keterangan' => $this->POST('keterangan')
      ];
      $this->detail_permintaan_m->insert($data_detail);
    }
    $this->data['detail'] = $this->detail_permintaan_m->get(['id_permintaan'=> $this->uri->segment(3)]);

    $this->data['title'] = 'Detail Permintaan';
    $this->data['content'] = 'operator_unit/detail_permintaan';
    $this->template($this->data,'operator');
  }
}

 ?>
