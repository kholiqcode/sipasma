<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo base_url('home'); ?>" class="brand-link">
        <img src="<?= base_url() ?>/assets/dist/img/e.png" class="brand-image img-circle elevation-5" style="opacity: .">
        <span class="brand-text font-weight-bold">SIPASMA</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url('assets/dist/img/user2-160x160.jpg') ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= session()->get('nama') ?? 'Guest' ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <?php if (!is_null(session()->get('logged_in'))) : ?>
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard') ?>" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('gejala/list') ?>" class="nav-link">
                            <i class="nav-icon fas fa-virus"></i>
                            <p>
                                Gejala
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('assessment') ?>" class="nav-link">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Assessment
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('assessment/list') ?>" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>
                                List Assessment
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('auth/logout') ?>" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a href="<?= base_url('assessment') ?>" class="nav-link">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Assessment
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('auth/login') ?>" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Login
                            </p>
                        </a>
                    </li>
                <?php endif ?>

            </ul>
            </br>
            </br>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>