<?php
  $nama = $this->operator_unit_m->get_row(['username'=>$this->session->userdata('username')])->nama;
 ?>
    <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="<?=base_url('operator')?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                Operator Unit
            </a>
        <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?=$nama?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="<?=base_url('logout')?>" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
