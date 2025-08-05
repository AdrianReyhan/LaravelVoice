

<?php $__env->startSection('content'); ?>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Detail Status</h1>

        <!-- Flash Success Message -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> <?php echo e(session('success')); ?>

                <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Detail Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-column">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Status Absen</h6>
                <small class="text-muted">Berikut adalah detail status absen.</small>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label><strong>ID:</strong></label>
                    <p><?php echo e($statuses->id); ?></p>
                </div>

                <div class="form-group">
                    <label><strong>Nama Status:</strong></label>
                    <p><?php echo e($statuses->nama_status); ?></p>
                </div>

                <div class="form-group mt-4">
                    <a href="<?php echo e(route('statuses.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <a href="<?php echo e(route('statuses.edit', $statuses->id)); ?>" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/status/show.blade.php ENDPATH**/ ?>