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
                            <?php if (!isset($periode)): ?>
                                <span><b>Periode: All</b></span>
                            <?php else: ?>
                                <span><b>Periode: <?= $periode['min'] . ' - ' . $periode['max'] ?></b></span>
                            <?php endif; ?>
                            <div class="periode-search row">
                                <?= form_open('ketua_koperasi/data_jurnal') ?>
                                <div class="col-md-3">
                                    <label for="tgl">Dari</label>
                                    <div class="input-group date">
                                          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                          <input type="text" name="periode[min]" id="tgl_dari" class="form-control" placeholder="YYYY-MM-DD" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="tgl">Sampai</label>
                                    <div class="input-group date">
                                          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                          <input type="text" name="periode[max]" id="tgl_sampai" class="form-control" placeholder="YYYY-MM-DD" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label> </label>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary" name="filter" value="Filter">
                                    </div>
                                </div>
                                <?= form_close() ?>
                            </div>
                            <br>
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
                            <table class="table" style="width: 50%;">
                                <tr>
                                    <td><b>Total Debit</b></td>
                                    <td><?= "Rp " . number_format($total->total_debit,2,',','.') ?></td>
                                </tr>
                                <tr>
                                    <td><b>Total Kredit</b></td>
                                    <td><?= "Rp " . number_format($total->total_kredit,2,',','.') ?></td>
                                </tr>
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

    <script type="text/javascript">
        $(document).ready(function() {
            $('.input-group.date').datepicker({format: "yyyy-mm-dd"});
        });
    </script>