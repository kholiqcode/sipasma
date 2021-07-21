<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="<?= base_url() ?>" class="h1"><b>SI</b>-KARMA</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sistem Pakar Deteksi Asma</p>
            <?php if (!empty(session()->getFlashdata('error'))) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4>Periksa Entrian Form</h4>
                    </hr />
                    <?php echo session()->getFlashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <form action="<?= base_url('auth/process') ?>" method="post">
                <?= csrf_field(); ?>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" autofocus value="<?= old('email') ?>" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->

<?= $this->endSection() ?>