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

        $this->load->model('user_m');
        $this->data['user'] = $this->user_m->get_row(['username' => $this->session->userdata('username')]);
    }

    public function index()
    {
        $this->data['title']    = 'Admin';
        $this->data['content']  = 'admin/dashboard'; 
        $this->template($this->data);
    }

    public function bahan_baku()
    {
        $this->load->model('bahan_baku_m');
        $this->load->model('supplier_m');

        if ($this->POST('simpan'))
        {
            $data_bahan_baku = [
                'id_suplier'    => $this->POST('id_suplier'),
                'nama_bahan'    => $this->POST('nama'),
                'jenis_bahan'   => $this->POST('jenis'),
                'satuan'        => $this->POST('satuan'),
                'harga'         => $this->POST('harga')
            ];
            $this->bahan_baku_m->insert($data_bahan_baku);
            $this->flashmsg('Input bahan baku berhasil');
            redirect('admin/bahan_baku');
            exit;
        }

        if ($this->POST('delete') && $this->POST('id_bahan_baku'))
        {
            $this->bahan_baku_m->delete($this->POST('id_bahan_baku'));
            exit;
        }

        if ($this->POST('get') && $this->POST('id_bahan_baku'))
        {
            $data_bahan_baku = $this->bahan_baku_m->get_row(['id_bahan_baku' => $this->POST('id_bahan_baku')]);
            $supp_dropdown = [];
            $supplier = $this->supplier_m->get_by_order('id_suplier', 'DESC');
            foreach ($supplier as $row)
                $supp_dropdown[$row->id_suplier] = $row->nama_suplier;
            $data_bahan_baku->dropdown = form_dropdown('edit_id_suplier', $supp_dropdown, $data_bahan_baku->id_suplier, ['class' => 'form-control', 'id' => 'edit_id_suplier']);
            echo json_encode($data_bahan_baku);
            exit;
        }

        if ($this->POST('edit'))
        {
            $edit_bahan_baku = [
                'id_suplier'    => $this->POST('edit_id_suplier'),
                'nama_bahan'    => $this->POST('edit_nama'),
                'jenis_bahan'   => $this->POST('edit_jenis'),
                'satuan'        => $this->POST('edit_satuan'),
                'harga'         => $this->POST('edit_harga')
            ];
            $this->bahan_baku_m->update($this->POST('edit_id_bahan_baku'), $edit_bahan_baku);
            $this->flashmsg('Data bahan baku berhasil di-edit');
            redirect('admin/bahan_baku');
            exit;
        }

        $this->data['title']        = 'Bahan Baku';
        $this->data['content']      = 'admin/bahan_baku'; 
        $this->data['supplier']     = $this->supplier_m->get();
        $this->data['bahan_baku']   = $this->bahan_baku_m->get_by_order('id_bahan_baku', 'DESC');
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

        $this->data['title']        = 'Supplier';
        $this->data['content']      = 'admin/data_supplier';
        $this->data['supplier']     = $this->supplier_m->get_by_order('id_suplier', 'DESC');
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
