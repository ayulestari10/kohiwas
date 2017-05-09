<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Bahan Baku Minimum
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Bahan Baku Minimum</li>
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
                        <table class="table table-striped">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Stok</th>
                                <th>Stok Minimum</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <!-- <th width="150"></th> -->
                            </tr>
                            <?php $i = 0; foreach ($bahan_baku_min as $row): ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <td><?= $row->nama_bahan ?></td>
                                    <td><?= $row->jenis_bahan ?></td>
                                    <td><?= $row->stok ?></td>
                                    <td><?= $row->stok_min ?></td>
                                    <td><?= $row->satuan ?></td>
                                    <td><?= $row->harga ?></td>
                                    <td>
                                        <button class="btn btn-info" data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i></button>
                                        <button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>