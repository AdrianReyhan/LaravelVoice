

<?php $__env->startSection('content'); ?>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Edit Status</h1>

     

        <!-- Form Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-column">
                <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Status</h6>
                <small class="text-muted">Silakan ubah data status absen di bawah ini.</small>
            </div>

            <div class="card-body">
                <form action="<?php echo e(route('statuses.update', $statuses->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <div class="form-group">
                        <label for="nama_status">Nama Status</label>
                        <input type="text" name="nama_status" id="nama_status"
                            class="form-control <?php $__errorArgs = ['nama_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('nama_status', $statuses->nama_status)); ?>"
                            placeholder="Contoh: Hadir, Izin, Sakit" required autofocus>

                        <?php $__errorArgs = ['nama_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback">
                                <?php echo e($message); ?>

                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="<?php echo e(route('statuses.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/status/edit.blade.php ENDPATH**/ ?>