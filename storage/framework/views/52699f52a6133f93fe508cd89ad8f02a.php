

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Status</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create Status Absen</h6>
                <p class="text-sm text-gray-600">Tambah formulir berikut untuk menambah status absen.</p>
            </div>
            <div class="card-body">
                <!-- Form for Editing Status -->
                <form action="<?php echo e(route('statuses.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="nama_status">Status Absen</label>
                        <input type="text" id="nama_status" name="nama_status"
                            value="<?php echo e(old('nama_status')); ?>" required class="form-control" />

                        <?php $__errorArgs = ['nama_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            Simpan
                        </button>
                        <a href="<?php echo e(route('statuses.index')); ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/status/create.blade.php ENDPATH**/ ?>