<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'The NexZen - Business Portal'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="<?php echo e(asset('dist/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/common.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/dashboard/dashboard.css')); ?>">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Racing+Sans+One&display=swap" rel="stylesheet">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="<?php echo e(request()->routeIs('business.dashboard') ? 'business-dashboard-page' : ''); ?><?php echo e(request()->routeIs('business.vehicles.index') ? ' vehicle-management-page' : ''); ?><?php echo e(request()->routeIs('business.vendors.index') ? ' vendor-management-page' : ''); ?><?php echo e(request()->routeIs('business.customers.index') ? ' customer-management-page' : ''); ?>">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="logo">
            <img src="<?php echo e($business->logo ? asset('storage/' . $business->logo) : asset('images/mainLogo.svg')); ?>" alt="Logo" id="sidebarLogo">
            <h3><?php echo e($business->business_name ?? 'RENTCAR'); ?></h3>
        </div>
        
        <?php
            $business = auth('business_admin')->user()->business;
            $subscription = $business->subscriptions()->whereIn('status', ['active', 'trial'])->first();
            $canAccessVehicles = $subscription ? $subscription->canAccessModule('vehicles') : false;
            $canAccessBookings = $subscription ? $subscription->canAccessModule('bookings') : false;
            $canAccessCustomers = $subscription ? $subscription->canAccessModule('customers') : false;
            $canAccessVendors = $subscription ? $subscription->canAccessModule('vendors') : false;
            $canAccessReports = $subscription ? $subscription->canAccessModule('reports') : false;
            $canAccessNotifications = $subscription ? $subscription->canAccessModule('notifications') : false;
            $canAccessSubscription = true; // Always allow access to subscription management
        ?>
        
        <?php if($subscription): ?>
        <a href="<?php echo e(route('business.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('business.dashboard') ? 'menuActive' : ''); ?> dashboard-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0.5" width="29" height="30" rx="8" fill="white" />
                <path d="M15.6619 10.4674C15.6183 10.4256 15.5603 10.4023 15.4999 10.4023C15.4395 10.4023 15.3815 10.4256 15.3379 10.4674L9.94434 15.6198C9.92143 15.6417 9.90321 15.668 9.89077 15.6972C9.87833 15.7263 9.87194 15.7577 9.87197 15.7894L9.87109 20.6252C9.87109 20.8738 9.96987 21.1123 10.1457 21.2881C10.3215 21.4639 10.56 21.5627 10.8086 21.5627H13.624C13.7483 21.5627 13.8676 21.5133 13.9555 21.4254C14.0434 21.3375 14.0928 21.2182 14.0928 21.0939V17.1095C14.0928 17.0474 14.1175 16.9878 14.1614 16.9438C14.2054 16.8999 14.265 16.8752 14.3271 16.8752H16.6709C16.7331 16.8752 16.7927 16.8999 16.8366 16.9438C16.8806 16.9878 16.9053 17.0474 16.9053 17.1095V21.0939C16.9053 21.2182 16.9547 21.3375 17.0426 21.4254C17.1305 21.5133 17.2497 21.5627 17.374 21.5627H20.1883C20.4369 21.5627 20.6754 21.4639 20.8512 21.2881C21.027 21.1123 21.1258 20.8738 21.1258 20.6252V15.7894C21.1258 15.7577 21.1194 15.7263 21.107 15.6972C21.0945 15.668 21.0763 15.6417 21.0534 15.6198L15.6619 10.4674Z" fill="#6B6ADE" />
                <path d="M22.3821 14.6528L20.1907 12.5563V9.375C20.1907 9.25068 20.1413 9.13145 20.0534 9.04354C19.9655 8.95564 19.8463 8.90625 19.722 8.90625H18.3157C18.1914 8.90625 18.0722 8.95564 17.9843 9.04354C17.8964 9.13145 17.847 9.25068 17.847 9.375V10.3125L16.1501 8.69004C15.9913 8.52949 15.7552 8.4375 15.5 8.4375C15.2457 8.4375 15.0102 8.52949 14.8514 8.69033L8.61993 14.6522C8.43771 14.828 8.41485 15.1172 8.58068 15.3076C8.62232 15.3557 8.6733 15.3948 8.73053 15.4225C8.78776 15.4502 8.85003 15.4661 8.91356 15.469C8.97709 15.4719 9.04054 15.4618 9.10006 15.4394C9.15958 15.417 9.21392 15.3827 9.25978 15.3387L15.3389 9.52969C15.3825 9.48796 15.4405 9.46468 15.5009 9.46468C15.5612 9.46468 15.6193 9.48796 15.6629 9.52969L21.7426 15.3387C21.8321 15.4246 21.9521 15.4714 22.0762 15.469C22.2002 15.4666 22.3183 15.4151 22.4044 15.3258C22.5843 15.1395 22.5693 14.8318 22.3821 14.6528Z" fill="#6B6ADE" />
            </svg>
                        Dashboard
                    </a>
        <?php endif; ?>

        <?php if($canAccessBookings): ?>
        <a href="<?php echo e(route('business.bookings.index')); ?>" class="nav-link <?php echo e(request()->routeIs('business.bookings.*') ? 'menuActive' : ''); ?> data-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M11.0469 22.5312H10.1094C9.92289 22.5312 9.74405 22.4572 9.61219 22.3253C9.48033 22.1934 9.40625 22.0146 9.40625 21.8281V17.6094C9.40625 17.4229 9.48033 17.2441 9.61219 17.1122C9.74405 16.9803 9.92289 16.9062 10.1094 16.9062H11.0469C11.2334 16.9062 11.4122 16.9803 11.5441 17.1122C11.6759 17.2441 11.75 17.4229 11.75 17.6094V21.8281C11.75 22.0146 11.6759 22.1934 11.5441 22.3253C11.4122 22.4572 11.2334 22.5312 11.0469 22.5312V22.5312Z" fill="#6B6ADE" />
                <path d="M17.6094 22.5312H16.6719C16.4854 22.5312 16.3066 22.4572 16.1747 22.3253C16.0428 22.1934 15.9688 22.0146 15.9688 21.8281V14.7969C15.9688 14.6104 16.0428 14.4316 16.1747 14.2997C16.3066 14.1678 16.4854 14.0938 16.6719 14.0938H17.6094C17.7959 14.0938 17.9747 14.1678 18.1066 14.2997C18.2384 14.4316 18.3125 14.6104 18.3125 14.7969V21.8281C18.3125 22.0146 18.2384 22.1934 18.1066 22.3253C17.9747 22.4572 17.7959 22.5312 17.6094 22.5312V22.5312Z" fill="#6B6ADE" />
                <path d="M20.8906 22.5312H19.9531C19.7666 22.5312 19.5878 22.4572 19.4559 22.3253C19.3241 22.1934 19.25 22.0146 19.25 21.8281V11.5156C19.25 11.3291 19.3241 11.1503 19.4559 11.0184C19.5878 10.8866 19.7666 10.8125 19.9531 10.8125H20.8906C21.0771 10.8125 21.2559 10.8866 21.3878 11.0184C21.5197 11.1503 21.5937 11.3291 21.5937 11.5156V21.8281C21.5937 22.0146 21.5197 22.1934 21.3878 22.3253C21.2559 22.4572 21.0771 22.5312 20.8906 22.5312V22.5312Z" fill="#6B6ADE" />
                <path d="M14.3281 22.5312H13.3906C13.2041 22.5312 13.0253 22.4572 12.8934 22.3253C12.7616 22.1934 12.6875 22.0146 12.6875 21.8281V9.17187C12.6875 8.98539 12.7616 8.80655 12.8934 8.67469C13.0253 8.54283 13.2041 8.46875 13.3906 8.46875H14.3281C14.5146 8.46875 14.6934 8.54283 14.8253 8.67469C14.9572 8.80655 15.0312 8.98539 15.0312 9.17187V21.8281C15.0312 22.0146 14.9572 22.1934 14.8253 22.3253C14.6934 22.4572 14.5146 22.5312 14.3281 22.5312V22.5312Z" fill="#6B6ADE" />
            </svg>
            Bookings
        </a>
        <?php endif; ?>

        <?php if($canAccessVehicles): ?>
        <a href="<?php echo e(route('business.vehicles.index')); ?>" class="nav-link <?php echo e(request()->routeIs('business.vehicles.*') ? 'menuActive' : ''); ?> vehicle-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <g clip-path="url(#clip0_1061_10877)">
                    <path d="M6.58536 14.2619L7.36094 11.9288C7.36094 11.9089 7.36094 11.9089 7.38033 11.889C7.67117 11.1911 8.33041 10.7324 9.06721 10.7324H13.7982C15.4269 10.7524 16.9781 11.3905 18.1415 12.5869L19.6345 13.903H20.5458C22.5235 13.903 24.1328 15.558 24.1328 17.5919V18.3497C24.1328 18.6288 23.9195 18.8482 23.6481 18.8482H22.1357C21.8642 19.7455 21.0499 20.4035 20.0998 20.4035C19.1303 20.4035 18.316 19.7455 18.0639 18.8482H12.0144C11.743 19.7455 10.9286 20.4035 9.97851 20.4035C9.00904 20.4035 8.19469 19.7455 7.94262 18.8482H6.68231C6.31391 18.8482 6.00368 18.5291 6.00368 18.1503V15.5979C5.9649 15.0595 6.19757 14.5809 6.58536 14.2619ZM8.27224 12.2678L7.72934 13.903H9.84279L10.1336 11.7494H9.06721C8.7182 11.7494 8.40797 11.9488 8.27224 12.2678ZM13.3911 11.7494H11.1225L10.8317 13.903H13.9727L13.3911 11.7494ZM17.4822 13.3247L17.4628 13.3047C16.6291 12.4473 15.5627 11.9089 14.3993 11.7893L14.981 13.903H18.1415L17.4822 13.3247ZM20.0804 19.4065C20.7203 19.4065 21.2438 18.8681 21.2438 18.2101C21.2438 17.5521 20.7203 17.0137 20.0804 17.0137C19.4406 17.0137 18.9171 17.5521 18.9171 18.2101C18.9171 18.8681 19.4406 19.4065 20.0804 19.4065ZM9.93973 19.4065C10.5796 19.4065 11.1031 18.8681 11.1031 18.2101C11.1031 17.5521 10.5796 17.0137 9.93973 17.0137C9.29988 17.0137 8.77637 17.5521 8.77637 18.2101C8.77637 18.8681 9.29988 19.4065 9.93973 19.4065Z" fill="#6B6ADE" />
                </g>
                <defs>
                    <clipPath id="clip0_1061_10877">
                        <rect width="18.1333" height="16" fill="white" transform="translate(6 7)" />
                    </clipPath>
                </defs>
            </svg>
                        Vehicles
                    </a>
        <?php endif; ?>

        <?php if($canAccessVendors): ?>
        <a href="<?php echo e(route('business.vendors.index')); ?>" class="nav-link <?php echo e(request()->routeIs('business.vendors.*') ? 'menuActive' : ''); ?> vendor-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <g clip-path="url(#clip0_1054_10921)">
                    <path d="M20.7563 11.5314C20.7226 11.4657 20.6738 11.409 20.6139 11.3657C20.5541 11.3225 20.4849 11.294 20.412 11.2826C20.3391 11.2712 20.2645 11.2771 20.1943 11.3C20.1241 11.3229 20.0603 11.362 20.0081 11.4142L18.2081 13.2153C18.1197 13.3024 18.0006 13.3512 17.8766 13.3512C17.7525 13.3512 17.6334 13.3024 17.5451 13.2153L16.767 12.436C16.7234 12.3925 16.6889 12.3408 16.6653 12.284C16.6418 12.2271 16.6296 12.1661 16.6296 12.1046C16.6296 12.043 16.6418 11.982 16.6653 11.9251C16.6889 11.8683 16.7234 11.8166 16.767 11.7731L18.5593 9.98038C18.6131 9.92666 18.6529 9.86068 18.6755 9.78812C18.6981 9.71557 18.7027 9.63861 18.6889 9.56389C18.6751 9.48917 18.6434 9.41892 18.5964 9.35918C18.5494 9.29945 18.4887 9.25203 18.4193 9.22101V9.22101C17.0655 8.61573 15.3757 8.93155 14.3101 9.98917C13.4049 10.888 13.135 12.2925 13.5704 13.8426C13.5939 13.9252 13.594 14.0128 13.5708 14.0955C13.5476 14.1783 13.5019 14.253 13.4388 14.3113L8.5615 18.7659C8.37149 18.9365 8.21823 19.144 8.11107 19.3758C8.0039 19.6076 7.94508 19.8588 7.93819 20.1141C7.93129 20.3693 7.97647 20.6233 8.07098 20.8605C8.16548 21.0978 8.30732 21.3132 8.48784 21.4938C8.66836 21.6745 8.88379 21.8164 9.12097 21.911C9.35816 22.0056 9.61213 22.0509 9.86739 22.0441C10.1227 22.0373 10.3739 21.9786 10.6057 21.8716C10.8375 21.7645 11.0451 21.6113 11.2158 21.4214L15.7181 16.5332C15.7757 16.471 15.8491 16.4256 15.9305 16.402C16.0119 16.3784 16.0982 16.3775 16.1801 16.3993C16.6215 16.5202 17.0767 16.583 17.5342 16.5859C18.5128 16.5859 19.3715 16.2692 19.9908 15.659C21.1378 14.529 21.3127 12.6124 20.7563 11.5314ZM9.91472 21.1006C9.72177 21.1217 9.52704 21.0823 9.35741 20.988C9.18777 20.8937 9.05159 20.749 8.96767 20.574C8.88374 20.399 8.8562 20.2022 8.88886 20.0109C8.92151 19.8196 9.01274 19.6431 9.14996 19.5058C9.28719 19.3686 9.46363 19.2773 9.65495 19.2445C9.84627 19.2118 10.043 19.2393 10.2181 19.3232C10.3931 19.407 10.5378 19.5431 10.6322 19.7128C10.7266 19.8824 10.766 20.0771 10.745 20.27C10.7219 20.4823 10.627 20.6804 10.476 20.8314C10.325 20.9825 10.127 21.0774 9.91472 21.1006V21.1006Z" fill="#6B6ADE" />
                </g>
                <defs>
                    <clipPath id="clip0_1054_10921">
                        <rect width="15" height="15" fill="white" transform="translate(7 8)" />
                    </clipPath>
                </defs>
            </svg>
            Vendors
        </a>
        <?php endif; ?>

        <?php if($canAccessCustomers): ?>
        <a href="<?php echo e(route('business.customers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('business.customers.*') ? 'menuActive' : ''); ?> customer-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M16.7453 9.39199C16.1752 8.77646 15.3789 8.4375 14.5 8.4375C13.6164 8.4375 12.8175 8.77441 12.25 9.38613C11.6764 10.0046 11.3969 10.8451 11.4625 11.7527C11.5926 13.5434 12.9552 15 14.5 15C16.0448 15 17.4051 13.5437 17.5372 11.7533C17.6037 10.8539 17.3225 10.0151 16.7453 9.39199Z" fill="#6B6ADE" />
                <path d="M19.6564 21.5625H9.34392C9.20894 21.5643 9.07526 21.5359 8.95262 21.4795C8.82997 21.4231 8.72145 21.34 8.63493 21.2364C8.4445 21.0088 8.36775 20.6979 8.42458 20.3836C8.67185 19.0119 9.44353 17.8597 10.6564 17.0508C11.734 16.3327 13.0989 15.9375 14.5002 15.9375C15.9014 15.9375 17.2664 16.333 18.3439 17.0508C19.5568 17.8594 20.3285 19.0116 20.5757 20.3833C20.6326 20.6977 20.5558 21.0085 20.3654 21.2361C20.2789 21.3398 20.1704 21.4229 20.0478 21.4794C19.9251 21.5358 19.7914 21.5642 19.6564 21.5625Z" fill="#6B6ADE" />
            </svg>
                        Customers
                    </a>
        <?php endif; ?>

        <?php if($canAccessReports): ?>
        <a href="<?php echo e(route('business.reports.index')); ?>" class="nav-link <?php echo e(request()->routeIs('business.reports*') ? 'menuActive' : ''); ?> data-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <g clip-path="url(#clip0_1054_10948)">
                    <path d="M19.5391 14.5625H15.4375C15.0645 14.5625 14.7069 14.4143 14.4431 14.1506C14.1794 13.8869 14.0312 13.5292 14.0312 13.1562V9.05469C14.0312 9.02361 14.0189 8.9938 13.9969 8.97182C13.9749 8.94985 13.9451 8.9375 13.9141 8.9375H11.2187C10.7215 8.9375 10.2446 9.13504 9.89292 9.48667C9.54129 9.83831 9.34375 10.3152 9.34375 10.8125V20.1875C9.34375 20.6848 9.54129 21.1617 9.89292 21.5133C10.2446 21.865 10.7215 22.0625 11.2187 22.0625H17.7812C18.2785 22.0625 18.7554 21.865 19.1071 21.5133C19.4587 21.1617 19.6562 20.6848 19.6562 20.1875V14.6797C19.6562 14.6486 19.6439 14.6188 19.6219 14.5968C19.5999 14.5748 19.5701 14.5625 19.5391 14.5625Z" fill="#6B6ADE" />
                    <path d="M19.2818 13.5271L15.0687 9.31391C15.0605 9.30577 15.05 9.30023 15.0387 9.29799C15.0274 9.29575 15.0156 9.29691 15.005 9.30132C14.9943 9.30574 14.9851 9.31321 14.9787 9.3228C14.9723 9.33239 14.9688 9.34367 14.9688 9.35522V13.1583C14.9688 13.2826 15.0181 13.4018 15.106 13.4897C15.194 13.5776 15.3132 13.627 15.4375 13.627H19.2405C19.2521 13.627 19.2634 13.6235 19.273 13.617C19.2825 13.6106 19.29 13.6015 19.2944 13.5908C19.2988 13.5801 19.3 13.5684 19.2978 13.557C19.2955 13.5457 19.29 13.5353 19.2818 13.5271V13.5271Z" fill="#6B6ADE" />
                </g>
                <defs>
                    <clipPath id="clip0_1054_10948">
                        <rect width="11" height="15" fill="white" transform="translate(9 8)" />
                    </clipPath>
                </defs>
            </svg>
                        Reports
                    </a>
        <?php endif; ?>

        <?php if($canAccessNotifications): ?>
        <a href="<?php echo e(route('business.notifications.index')); ?>" class="nav-link <?php echo e(request()->routeIs('business.notifications.*') ? 'menuActive' : ''); ?> notifications-link">
            <svg width="20" height="20" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M15 2.5C10.8575 2.5 7.5 5.8575 7.5 10V12.5L5 15V17.5H25V15L22.5 12.5V10C22.5 5.8575 19.1425 2.5 15 2.5ZM15 20C13.625 20 12.5 18.875 12.5 17.5H17.5C17.5 18.875 16.375 20 15 20Z" fill="#6B6ADE" />
            </svg>
            Notifications
        </a>
        <?php endif; ?>
        </nav>

    <!-- Header -->
    <header class="header">
        <div class="row w-100">
            <div class="col-md-6">
                <div class="head_left">
                    <div class="header_title">
                        <div class="title_uptext"></div>
                        <h1><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                    </div>
                    <?php if(request()->routeIs('business.vehicles.index') || request()->routeIs('business.vendors.index') || request()->routeIs('business.customers.index')): ?>
                    <!-- Common Management Header Controls -->
                    <div class="common-header-controls">
                        <div class="common-search-container">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="commonSearch" class="form-control" placeholder="Search <?php echo e(request()->routeIs('business.vehicles.index') ? 'vehicles' : (request()->routeIs('business.vendors.index') ? 'vendors' : 'customers')); ?>...">
                            </div>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()" title="Clear Search">
                                <i class="fas fa-times me-1"></i>CLEAR
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
    </div>
            </div>
            <div class="col-md-6 profileCol">
                <div class="head_right">
                    <div class="profile">
                        <?php if(request()->routeIs('business.dashboard')): ?>
                        <div class="select-container" id="globalRecordsFilterContainer">
                            <button class="select-button" id="globalRecordsFilterButton"><?php echo e($currentRangeLabel ?? 'Last 7 days'); ?></button>
                            <div class="dropdown" id="globalRecordsDropdown">
                                <div class="option globalFilterOption" data-range="today">Today</div>
                                <div class="option globalFilterOption" data-range="yesterday">Yesterday</div>
                                <div class="option globalFilterOption" data-range="last7">Last 7 days</div>
                                <div class="option globalFilterOption" data-range="last30">Last 30 days</div>
                                <div class="option globalFilterOption" data-range="this_month">This Month</div>
                                <div class="option globalFilterOption" data-range="this_year">This Year</div>
                                <div class="option globalFilterOption" data-range="all">All Time</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if(request()->routeIs('business.vehicles.index') || request()->routeIs('business.vendors.index') || request()->routeIs('business.customers.index')): ?>
                        <!-- Dynamic Add Button for Management Pages -->
                        <div class="common-add-container">
                            <?php if(request()->routeIs('business.vehicles.index')): ?>
                                <?php
                                    $businessAdmin = auth('business_admin')->user();
                                    $business = $businessAdmin ? $businessAdmin->business : null;
                                    $subscription = $business ? $business->subscriptions()->whereIn('status', ['active', 'trial'])->first() : null;
                                    $capacityStatus = $subscription ? $subscription->getVehicleCapacityStatus() : null;
                                ?>
                                <?php if($capacityStatus && $capacityStatus['can_add']): ?>
                                    <a href="<?php echo e(route('business.vehicles.create')); ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>ADD NEW VEHICLE
                                    </a>
                                <?php elseif($capacityStatus && !$capacityStatus['can_add']): ?>
                                    <button class="btn btn-primary" onclick="showVehicleLimitModal()">
                                        <i class="fas fa-plus me-2"></i>ADD NEW VEHICLE
                    </button>
                                <?php else: ?>
                                    <a href="<?php echo e(route('business.vehicles.create')); ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>ADD NEW VEHICLE
                                    </a>
                                <?php endif; ?>
                            <?php elseif(request()->routeIs('business.vendors.index')): ?>
                                <a href="<?php echo e(route('business.vendors.create')); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>ADD NEW VENDOR
                                </a>
                            <?php elseif(request()->routeIs('business.customers.index')): ?>
                                <a href="<?php echo e(route('business.customers.create')); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>ADD NEW CUSTOMER
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="scan" id="viewFullScreen">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_160_572)">
                                    <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M20 40C31.0457 40 40 31.0457 40 20C40 8.9543 31.0457 0 20 0C8.9543 0 0 8.9543 0 20C0 31.0457 8.9543 40 20 40Z" fill="white" />
                                    <path d="M28.25 15.5C28.25 15.9142 28.5858 16.25 29 16.25C29.4142 16.25 29.75 15.9142 29.75 15.5H28.25ZM24.5 10.25C24.0858 10.25 23.75 10.5858 23.75 11C23.75 11.4142 24.0858 11.75 24.5 11.75V10.25ZM11.75 24.5C11.75 24.0858 11.4142 23.75 11 23.75C10.5858 23.75 10.25 24.0858 10.25 24.5H11.75ZM15.5 29.75C15.9142 29.75 16.25 29.4142 16.25 29C16.25 28.5858 15.9142 28.25 15.5 28.25V29.75ZM15.5 11.75C15.9142 11.75 16.25 11.4142 16.25 11C16.25 10.5858 15.9142 10.25 15.5 10.25V11.75ZM10.25 15.5C10.25 15.9142 10.5858 16.25 11 16.25C11.4142 16.25 11.75 15.9142 11.75 15.5H10.25ZM24.5 28.25C24.0858 28.25 23.75 28.5858 23.75 29C23.75 29.4142 24.0858 29.75 24.5 29.75V28.25ZM29.75 24.5C29.75 24.0858 29.4142 23.75 29 23.75C28.5858 23.75 28.25 24.0858 28.25 24.5H29.75ZM29.75 15.5C29.75 12.6005 27.3995 10.25 24.5 10.25V11.75C26.5711 11.75 28.25 13.4289 28.25 15.5H29.75ZM10.25 24.5C10.25 27.3995 12.6005 29.75 15.5 29.75V28.25C13.4289 28.25 11.75 26.5711 11.75 24.5H10.25ZM15.5 10.25C12.6005 10.25 10.25 12.6005 10.25 15.5H11.75C11.75 13.4289 13.4289 11.75 15.5 11.75V10.25ZM24.5 29.75C27.3995 29.75 29.75 27.3995 29.75 24.5H28.25C28.25 26.5711 26.5711 28.25 24.5 28.25V29.75Z" fill="white" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_160_572">
                                        <rect width="40" height="40" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <?php if($canAccessNotifications): ?>
                        <div class="notify">
                            <span class="notification-trigger">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_160_568)">
                                        <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M20 40C31.0457 40 40 31.0457 40 20C40 8.9543 31.0457 0 20 0C8.9543 0 0 8.9543 0 20C0 31.0457 8.9543 40 20 40Z" fill="white" />
                                        <path d="M14.0278 16.6326L14.7517 16.8287L14.7517 16.8287L14.0278 16.6326ZM18.1584 12.3022L17.9271 11.5888L18.1584 12.3022ZM13.983 16.798L13.2591 16.6019L13.2591 16.6019L13.983 16.798ZM13.7896 20.8674L14.5288 20.7409V20.7409L13.7896 20.8674ZM13.8121 20.9989L13.0728 21.1255L13.0728 21.1255L13.8121 20.9989ZM14.2876 25.4572L14.4373 24.7223L14.2876 25.4572ZM14.6552 25.5321L14.8049 24.7971H14.8049L14.6552 25.5321ZM25.3448 25.5321L25.1951 24.7971H25.1951L25.3448 25.5321ZM25.7124 25.4572L25.8621 26.1921L25.7124 25.4572ZM26.1922 20.9737L26.9315 21.1003V21.1003L26.1922 20.9737ZM26.2161 20.8343L25.4769 20.7077V20.7077L26.2161 20.8343ZM26.0417 16.8262L25.3163 17.0166V17.0166L26.0417 16.8262ZM25.9793 16.5883L26.7047 16.398V16.398L25.9793 16.5883ZM21.9671 12.3084L22.2023 11.5962V11.5962L21.9671 12.3084ZM26.353 21.3008L25.9868 21.9553L26.353 21.3008ZM13.668 21.2892L13.3088 20.6308L13.668 21.2892ZM20.75 11C20.75 10.5858 20.4142 10.25 20 10.25C19.5858 10.25 19.25 10.5858 19.25 11H20.75ZM19.25 12.0047C19.25 12.419 19.5858 12.7547 20 12.7547C20.4142 12.7547 20.75 12.419 20.75 12.0047H19.25ZM17.0091 25.9034L17.0929 25.1581L16.1956 25.0572L16.2611 25.9578L17.0091 25.9034ZM22.9909 25.9034L23.7389 25.9578L23.8044 25.0572L22.9071 25.1581L22.9909 25.9034ZM22.9001 26.4772L23.6285 26.6563L22.9001 26.4772ZM22.8182 26.8106L22.0899 26.6315L22.8182 26.8106ZM20.7039 28.917L20.8784 29.6464H20.8784L20.7039 28.917ZM19.2961 28.917L19.1216 29.6464H19.1216L19.2961 28.917ZM17.1818 26.8106L16.4535 26.9896L17.1818 26.8106ZM17.0999 26.4772L17.8282 26.2981H17.8282L17.0999 26.4772ZM14.7517 16.8287C15.2392 15.0293 16.6205 13.5891 18.3896 13.0157L17.9271 11.5888C15.6795 12.3173 13.925 14.1442 13.3039 16.4364L14.7517 16.8287ZM14.7069 16.9942L14.7517 16.8287L13.3039 16.4364L13.2591 16.6019L14.7069 16.9942ZM14.5288 20.7409C14.3153 19.4939 14.3761 18.215 14.7069 16.9942L13.2591 16.6019C12.8713 18.0331 12.8 19.5322 13.0503 20.994L14.5288 20.7409ZM14.5513 20.8723L14.5288 20.7409L13.0503 20.994L13.0728 21.1255L14.5513 20.8723ZM13.25 23.2604C13.25 22.6932 13.5638 22.2003 14.0271 21.9476L13.3088 20.6308C12.3807 21.137 11.75 22.1251 11.75 23.2604H13.25ZM14.4373 24.7223C13.7489 24.582 13.25 23.9725 13.25 23.2604H11.75C11.75 24.6807 12.7462 25.9085 14.1379 26.1921L14.4373 24.7223ZM14.8049 24.7971L14.4373 24.7223L14.1379 26.1921L14.5055 26.267L14.8049 24.7971ZM25.1951 24.7971C21.7666 25.4956 18.2334 25.4956 14.8049 24.7971L14.5055 26.267C18.1316 27.0057 21.8684 27.0057 25.4945 26.267L25.1951 24.7971ZM25.5627 24.7223L25.1951 24.7971L25.4945 26.267L25.8621 26.1921L25.5627 24.7223ZM26.75 23.2604C26.75 23.9725 26.2511 24.582 25.5627 24.7223L25.8621 26.1921C27.2538 25.9085 28.25 24.6807 28.25 23.2604H26.75ZM25.9868 21.9553C26.4425 22.2103 26.75 22.6989 26.75 23.2604H28.25C28.25 22.1365 27.6319 21.157 26.7192 20.6463L25.9868 21.9553ZM25.4769 20.7077L25.453 20.8471L26.9315 21.1003L26.9554 20.9608L25.4769 20.7077ZM25.3163 17.0166C25.6323 18.2211 25.6871 19.4799 25.4769 20.7077L26.9554 20.9608C27.2017 19.5223 27.1375 18.0474 26.7672 16.6359L25.3163 17.0166ZM25.2539 16.7787L25.3163 17.0166L26.7672 16.6359L26.7047 16.398L25.2539 16.7787ZM21.7319 13.0205C23.4622 13.592 24.7887 15.0057 25.2539 16.7787L26.7047 16.398C26.1113 14.1359 24.4174 12.3278 22.2023 11.5962L21.7319 13.0205ZM18.3896 13.0157C19.474 12.6642 20.6528 12.6642 21.7319 13.0205L22.2023 11.5962C20.8171 11.1387 19.3114 11.14 17.9271 11.5888L18.3896 13.0157ZM26.7192 20.6463C26.8626 20.7265 26.966 20.8988 26.9315 21.1003L25.453 20.8471C25.3735 21.3114 25.6116 21.7453 25.9868 21.9553L26.7192 20.6463ZM13.0728 21.1255C13.0358 20.9092 13.1471 20.719 13.3088 20.6308L14.0271 21.9476C14.3967 21.746 14.6284 21.3228 14.5513 20.8723L13.0728 21.1255ZM19.25 11V12.0047H20.75V11H19.25ZM16.9253 26.6487C18.9687 26.8784 21.0313 26.8784 23.0747 26.6487L22.9071 25.1581C20.9751 25.3753 19.0249 25.3753 17.0929 25.1581L16.9253 26.6487ZM23.6285 26.6563C23.6849 26.4268 23.7218 26.1932 23.7389 25.9578L22.2429 25.849C22.2319 26.0003 22.2081 26.1506 22.1718 26.2981L23.6285 26.6563ZM23.5465 26.9896L23.6285 26.6563L22.1718 26.2981L22.0899 26.6315L23.5465 26.9896ZM20.8784 29.6464C22.1945 29.3315 23.223 28.3052 23.5465 26.9896L22.0899 26.6315C21.8997 27.4047 21.2965 28.004 20.5294 28.1875L20.8784 29.6464ZM19.1216 29.6464C19.6991 29.7845 20.3009 29.7845 20.8784 29.6464L20.5294 28.1875C20.1813 28.2708 19.8187 28.2708 19.4706 28.1875L19.1216 29.6464ZM16.4535 26.9896C16.777 28.3052 17.8055 29.3315 19.1216 29.6464L19.4706 28.1875C18.7035 28.004 18.1003 27.4047 17.9101 26.6315L16.4535 26.9896ZM16.3715 26.6563L16.4535 26.9896L17.9101 26.6315L17.8282 26.2981L16.3715 26.6563ZM16.2611 25.9578C16.2782 26.1932 16.3151 26.4268 16.3715 26.6563L17.8282 26.2981C17.7919 26.1506 17.7681 26.0003 17.7571 25.849L16.2611 25.9578Z" fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_160_568">
                                            <rect width="40" height="40" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <div class="notification-badge"></div>
                            <div class="notifications-container" id="notificationsContainer">
                                <div class="notifications-header">
                                    <h2>Notifications</h2>
                                    <span class="notifications-count">0 new notifications</span>
                                </div>
                                <div class="tabs">
                                    <div class="tab active" data-tab="all">All Notifications</div>
                                    <div class="tab" data-tab="unread">Unread</div>
                                </div>
                                <div class="tab-content active" id="all">
                                    <div class="notification-list"></div>
                                </div>
                                <div class="tab-content" id="unread">
                                    <div class="notification-list"></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="profile_menu">
                            <div class="profile_img" id="profileBtn">
                                <img src="<?php echo e(asset('images/profile.svg')); ?>" alt="Profile" id="profileDropdown">
                            </div>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="<?php echo e(route('business.manage-account.index')); ?>" class="menu-item manage-account">Manage Account</a>
                                <a href="<?php echo e(route('business.activity-log.index')); ?>" class="menu-item">Activity Log</a>
                                <a href="#" class="menu-item" onclick="showChangePasswordModal()">Change Password</a>
                                <a href="<?php echo e(route('business.reports.index')); ?>" class="menu-item">Reports</a>
                            <form method="POST" action="<?php echo e(route('business.logout')); ?>">
                                <?php echo csrf_field(); ?>
                                    <button type="submit" class="menu-item" style="background:none;border:none;text-align:left;width:100%">Log Out</button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Scripts -->
    <script src="<?php echo e(asset('dist/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('dist/bootstrap/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('dist/js/common.js')); ?>"></script>
    <script src="<?php echo e(asset('dist/js/globalFilter.js')); ?>"></script>
    <script src="<?php echo e(asset('js/custom-dropdowns.js')); ?>"></script>

    <div id="pageLoader" class="page-loader" style="display:none; align-items:center; justify-content:center; position:fixed; inset:0; background:rgba(0,0,0,0.25); z-index: 2000;">
        <div class="spinner-border text-light" role="status"></div>
    </div>

    <script>
        function showPageLoader(){
            var loader = document.getElementById('pageLoader');
            if(loader){ loader.style.display = 'flex'; }
        }

        function navigateWithRange(range){
            const url = new URL(window.location.href);
            url.searchParams.set('range', range);
            showPageLoader();
            window.location.href = url.toString();
        }
        // Date filter dropdown
        const filterButton = document.getElementById('globalRecordsFilterButton');
        const filterDropdown = document.getElementById('globalRecordsDropdown');
        if(filterButton && filterDropdown){
            filterButton.addEventListener('click', function(e){
                e.stopPropagation();
                filterDropdown.classList.toggle('show');
            });
            document.addEventListener('click', function(e){
                if(!filterDropdown.contains(e.target) && !filterButton.contains(e.target)){
                    filterDropdown.classList.remove('show');
                }
            });

            filterDropdown.querySelectorAll('.option').forEach(function(opt){
                opt.addEventListener('click', function(){
                    const range = this.getAttribute('data-range');
                    navigateWithRange(range);
                });
            });
        }

        // Notifications toggle
        <?php if($canAccessNotifications): ?>
        const trigger = document.querySelector('.notification-trigger');
        const container = document.querySelector('#notificationsContainer');
        if(trigger && container){
            trigger.addEventListener('click', function(e){
                e.stopPropagation();
                container.classList.toggle('show');
            });
            document.addEventListener('click', function(e){
                if(!container.contains(e.target) && !trigger.contains(e.target)){
                    container.classList.remove('show');
                }
            });
        }

        // Tab switching for notifications
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(tabName).classList.add('active');
            });
        });
        <?php endif; ?>

        // Profile dropdown
        const profileBtn = document.getElementById('profileBtn');
        const dropdown = document.getElementById('dropdownMenu');
        if(profileBtn && dropdown){
            profileBtn.addEventListener('click', function(e){
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
            document.addEventListener('click', function(){ 
                dropdown.classList.remove('show'); 
            });
        }

        // Fullscreen toggle
        const fullscreenBtn = document.getElementById('viewFullScreen');
        if(fullscreenBtn){
            fullscreenBtn.addEventListener('click', function(){
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            });
        }
    </script>
    
    <!-- Dashboard JS -->
    <script src="<?php echo e(asset('dist/js/dashboard/dashboard.js')); ?>"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
    
    <!-- Common Search Functionality -->
    <script>
    
    function performLiveSearch() {
        const searchTerm = document.getElementById('commonSearch').value.toLowerCase().trim();
        const currentPath = window.location.pathname;
        
        // Always use client-side search for live filtering
        let tableBody = null;
        let recordCountElement = null;
        
        if (currentPath.includes('/vehicles')) {
            tableBody = document.getElementById('vehicleTableBody');
            recordCountElement = document.querySelector('.record-count');
        } else if (currentPath.includes('/vendors')) {
            tableBody = document.getElementById('vendorTableBody');
            recordCountElement = document.querySelector('.record-count');
        } else if (currentPath.includes('/customers')) {
            tableBody = document.getElementById('customerTableBody');
            recordCountElement = document.querySelector('.record-count');
        }
        
        if (!tableBody) return;
        
        const rows = tableBody.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update record count
        if (recordCountElement) {
            if (searchTerm) {
                recordCountElement.textContent = `${visibleCount} Records Found (filtered from ${rows.length} total)`;
            } else {
                // Get original count from data attribute or calculate
                const originalCount = recordCountElement.getAttribute('data-original-count') || rows.length;
                recordCountElement.textContent = `${originalCount} Records Found`;
            }
        }
        
        // Hide/show pagination based on search results
        const pagination = document.querySelector('.pagination');
        if (pagination) {
            if (searchTerm && visibleCount === 0) {
                pagination.style.display = 'none';
            } else {
                pagination.style.display = '';
            }
        }
    }
    
    function clearSearch() {
        const searchInput = document.getElementById('commonSearch');
        if (searchInput) {
            searchInput.value = '';
            performLiveSearch(); // Always use client-side clear
        }
    }
    
    // Initialize search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('commonSearch');
        if (searchInput) {
            // Store original record count
            const recordCountElement = document.querySelector('.record-count');
            if (recordCountElement && !recordCountElement.getAttribute('data-original-count')) {
                const currentPath = window.location.pathname;
                if (currentPath.includes('/vehicles')) {
                    const tableBody = document.getElementById('vehicleTableBody');
                    if (tableBody) {
                        const rowCount = tableBody.querySelectorAll('tr').length;
                        recordCountElement.setAttribute('data-original-count', rowCount);
                    }
                }
            }
            
            // Live search works automatically on input - no button needed
            
            // Live search on input with debounce
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performLiveSearch();
                }, 200); // 200ms debounce for very responsive live search
            });
            
            // Clear search on Escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    clearSearch();
                }
            });
        }
    });
    </script>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="fas fa-key me-2"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <div class="invalid-feedback" id="current_password_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                            <div class="invalid-feedback" id="new_password_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            <div class="invalid-feedback" id="new_password_confirmation_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                            <span class="btn-text">Change Password</span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Changing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function showChangePasswordModal() {
        const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        modal.show();
    }

    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const changeBtn = document.getElementById('changePasswordBtn');
        const btnText = changeBtn.querySelector('.btn-text');
        const btnLoading = changeBtn.querySelector('.btn-loading');
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        
        // Show loading state
        changeBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-block';
        
        const formData = new FormData(this);
        
        fetch('<?php echo e(route("business.manage-account.change-password")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                document.getElementById('changePasswordModal').querySelector('.btn-close').click();
                document.getElementById('changePasswordForm').reset();
            } else {
                if (data.errors) {
                    // Display validation errors
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(field);
                        const errorDiv = document.getElementById(field + '_error');
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = data.errors[field][0];
                        }
                    });
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while changing password.');
        })
        .finally(() => {
            changeBtn.disabled = false;
            btnText.style.display = 'inline-block';
            btnLoading.style.display = 'none';
        });
    });
    </script>
</body>
</html><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/layouts/app.blade.php ENDPATH**/ ?>