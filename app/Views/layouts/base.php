<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Elektronik Sistem Informasi Kinerja Pemeriksa' ?> | e-SIKAP</title>

    <link rel="shortcut icon" href="<?= base_url('assets/dist/img/e.png') ?>">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <?= $this->renderSection('style') ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <?= $this->renderSection('app') ?>
    <!-- jQuery -->
    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js')?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets/dist/js/adminlte.min.js')?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= base_url('assets/dist/js/demo.js')?>"></script>
    <?= $this->renderSection('script') ?>

</body>

</html>