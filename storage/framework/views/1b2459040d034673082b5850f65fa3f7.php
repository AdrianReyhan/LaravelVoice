<?php $__env->startSection('content'); ?>
    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4"><?php echo e(__('Welcome Back!')); ?></h1>
                                </div>
                                <form action="<?php echo e(route('login')); ?>" method="post" class="user">
                                    <?php echo csrf_field(); ?>

                                    <div class="form-group">
                                        <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                                               class="form-control form-control-user <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               id="exampleInputEmail" aria-describedby="emailHelp"
                                               placeholder="<?php echo e(__('Enter Email Address')); ?>" required autofocus>
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

                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control form-control-user <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               id="exampleInputPassword" placeholder="<?php echo e(__('Password')); ?>" required>
                                    </div>
                                    <?php $__errorArgs = ['password'];
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

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" name="remember" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck"><?php echo e(__('Remember Me')); ?></label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <?php echo e(__('Login')); ?>

                                    </button>
                                </form>
                                <hr>
                                <?php if(Route::has('password.request')): ?>
                                <div class="text-center">
                                    <a class="small" href="<?php echo e(route('password.request')); ?>"><?php echo e(__('Forgot Password?')); ?></a>
                                </div>
                                <?php endif; ?>
                                <?php if(Route::has('register')): ?>
                                <div class="text-center">
                                    <a class="small" href="<?php echo e(route('register')); ?>"><?php echo e(__('Create New Account!')); ?></a>
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

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/auth/login.blade.php ENDPATH**/ ?>