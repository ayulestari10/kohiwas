<!-- Content Header (Page header) -->
<div class="container">
    <section class="content-header">
        <h1>
            Buku Besar
            <small style="font-size: 14px;"><a href="<?= base_url('ketua_koperasi/cetakBukuBesar') ?>"><i class="fa fa-download"></i> Cetak</a></small>
        </h1>
    </section>

    <div class="container">
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Entri buku besar
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
                                <?= form_open('ketua_koperasi/data_bukuBesar') ?>
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
                            <b class="pull-left">Nama Akun: Kas</b>
                            <b class="pull-right">Kode Akun: 200</b>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <tr>
                                    <th rowspan="2">Tanggal</th>
                                    <th rowspan="2">Keterangan</th>
                                    <th rowspan="2">Ref</th>
                                    <th rowspan="2">Debit</th>
                                    <th rowspan="2">Kredit</th>
                                    <th colspan="2">Saldo</th>
                                </tr>
                                <tr>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                </tr>
                                <?php foreach ($buku_besar as $row): ?>
                                <tr>
                                    <td><?= $row->tgl  ?></td>
                                    <td><?= $row->ket ?></td>
                                    <td><?= $row->ref ?></td>
                                    <td><?= "Rp " . number_format($row->debit,2,',','.') ?></td>
                                    <td><?= "Rp " . number_format($row->kredit,2,',','.') ?></td>
                                    <td><?= "Rp " . number_format($row->saldo_debit,2,',','.') ?></td>
                                    <td><?= "Rp " . number_format($row->saldo_kredit,2,',','.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                            <!-- /.table-responsive -->
                            <table class="table" style="width: 50%;">
                                <tr>
                                    <td><b>Total Saldo Debit</b></td>
                                    <td><?= "Rp " . number_format($total->total_saldo_debit,2,',','.') ?></td>
                                </tr>
                                <tr>
                                    <td><b>Total Saldo Kredit</b></td>
                                    <td><?= "Rp " . number_format($total->total_saldo_kredit,2,',','.') ?></td>
                                </tr>
                            </table>
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