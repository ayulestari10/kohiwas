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
                <p>Hello Ayu</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="active">
                <a href="<?= base_url('admin') ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview">
                <a href="<?= base_url('admin/dataSimpanan') ?>">
                    <i class="fa fa-book"></i>
                    <span>Simpanan</span>
                </a>
            </li>
            <li class="treeview">
                <a href="<?= base_url('admin/dataPinjaman') ?>">
                    <i class="fa fa-book"></i>
                    <span>Pinjaman</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('admin/dataAngsuran') ?>"><i class="fa fa-angle-double-right"></i> Angsuran</a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">
