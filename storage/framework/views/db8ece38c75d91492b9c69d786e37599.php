

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Take Photos for Face Recognition</h1>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php elseif(session('error')): ?>
                <div class="alert alert-danger">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <!-- Form untuk mengupload gambar -->
            <form action="<?php echo e(route('uploadImage')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label for="images" class="form-label">Select 3 Images for Upload</label>
                    <!-- Menambahkan atribut multiple untuk memilih lebih dari satu gambar -->
                    <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple required>
                    <small class="form-text text-muted">You can select up to 3 images</small>
                </div>
                <button type="submit" class="btn btn-success mt-3">Upload Images</button>
            </form>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/face/index.blade.php ENDPATH**/ ?>