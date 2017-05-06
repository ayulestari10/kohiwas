<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
           Daftar Permintaan Disetujui
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Permintaan Disetujui</li>
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
                              <th>Nama</th>
                              <th>Unit</th>
                              <th>Tanggal Permintaan</th>
                              <th>Batas Waktu</th>
                              <th>Keterangan</th>
                              <th>Aksi</th>
                            </tr>
                            <?php $i = 0; foreach ($permintaan as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?=$row->nama?></td>
                                <td><?= $this->unit_m->get_row(['id_unit'=>$row->id_unit])->nama_unit ?></td>
                                <td><?= $row->tanggal_permintaan ?></td>
                                <td><?= $row->batas_waktu ?></td>
                                <td><?=$row->keterangan?></td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#editSupplier" onclick="get_permintaan(<?= $row->id_permintaan ?>)"><i class="fa fa-eye"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        function delete_permintaan(id) {
            $.ajax({
                url: '<?= base_url('operator/permintaan') ?>',
                type: 'POST',
                data: {
                    id_permintaan: id,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('operator/permintaan') ?>';
                }
            });
        }

        function get_permintaan(id) {
            $.ajax({
                url: '<?= base_url('operator/permintaan') ?>',
                type: 'POST',
                data: {
                    id_permintaan: id ,
                    get: true
                },
                success: function(response) {
                    response = JSON.parse(response);
                    $('#edit_id_permintaan').val(response.id_permintaan);
                    $('#edit_nama').val(response.nama);
                    $('#edit_keterangan').val(response.keterangan);
                }
            });
        }
    </script>
