      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
            Jurnal Umum
            <small style="font-size: 14px;"><a href="<?= base_url('ketua_koperasi/cetakJurnal') ?>"><i class="fa fa-download"></i> Cetak</a></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('ketua_koperasi') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Jurnal Umum</li>
        </ol>
    </section>

      <div class="container">
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Simpanan
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <style type="text/css">
                                tr th, tr td {text-align: center;}
                            </style>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Debit</th>
                                        <th>Kredit</th>
                                    </tr>
                                </thead>
                                <tbody id="data-view">
                                    <?php foreach ($jurnal_umum as $row): ?>
                                    <tr>
                                        <td><?= $row->tgl ?></td>
                                        <td><?= $row->ket ?></td>
                                        <td><?= "Rp " . number_format($row->debit,2,',','.') ?></td>
                                        <td><?= "Rp " . number_format($row->kredit,2,',','.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
    </div>