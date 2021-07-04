<?= $this->extend('layouts/base') ?>
<?= $this->section('app') ?>
<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="<?= base_url('assets/dist/img/AdminLTELogo.png') ?>" alt="AdminLTELogo" height="60" width="60">
    </div>

    <!-- Navbar -->
    <?= $this->include('components/navbar') ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?= $this->include('components/sidebar') ?>
    <!-- End Of Main Sidebar Container -->

    <!-- Content Wrapper. Contains page content -->
    <?= $this->renderSection('content') ?>
    <!-- /.content-wrapper -->

    <?= $this->include('components/footer') ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?= $this->endSection() ?>