<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'The NexZen - Business Portal')</title>
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('dist/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dist/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/dashboard/dashboard.css') }}">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Racing+Sans+One&display=swap" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="{{ request()->routeIs('business.dashboard') ? 'business-dashboard-page' : '' }}{{ request()->routeIs('business.vehicles.index') ? ' vehicle-management-page' : '' }}{{ request()->routeIs('business.vendors.index') ? ' vendor-management-page' : '' }}{{ request()->routeIs('business.customers.index') ? ' customer-management-page' : '' }}">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="logo">
            <img src="{{ $business->logo ? asset('storage/' . $business->logo) : asset('images/mainLogo.svg') }}" alt="Logo" id="sidebarLogo">
            <h3>{{ $business->business_name ?? 'RENTCAR' }}</h3>
        </div>
        
        @php
            $business = auth('business_admin')->user()->business;
            $subscription = $business->subscriptions()->whereIn('status', ['active', 'trial'])->first();
            $canAccessVehicles = $subscription ? $subscription->canAccessModule('vehicles') : false;
            $canAccessBookings = $subscription ? $subscription->canAccessModule('bookings') : false;
            $canAccessCustomers = $subscription ? $subscription->canAccessModule('customers') : false;
            $canAccessVendors = $subscription ? $subscription->canAccessModule('vendors') : false;
            $canAccessReports = $subscription ? $subscription->canAccessModule('reports') : false;
            $canAccessNotifications = $subscription ? $subscription->canAccessModule('notifications') : false;
            $canAccessSubscription = true; // Always allow access to subscription management
        @endphp
        
        <div class="sidebar-nav-links">
        @if($subscription)
        <a href="{{ route('business.dashboard') }}" class="nav-link {{ request()->routeIs('business.dashboard') ? 'menuActive' : '' }} dashboard-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0.5" width="29" height="30" rx="8" fill="white" />
                <path d="M15.6619 10.4674C15.6183 10.4256 15.5603 10.4023 15.4999 10.4023C15.4395 10.4023 15.3815 10.4256 15.3379 10.4674L9.94434 15.6198C9.92143 15.6417 9.90321 15.668 9.89077 15.6972C9.87833 15.7263 9.87194 15.7577 9.87197 15.7894L9.87109 20.6252C9.87109 20.8738 9.96987 21.1123 10.1457 21.2881C10.3215 21.4639 10.56 21.5627 10.8086 21.5627H13.624C13.7483 21.5627 13.8676 21.5133 13.9555 21.4254C14.0434 21.3375 14.0928 21.2182 14.0928 21.0939V17.1095C14.0928 17.0474 14.1175 16.9878 14.1614 16.9438C14.2054 16.8999 14.265 16.8752 14.3271 16.8752H16.6709C16.7331 16.8752 16.7927 16.8999 16.8366 16.9438C16.8806 16.9878 16.9053 17.0474 16.9053 17.1095V21.0939C16.9053 21.2182 16.9547 21.3375 17.0426 21.4254C17.1305 21.5133 17.2497 21.5627 17.374 21.5627H20.1883C20.4369 21.5627 20.6754 21.4639 20.8512 21.2881C21.027 21.1123 21.1258 20.8738 21.1258 20.6252V15.7894C21.1258 15.7577 21.1194 15.7263 21.107 15.6972C21.0945 15.668 21.0763 15.6417 21.0534 15.6198L15.6619 10.4674Z" fill="#6B6ADE" />
                <path d="M22.3821 14.6528L20.1907 12.5563V9.375C20.1907 9.25068 20.1413 9.13145 20.0534 9.04354C19.9655 8.95564 19.8463 8.90625 19.722 8.90625H18.3157C18.1914 8.90625 18.0722 8.95564 17.9843 9.04354C17.8964 9.13145 17.847 9.25068 17.847 9.375V10.3125L16.1501 8.69004C15.9913 8.52949 15.7552 8.4375 15.5 8.4375C15.2457 8.4375 15.0102 8.52949 14.8514 8.69033L8.61993 14.6522C8.43771 14.828 8.41485 15.1172 8.58068 15.3076C8.62232 15.3557 8.6733 15.3948 8.73053 15.4225C8.78776 15.4502 8.85003 15.4661 8.91356 15.469C8.97709 15.4719 9.04054 15.4618 9.10006 15.4394C9.15958 15.417 9.21392 15.3827 9.25978 15.3387L15.3389 9.52969C15.3825 9.48796 15.4405 9.46468 15.5009 9.46468C15.5612 9.46468 15.6193 9.48796 15.6629 9.52969L21.7426 15.3387C21.8321 15.4246 21.9521 15.4714 22.0762 15.469C22.2002 15.4666 22.3183 15.4151 22.4044 15.3258C22.5843 15.1395 22.5693 14.8318 22.3821 14.6528Z" fill="#6B6ADE" />
            </svg>
                        Dashboard
                    </a>
        @endif

        @if($canAccessBookings)
        <a href="{{ route('business.bookings.index') }}" class="nav-link {{ request()->routeIs('business.bookings.*') ? 'menuActive' : '' }} data-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M11.0469 22.5312H10.1094C9.92289 22.5312 9.74405 22.4572 9.61219 22.3253C9.48033 22.1934 9.40625 22.0146 9.40625 21.8281V17.6094C9.40625 17.4229 9.48033 17.2441 9.61219 17.1122C9.74405 16.9803 9.92289 16.9062 10.1094 16.9062H11.0469C11.2334 16.9062 11.4122 16.9803 11.5441 17.1122C11.6759 17.2441 11.75 17.4229 11.75 17.6094V21.8281C11.75 22.0146 11.6759 22.1934 11.5441 22.3253C11.4122 22.4572 11.2334 22.5312 11.0469 22.5312V22.5312Z" fill="#6B6ADE" />
                <path d="M17.6094 22.5312H16.6719C16.4854 22.5312 16.3066 22.4572 16.1747 22.3253C16.0428 22.1934 15.9688 22.0146 15.9688 21.8281V14.7969C15.9688 14.6104 16.0428 14.4316 16.1747 14.2997C16.3066 14.1678 16.4854 14.0938 16.6719 14.0938H17.6094C17.7959 14.0938 17.9747 14.1678 18.1066 14.2997C18.2384 14.4316 18.3125 14.6104 18.3125 14.7969V21.8281C18.3125 22.0146 18.2384 22.1934 18.1066 22.3253C17.9747 22.4572 17.7959 22.5312 17.6094 22.5312V22.5312Z" fill="#6B6ADE" />
                <path d="M20.8906 22.5312H19.9531C19.7666 22.5312 19.5878 22.4572 19.4559 22.3253C19.3241 22.1934 19.25 22.0146 19.25 21.8281V11.5156C19.25 11.3291 19.3241 11.1503 19.4559 11.0184C19.5878 10.8866 19.7666 10.8125 19.9531 10.8125H20.8906C21.0771 10.8125 21.2559 10.8866 21.3878 11.0184C21.5197 11.1503 21.5937 11.3291 21.5937 11.5156V21.8281C21.5937 22.0146 21.5197 22.1934 21.3878 22.3253C21.2559 22.4572 21.0771 22.5312 20.8906 22.5312V22.5312Z" fill="#6B6ADE" />
                <path d="M14.3281 22.5312H13.3906C13.2041 22.5312 13.0253 22.4572 12.8934 22.3253C12.7616 22.1934 12.6875 22.0146 12.6875 21.8281V9.17187C12.6875 8.98539 12.7616 8.80655 12.8934 8.67469C13.0253 8.54283 13.2041 8.46875 13.3906 8.46875H14.3281C14.5146 8.46875 14.6934 8.54283 14.8253 8.67469C14.9572 8.80655 15.0312 8.98539 15.0312 9.17187V21.8281C15.0312 22.0146 14.9572 22.1934 14.8253 22.3253C14.6934 22.4572 14.5146 22.5312 14.3281 22.5312V22.5312Z" fill="#6B6ADE" />
            </svg>
            Bookings
        </a>
        @endif

        @if($canAccessVehicles)
        <a href="{{ route('business.vehicles.index') }}" class="nav-link {{ request()->routeIs('business.vehicles.*') ? 'menuActive' : '' }} vehicle-link">
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
        @endif

        @if($canAccessVendors)
        <a href="{{ route('business.vendors.index') }}" class="nav-link {{ request()->routeIs('business.vendors.*') ? 'menuActive' : '' }} vendor-link">
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
        @endif

        @if($canAccessCustomers)
        <a href="{{ route('business.customers.index') }}" class="nav-link {{ request()->routeIs('business.customers.*') ? 'menuActive' : '' }} customer-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M16.7453 9.39199C16.1752 8.77646 15.3789 8.4375 14.5 8.4375C13.6164 8.4375 12.8175 8.77441 12.25 9.38613C11.6764 10.0046 11.3969 10.8451 11.4625 11.7527C11.5926 13.5434 12.9552 15 14.5 15C16.0448 15 17.4051 13.5437 17.5372 11.7533C17.6037 10.8539 17.3225 10.0151 16.7453 9.39199Z" fill="#6B6ADE" />
                <path d="M19.6564 21.5625H9.34392C9.20894 21.5643 9.07526 21.5359 8.95262 21.4795C8.82997 21.4231 8.72145 21.34 8.63493 21.2364C8.4445 21.0088 8.36775 20.6979 8.42458 20.3836C8.67185 19.0119 9.44353 17.8597 10.6564 17.0508C11.734 16.3327 13.0989 15.9375 14.5002 15.9375C15.9014 15.9375 17.2664 16.333 18.3439 17.0508C19.5568 17.8594 20.3285 19.0116 20.5757 20.3833C20.6326 20.6977 20.5558 21.0085 20.3654 21.2361C20.2789 21.3398 20.1704 21.4229 20.0478 21.4794C19.9251 21.5358 19.7914 21.5642 19.6564 21.5625Z" fill="#6B6ADE" />
            </svg>
                        Customers
                    </a>
        @endif

        @if($canAccessReports)
        <a href="{{ route('business.reports.index') }}" class="nav-link {{ request()->routeIs('business.reports*') ? 'menuActive' : '' }} data-link">
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
        @endif

        @if($canAccessNotifications)
        <a href="{{ route('business.notifications.index') }}" class="nav-link {{ request()->routeIs('business.notifications.*') ? 'menuActive' : '' }} notifications-link">
            <svg width="20" height="20" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M15 2.5C10.8575 2.5 7.5 5.8575 7.5 10V12.5L5 15V17.5H25V15L22.5 12.5V10C22.5 5.8575 19.1425 2.5 15 2.5ZM15 20C13.625 20 12.5 18.875 12.5 17.5H17.5C17.5 18.875 16.375 20 15 20Z" fill="#6B6ADE" />
            </svg>
            Notifications
        </a>
        @endif
        </div>

        <!-- Need Help Section in Sidebar -->
        <div class="sidebar-need-help">
            <div class="need-help-icon-sidebar">
                <i class="fas fa-folder"></i>
                <div class="need-help-docs-sidebar">
                    <i class="fas fa-file-alt"></i>
                    <i class="fas fa-file-pdf"></i>
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <h3 class="need-help-title-sidebar">Need help?</h3>
            <p class="need-help-subtitle-sidebar">Please check our docs</p>
            <button class="btn btn-documentation-sidebar">
                DOCUMENTATION
            </button>
        </div>
        </nav>

    <!-- Header -->
    <header class="header">
        <div class="row w-100">
            <div class="col-md-6">
                <div class="head_lddeft">
                    <div class="header_tdditle">
                        <h1>@yield('page-title', 'Dashboard')</h1>
                    </div>
    </div>
            </div>
            <div class="col-md-6 profileCol">
                <div class="head_right">
                    <div class="profile">
                        @if(request()->routeIs('business.dashboard'))
                        <div class="select-container" id="globalRecordsFilterContainer">
                            <button class="select-button" id="globalRecordsFilterButton">{{ $currentRangeLabel ?? 'Last 7 days' }}</button>
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
                        @endif
                        <div class="scan header-icon" id="viewFullScreen">
                            <i class="fas fa-expand"></i>
                        </div>
                        @if($canAccessNotifications)
                        <div class="notify header-icon">
                            <span class="notification-trigger">
                                <i class="fas fa-bell"></i>
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
                        @endif
                        <div class="profile_menu header-icon">
                            <div class="profile_img" id="profileBtn">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="dropdown-menu profile-dropdown-menu" id="dropdownMenu">
                                <a href="{{ route('business.manage-account.index') }}" class="menu-item manage-account">Manage Account</a>
                                <a href="{{ route('business.activity-log.index') }}" class="menu-item">Activity Log</a>
                                <a href="#" class="menu-item" onclick="showChangePasswordModal()">Change Password</a>
                                <a href="{{ route('business.reports.index') }}" class="menu-item">Reports</a>
                            <form method="POST" action="{{ route('business.logout') }}">
                                @csrf
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
    </main>

    <!-- Scripts -->
    <script src="{{ asset('dist/jquery.min.js') }}"></script>
    <script src="{{ asset('dist/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/common.js') }}"></script>
    <script src="{{ asset('dist/js/globalFilter.js') }}"></script>
    <script src="{{ asset('js/custom-dropdowns.js') }}"></script>

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
        @if($canAccessNotifications)
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
        @endif

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
    <script src="{{ asset('dist/js/dashboard/dashboard.js') }}"></script>
    
    @stack('scripts')

    <!-- Change Password Modal -->

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
        
        fetch('{{ route("business.manage-account.change-password") }}', {
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
</html>