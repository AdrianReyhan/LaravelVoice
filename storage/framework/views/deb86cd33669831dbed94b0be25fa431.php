<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mehrdad Amini">
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <!-- Custom fonts for this template-->
    <link href="<?php echo e(asset('css/fontawsome-free-all.min.css')); ?>" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template-->
    <link href="<?php echo e(asset('css/sb-admin-2.min.css')); ?>" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

<div class="container">

    <?php echo $__env->yieldContent('content'); ?>

</div>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>

<!-- Core plugin JavaScript-->
<script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/jquery.easing-1.4.1.min.js')); ?>"></script>

<!-- Custom scripts for all pages-->
<script src="<?php echo e(asset('js/sb-admin-2.min.js')); ?>"></script>

</body>

</html>
<?php /**PATH E:\voiceLaravel\resources\views/layouts/guest.blade.php ENDPATH**/ ?>