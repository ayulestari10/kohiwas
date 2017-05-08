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
    $this->load->model('operator_unit_m');
    $this->load->model('user_m');
    $this->data['user'] = $this->user_m->get_row(['username' => $this->session->userdata('username')]);
  }

  public function index()
  {
    $this->permintaan();
  }

  public function permintaan()
  {
    $this->load->model('unit_m');
    $this->load->model('permintaan_bahan_baku_m');
    $this->load->model('operator_unit_m');
    $this->load->model('detail_permintaan_m');
    $op = $this->operator_unit_m->get_row(['username'=>$this->session->userdata('username')]);
    $permintaan_cond =[
      'approved' => 0,
      'id_unit' => $op->id_unit
    ];
    $permintaan_approved =[
      'approved' => 1,
      'id_unit' => $op->id_unit
    ];
    $this->data['permintaan'] = $this->permintaan_bahan_baku_m->get($permintaan_cond);
    $this->data['permintaan_approved'] = $this->permintaan_bahan_baku_m->get($permintaan_approved);

    if ($this->POST('simpan')) {
      $data_permintaan = [
        'nama' => $this->POST('nama'),
        'keterangan' => $this->POST('keterangan'),
        'tanggal_permintaan' => date("Y-m-d"),
        'id_unit' => $op->id_unit,
        'batas_waktu' => date("Y-m-d",strtotime($this->POST('batas_waktu')))
      ];
      $this->permintaan_bahan_baku_m->insert($data_permintaan);
      $this->flashmsg('Permintaan berhasil disimpan untuk dikonfirmasi');
      redirect('operator/permintaan');
    }

    if ($this->POST('edit')) {
      $op = $this->operator_unit_m->get_row(['username'=>$this->session->userdata('username')]);
      $data_edit = [
        'nama' => $this->POST('nama'),
        'keterangan' => $this->POST('keterangan'),
        'batas_waktu' => date("Y-m-d",strtotime($this->POST('batas_waktu')))
      ];

      $this->permintaan_bahan_baku_m->update($this->POST('id_permintaan'),$data_edit);
      $this->flashmsg('Permintaan berhasil diperbarui');
      redirect('operator/permintaan');
    }

    if ($this->POST('delete') && $this->POST('id_permintaan'))
    {
        $this->permintaan_bahan_baku_m->delete($this->POST('id_permintaan'));
        $this->detail_permintaan_m->delete_by(['id_permintaan'=>$this->POST('id_permintaan')],$this->POST('id_permintaan'));
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

  public function detail_permintaan()
  {
    $this->load->model('permintaan_bahan_baku_m');
    $this->load->model('detail_permintaan_m');
    $this->load->model('bahan_baku_m');

    if ($this->POST('simpan')) {
      $data_detail =[
        'id_permintaan' => $this->POST('id_permintaan'),
        'id_bahan_baku' => $this->POST('id_bahan_baku'),
        'jumlah_permintaan' => $this->POST('jumlah_permintaan'),
        'keterangan' => $this->POST('keterangan')
      ];
      $this->flashmsg('Bahan baku ditambahkan ke permintaan');
      $this->detail_permintaan_m->insert($data_detail);
    }

    if ($this->POST('get') && $this->POST('id_detail_permintaan'))
    {
        $data_permintaan = $this->detail_permintaan_m->get_row(['id_detail_permintaan' => $this->POST('id_detail_permintaan')]);
        echo json_encode($data_permintaan);
        exit;
    }

    if ($this->POST('edit')) {
      $jumlah_data = [
        'jumlah_permintaan'=>$this->POST('jumlah'),
      ];
      $id = $this->POST('id_detail_permintaan');
      $this->detail_permintaan_m->update($id,$jumlah_data);
    }

      if ($this->POST('delete') && $this->POST('id_detail_permintaan'))
      {
          $this->detail_permintaan_m->delete($this->POST('id_detail_permintaan'));
          exit;
      }

    $this->data['title'] = 'Detail Permintaan';
    $this->data['content'] = 'operator_unit/detail_permintaan';
    $this->data['bahan'] = $this->bahan_baku_m->get();
    $this->data['detail'] = $this->detail_permintaan_m->get(['id_permintaan'=> $this->uri->segment(3)]);
    $this->template($this->data,'operator');
  }

  public function addpermintaan()
  {
    $this->load->model('bahan_baku_m');
    $this->load->model('permintaan_bahan_baku_m');

    $this->data['bahan_baku'] = $this->bahan_baku_m->get();
    $this->data['title'] = 'add bahan baku';
    $this->data['content'] = 'operator_unit/add_permintaan';
    $this->template($this->data, 'operator');
  }
}

 ?>
