

<?php $__env->startSection('content'); ?>
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800"><?php echo e(__('Verifikasi Wajah')); ?></h1>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                <?php echo e(__('Silakan unggah gambar untuk verifikasi wajah.')); ?>

                            </p>

                            <!-- Form untuk mengunggah gambar -->
                            <h3>Upload Gambar untuk Pengenalan Wajah</h3>
                            <form action="<?php echo e(url('/verify-face')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="form-group">
                                    <input type="file" name="image" accept="image/*" required>
                                    <button type="submit" class="btn btn-primary">Verify Face</button>
                                </div>
                            </form>

                            <!-- Menampilkan hasil verifikasi -->
                            <?php if(session('status')): ?>
                                <div class="alert alert-<?php echo e(session('status') == 'success' ? 'success' : 'danger'); ?>">
                                    <p><?php echo e(session('message') ?? (session('status') == 'success' ? 'Verifikasi berhasil!' : 'Verifikasi gagal!')); ?></p>
                                    <?php if(session('identity')): ?>
                                        <p><strong>Identitas:</strong> <?php echo e(session('identity')); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Menampilkan gambar yang diupload -->
                            <?php if(session('image_path')): ?>
                                <h3>Gambar yang Diupload:</h3>
                                <img src="<?php echo e(asset('storage/' . session('image_path'))); ?>" alt="Uploaded Image" class="img-fluid">
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/verifwajah/index.blade.php ENDPATH**/ ?>