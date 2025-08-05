<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php if(request()->routeIs('home')): ?> active <?php endif; ?>">
        <a class="nav-link" href="<?php echo e(route('home')); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span><?php echo e(__('Dashboard')); ?></span></a>
    </li>

    <!-- Nav Item - Tables -->
    <li class="nav-item <?php if(request()->routeIs('users.index')): ?> active <?php endif; ?>">
        <a class="nav-link" href="<?php echo e(route('users.index')); ?>">
            <i class="fas fa-fw fa-users"></i>
            <span><?php echo e(__('Users')); ?></span></a>
    </li>
    <li class="nav-item <?php if(request()->routeIs('statuses.index')): ?> active <?php endif; ?>">
        <a class="nav-link" href="<?php echo e(route('statuses.index')); ?>">
            <i class="fas fa-fw fa-users"></i>
            <span><?php echo e(__('Status')); ?></span></a>
    </li>

    <li class="nav-item <?php if(request()->routeIs('voiceEnroll.index')): ?> active <?php endif; ?>">
        <a class="nav-link" href="<?php echo e(route('voiceEnroll.index')); ?>">
            <i class="fas fa-volume-up"></i>
            <span><?php echo e(__('Voice Enroll')); ?></span></a>
    </li>
    
    <li class="nav-item <?php if(request()->routeIs('faceEnrol.index')): ?> active <?php endif; ?>">
        <a class="nav-link" href="<?php echo e(route('faceEnrol.index')); ?>">
            <i class="fas fa-fw fa-eye"></i>
            <span><?php echo e(__('Face Enroll')); ?></span></a>
    </li>
    </li>
    
    <li class="nav-item <?php if(request()->routeIs('verifikasi.index')): ?> active <?php endif; ?>">
        <a class="nav-link" href="<?php echo e(route('verifikasi.index')); ?>">
            <i class="fas fa-fw fa-eye"></i>
            <span><?php echo e(__('verif')); ?></span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo" style="padding-top: inherit;">
            <i class="fas fa-fw fa-cog"></i>
            <span>Two-level menu</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#">Child menu</a>
            </div>
        </div>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline pt-4">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
<?php /**PATH E:\voiceLaravel\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>