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
        $this->data = [
            'title'		=> 'Admin',
            'content'	=> 'admin/dashboard'
        ];
        $this->template($this->data);
    }

    public function supplier()
    {
        $this->load->model('supplier_m');
        if ($this->POST('simpan'))
        {
            $input_supplier = [
                'nama_suplier'  => $this->POST('nama_suplier'),
                'alamat'        => $this->POST('alamat')
            ];
            $this->supplier_m->insert($input_supplier);
            $this->flashmsg('Input data supplier berhasil');
            redirect('admin/supplier');
            exit;
        }

        if ($this->POST('delete') && $this->POST('id_suplier'))
        {
            $this->supplier_m->delete($this->POST('id_suplier'));
            exit;
        }

        if ($this->POST('get') && $this->POST('id_suplier'))
        {
            $data_supplier = $this->supplier_m->get_row(['id_suplier' => $this->POST('id_suplier')]);
            echo json_encode($data_supplier);
            exit;
        }

        if ($this->POST('edit'))
        {
            $edit_supplier = [
                'nama_suplier'  => $this->POST('edit_nama'),
                'alamat'        => $this->POST('edit_alamat')
            ];
            $this->supplier_m->update($this->POST('edit_id_suplier'), $edit_supplier);
            $this->flashmsg('Data supplier berhasil di-edit');
            redirect('admin/supplier');
            exit;
        }

        $this->data = [
            'title'     => 'Supplier',
            'content'   => 'admin/data_supplier',
            'supplier'  => $this->supplier_m->get_by_order('id_suplier', 'DESC')
        ];
        $this->template($this->data);
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
            exit;
        }

        $this->data['title'] = 'Daftar Operator Unit';
        $this->data['content'] = 'admin/list_user';
        $this->template($this->data);
    }
}

?>
