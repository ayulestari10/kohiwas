<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Supplier <button id="add" class="btn btn-success" data-toggle="modal" data-target="#addSupplier"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Supplier</li>
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
                                <th>Alamat</th>
                                <th width="150"></th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>aa</td>
                                <td>aa</td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#editSupplier"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" tabindex="-1" role="dialog" id="addSupplier">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Supplier</h4>
          </div>
          <div class="modal-body">
            <form>
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat *</label>
                    <textarea class="form-control" rows="3" name="" required></textarea>
                </div>
                
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary">Simpan</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" tabindex="-1" role="dialog" id="editSupplier">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Supplier</h4>
          </div>
          <div class="modal-body">
            <form>
                <div class="form-group">
                    <label for="Nama">Nama</label>
                    <input type="text" class="form-control" name="" value="" required>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat</label>
                    <textarea class="form-control" rows="3" name="" required></textarea>
                </div>
                
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary">Simpan</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->