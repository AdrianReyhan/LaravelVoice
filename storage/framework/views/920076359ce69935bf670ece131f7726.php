

<?php $__env->startSection('content'); ?>
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Status</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Table</h6>
            </div>
            <div class="ml-3 mt-4">
                <a href="<?php echo e(route('statuses.create')); ?>" class="btn btn-primary">Tambah Status</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($status->nama_status); ?></td>
                                    <td class="d-flex ">
                                        <a href="<?php echo e(route('statuses.show', $status->id)); ?>"
                                            class="btn btn-info btn-sm mr-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('statuses.edit', $status->id)); ?>"
                                            class="btn btn-primary btn-sm mr-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('statuses.destroy', $status->id)); ?>" method="POST"
                                            class="d-inline delete-form">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                                                </button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/status/index.blade.php ENDPATH**/ ?>