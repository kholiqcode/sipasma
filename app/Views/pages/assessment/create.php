<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Self Assessment Penyakit Asma</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Beranda</a></li>
                        <li class="breadcrumb-item">Assessment</a></li>
                        <li class="breadcrumb-item active">Assessment Asma</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <?php if (!empty(session()->getFlashdata('error'))) : ?>
                        <div class="card bg-danger">
                            <div class="card-header">
                                <h3 class="card-title">Error</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <?php echo session()->getFlashdata('error'); ?>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    <?php endif; ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Self Assessment Penyakit Asma</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="<?= base_url('assessment/store') ?>" id="form">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-4" for="nama">Nama</label>
                                    <input type="text" name="nama" class="form-control col-md-8" value="<?= old('nama') ?>" id="nama" placeholder="Masukkan Nama">
                                </div>
                                <?php foreach ($gejala as $item) : ?>
                                    <div class="form-group row">
                                        <label class="col-md-4" for="gejala"><?= $item['nama'] ?></label>
                                        <div class="icheck-primary d-inline col-md-2">
                                            <input type="radio" id="radioPrimary1<?= $item['id'] ?>" value="akut" name="gejala[<?= $item['id'] ?>]">
                                            <label for="radioPrimary1<?= $item['id'] ?>">
                                                Akut
                                            </label>
                                        </div>
                                        <div class="icheck-primary d-inline col-md-2">
                                            <input type="radio" id="radioPrimary2<?= $item['id'] ?>" value="kronis" name="gejala[<?= $item['id'] ?>]">
                                            <label for="radioPrimary2<?= $item['id'] ?>">
                                                Kronis
                                            </label>
                                        </div>
                                        <div class="icheck-primary d-inline col-md-2">
                                            <input type="radio" id="radioPrimary3<?= $item['id'] ?>" value="periodik" name="gejala[<?= $item['id'] ?>]">
                                            <label for="radioPrimary3<?= $item['id'] ?>">
                                                Periodik
                                            </label>
                                        </div>
                                        <div class="icheck-primary d-inline">
                                            <input type="radio" id="radioPrimary4<?= $item['id'] ?>" value="unknown" name="gejala[<?= $item['id'] ?>]">
                                            <label for="radioPrimary4<?= $item['id'] ?>">
                                                Tidak Tahu
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
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
<script src="<?= base_url('assets/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<script>
    //Initialize Toast Elements
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    // $('#form').submit(function(e) {
    //     e.preventDefault();
    //     $.ajax({
    //         url: $(this).attr('action'),
    //         type: "POST",
    //         data: $(this).serialize(),
    //         dataType: "json",
    //         cache: false,
    //         async: false,
    //         success: function(response) {
    //             Toast.fire({
    //                 icon: 'success',
    //                 title: response?.meta?.message
    //             })
    //             // setTimeout(function() {
    //             //     window.location.href = "<?php //base_url('gejala/list') ?>";
    //             // }, 1000)
    //         },
    //         error: function(response) {
    //             response = response.responseJSON;
    //             Toast.fire({
    //                 icon: 'error',
    //                 title: response?.meta?.message
    //             })
    //         }
    //     });
    // });
</script>
<?= $this->endSection() ?>
<?= $this->section('style') ?>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="<?= base_url('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">
<!-- Select2 -->
<link rel="stylesheet" href="<?= base_url('assets/plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="<?= base_url('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
<?= $this->endSection() ?>