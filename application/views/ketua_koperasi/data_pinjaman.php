<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Pinjaman 
            <small style="font-size: 14px;"><a href="<?= base_url('ketua_koperasi/cetakPinjaman') ?>"><i class="fa fa-download"></i> Cetak</a></small>
        </h1>
        <ol class="breadcrumb">

            <li><a href="<?= base_url('admin') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Pinjaman</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <!-- <h3 class="box-title">Data Supplier</h3>   -->
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <?= $this->session->flashdata('msg') ?>
                        <table class="table table-striped">
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Tanggal Pinjaman</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Bunga</th>
                                <th>TTL Pinjaman</th>
                                <th>Lama Pinjaman</th>
                                <th>Angsuran</th>
                                <th>Status</th>
                            </tr>
                           <?php $i = 0; foreach ($pinjaman as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $this->anggota_m->get_row(['id_anggota' => $row->id_anggota])->nama ?></td>
                                <td><?= $row->tgl_pinjaman ?></td>
                                <td><?= "Rp " . number_format($row->jlh_pinjaman,2,',','.') ?></td>
                                <td><?= "Rp " . number_format($row->bunga,2,',','.') ?></td>
                                <td><?= $row->ttl_pinjaman ?></td>
                                <td><?= $row->lama_pinjaman ?></td>
                                <td><?= $row->angsuran ?></td>
                                <td><?= $row->status ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>