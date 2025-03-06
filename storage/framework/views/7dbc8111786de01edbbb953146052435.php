<?php $__env->startSection('custom_styles'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

         <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                <?php echo e(__('You are logged in!')); ?>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/home.blade.php ENDPATH**/ ?>