<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?=base_url('assets/img/avatar3.png')?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>Hello <?= $username ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php if($id_role == 2): ?>
        <ul class="sidebar-menu">
            <li class="active">
                <a href="<?= base_url('admin') ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/data_anggota') ?>">
                    <i class="fa fa-book"></i>
                    <span>Anggota</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/data_simpanan') ?>">
                    <i class="fa fa-book"></i>
                    <span>Simpanan</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Data Pinjaman</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('admin/data_pinjaman') ?>"><i class="fa fa-angle-double-right"></i> Pinjaman</a></li>
                    <li><a href="<?= base_url('admin/data_angsuran') ?>"><i class="fa fa-angle-double-right"></i> Angsuran</a></li>
                </ul>
            </li>
        </ul>
        <?php else: ?>
        <ul class="sidebar-menu">
            <li class="active">
                <a href="<?= base_url('ketua_koperasi') ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('ketua_koperasi/data_anggota') ?>">
                    <i class="fa fa-book"></i>
                    <span>Anggota</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('ketua_koperasi/data_simpanan') ?>">
                    <i class="fa fa-book"></i>
                    <span>Simpanan</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Data Pinjaman</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('ketua_koperasi/data_pinjaman') ?>"><i class="fa fa-angle-double-right"></i> Pinjaman</a></li>
                    <li><a href="<?= base_url('ketua_koperasi/data_angsuran') ?>"><i class="fa fa-angle-double-right"></i> Angsuran</a></li>
                </ul>
            </li>
        </ul>
        <?php endif; ?>
    </section>
    <!-- /.sidebar -->
</aside>
<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">
