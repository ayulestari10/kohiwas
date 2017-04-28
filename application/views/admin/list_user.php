<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Data User</h3>  
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">
                      
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID User</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Total Score</th>
                                    <th>Rank</th>
                                    <th>Last Login</th>
                                    <th>IP Address</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($list_user as $row): ?>
                                <?php if ($username != $row->username): ?>
                                <tr>
                                  <td><?= $row->id_user ?></td>
                                  <td><?= $row->username ?></td>
                                  <td><?= $this->role_m->get_row(['id_role' => $row->id_role])->nama ?></td>
                                  <td><?= $this->recent_file_m->get_total_score($row->id_user) ?></td>
                                  <td><?= $this->recent_file_m->get_user_rank($row->id_user) ?></td>
                                  <td><?= $row->last_login ?></td>
                                  <td><?= $row->ip_address ?></td>
                                  <td>
                                      <button class="btn btn-primary" onclick="changePassword('<?= $row->id_user ?>')" data-toggle="modal" data-target="#change-password"><i class="fa fa-edit"></i></button>
                                      <button class="btn btn-danger" onclick="deleteUser('<?= $row->id_user ?>')"><i class="fa fa-trash-o"></i></button>
                                  </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#add-new"><i class="fa fa-edit"></i> Tambah User</button>
                    </div>
                </div>
            </div>
      </div>
    </section>
