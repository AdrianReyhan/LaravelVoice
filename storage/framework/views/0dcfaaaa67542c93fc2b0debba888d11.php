<?php $__env->startSection('content'); ?>
    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-2"><?php echo e(__('Forgot Your Password?')); ?></h1>
                                    <p class="mb-4"><?php echo e(__('We get it, stuff happens. Just enter your email address below
                                        and we will send you a link to reset your password!')); ?></p>
                                </div>
                                <form class="user" action="<?php echo e(route('password.email')); ?>" method="post">
                                    <?php echo csrf_field(); ?>

                                    <div class="form-group">
                                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control form-control-user <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               id="exampleInputEmail" aria-describedby="emailHelp"
                                               placeholder="<?php echo e(__('Enter Email Address...')); ?>">
                                    </div>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="form-group custom-control">
                                        <label class=""><?php echo e($message); ?></label>
                                    </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <?php echo e(__('Reset My Password')); ?>

                                    </button>
                                </form>
                                <hr>
                                <?php if(Route::has('register')): ?>
                                    <div class="text-center">
                                        <a class="small" href="<?php echo e(route('register')); ?>"><?php echo e(__('Create New Account!')); ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if(Route::has('login')): ?>
                                    <div class="text-center">
                                        <a class="small" href="<?php echo e(route('login')); ?>"><?php echo e(__('Already have an account? Login!')); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/auth/passwords/email.blade.php ENDPATH**/ ?>