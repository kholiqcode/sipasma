<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Hasil Assessment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="#">Assessment</a></li>
                        <li class="breadcrumb-item active">Hasil Assessment</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header justify-center">
                            <h3 class="card-title">Kondisi Anda</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Gejala</th>
                                        <th>Keparahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kondisi as $key => $item) : ?>
                                        <tr>
                                            <td><?= $key ?>.</td>
                                            <td><?= $item['nama'] ?></td>
                                            <td><span class="badge bg-danger"><?= $item['keparahan'] ?></span></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header justify-center">
                            <h3 class="card-title">Kondisi Anda</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th class="text-center">Akut</th>
                                        <th class="text-center">Kronis</th>
                                        <th class="text-center">Periodik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Kemungkinan</td>
                                        <td class="text-center"><?= $kemungkinan['akut'] ?>%</td>
                                        <td class="text-center"><?= $kemungkinan['kronis'] ?>%</td>
                                        <td class="text-center"><?= $kemungkinan['periodik'] ?>%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
<?= $this->endSection() ?>
<?= $this->section('script') ?>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="<?= base_url('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">
<script src="<?= base_url('assets/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<?= $this->endSection() ?>