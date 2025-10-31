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
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay"></div>
    
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
            <div class="sidebar-nav-icon">
                <i class="fas fa-home"></i>
            </div>
            Dashboard
        </a>
        @endif

        @if($canAccessBookings)
        <a href="{{ route('business.bookings.index') }}" class="nav-link {{ request()->routeIs('business.bookings.*') ? 'menuActive' : '' }} data-link">
            <div class="sidebar-nav-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            Bookings
        </a>
        @endif

        @if($canAccessVehicles)
        <a href="{{ route('business.vehicles.index') }}" class="nav-link {{ request()->routeIs('business.vehicles.*') ? 'menuActive' : '' }} vehicle-link">
            <div class="sidebar-nav-icon">
                <i class="fas fa-car"></i>
            </div>
            Vehicles
        </a>
        @endif

        @if($canAccessVendors)
        <a href="{{ route('business.vendors.index') }}" class="nav-link {{ request()->routeIs('business.vendors.*') ? 'menuActive' : '' }} vendor-link">
            <div class="sidebar-nav-icon">
                <i class="fas fa-users"></i>
            </div>
            Vendors
        </a>
        @endif

        @if($canAccessCustomers)
        <a href="{{ route('business.customers.index') }}" class="nav-link {{ request()->routeIs('business.customers.*') ? 'menuActive' : '' }} customer-link">
            <div class="sidebar-nav-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            Customers
        </a>
        @endif

        @if($canAccessReports)
        <a href="{{ route('business.reports.index') }}" class="nav-link {{ request()->routeIs('business.reports*') ? 'menuActive' : '' }} data-link">
            <div class="sidebar-nav-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            Reports
        </a>
        @endif

        @if($canAccessNotifications)
        <a href="{{ route('business.notifications.index') }}" class="nav-link {{ request()->routeIs('business.notifications.*') ? 'menuActive' : '' }} notifications-link">
            <div class="sidebar-nav-icon">
                <i class="fas fa-bell"></i>
            </div>
            Notifications
        </a>
        @endif
        </div>

        <!-- Need Help Section in Sidebar -->
        <div class="sidebar-need-help" style="display: none;">
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
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
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
    @if($canAccessNotifications)
    <script src="{{ asset('dist/js/notificationContainer.js') }}"></script>
    @endif

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
    
    // Mobile Sidebar Toggle
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
    
    // Close sidebar when clicking outside on mobile
    document.querySelector('.sidebar-overlay').addEventListener('click', function() {
        toggleSidebar();
    });
    </script>
</body>
</html>