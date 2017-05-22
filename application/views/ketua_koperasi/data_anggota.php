<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Anggota
            <small style="font-size: 14px;"><a href="<?= base_url('ketua_koperasi/cetakAnggota') ?>"><i class="fa fa-download"></i> Cetak</a></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('ketua_koperasi') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Anggota</li>
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
                                <th>NLP</th>
                                <th>Nama</th>
                                <th>Tanggal Mendaftar</th>
                                <th>Alamat</th>
                                <th>Simpanan Pokok</th>
                            </tr>
                            <?php $i = 0; foreach ($anggota as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $row->nlp ?></td>
                                <td><?= $row->nama ?></td>
                                <td><?= $row->tgl_mendaftar ?></td>
                                <td><?= $row->alamat ?></td>
                                <td><?= "Rp " . number_format($row->simpanan_pokok,2,',','.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>