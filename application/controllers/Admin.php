<?php
/**
 *
 */
class Admin extends MY_Controller
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
  }

  public function index()
  {
    $data = [
			'title'		=> 'Admin',
			'content'	=> 'admin/dashboard'
		];
    $this->template($data);
  }

  public function daftar_operator()
  {
    $this->load->model('operator_unit_m');
    $this->load->model('unit_m');
    $this->load->model('user_m');

    $this->data['list_unit'] = $this->unit_m->get();
    $this->data['operator'] = $this->operator_unit_m->get();

    if ($this->POST('add')) {
      $data_user = [
        'username' => $this->POST('username'),
        'password' => $this->POST('password'),
        'id_role' => 2
      ];
      $this->user_m->insert($data_user);
      $user_detail =[
        'username' => $this->POST('username'),
        'nama' => $this->POST('nama'),
        'id_unit' => $this->POST('unit'),
        'no_pegawai' => $this->POST('nopeg')
      ];
      $this->operator_unit_m->insert($user_detail);
      redirect('admin/daftar_operator');
    }

    $this->data['title'] = 'Daftar Operator Unit';
    $this->data['content'] = 'admin/list_user';
    $this->template($this->data);
  }
}

 ?>
