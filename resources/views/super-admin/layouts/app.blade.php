<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'The NexZen - Super Admin')</title>
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('dist/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
        <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-crown me-2"></i>
                            NEXZEN
                        </h4>
                        <small class="text-white-50">Super Admin</small>
            </div>
            
                    <ul class="nav flex-column">
                        <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" href="{{ route('super-admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                        </li>
                        <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('super-admin.businesses.*') ? 'active' : '' }}" href="{{ route('super-admin.businesses.index') }}">
                        <i class="fas fa-building"></i>
                        Businesses
                    </a>
                        </li>
                        <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('super-admin.bugs.*') ? 'active' : '' }}" href="{{ route('super-admin.bugs.index') }}">
                        <i class="fas fa-bug"></i>
                        Bug Tracking
                    </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navigation -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllCache()" id="clearCacheBtn">
                                <i class="fas fa-trash-alt me-1"></i>
                                Clear Cache
                </button>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="migrationDropdown" data-bs-toggle="dropdown">
                                    <i class="fas fa-database me-1"></i>
                                    Migrations
                    </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="getMigrationStatus()">
                                        <i class="fas fa-info-circle me-2"></i>Check Status
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="runMigrations()">
                                        <i class="fas fa-play me-2"></i>Run Migrations
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="syncMigrations()">
                                        <i class="fas fa-sync me-2"></i>Sync Existing Tables
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="rollbackMigrations()">
                                        <i class="fas fa-undo me-2"></i>Rollback Last Batch
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="resetMigrations()">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Reset All Migrations
                                    </a></li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download me-1"></i>
                                Export
                    </button>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                Admin
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('super-admin.dashboard') }}">
                                    <i class="fas fa-user-cog me-2"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('super-admin.bugs.index') }}">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('super-admin.logout') }}">
                                @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('dist/jquery.min.js') }}"></script>
    <script src="{{ asset('dist/bootstrap/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        function clearAllCache() {
            const btn = document.getElementById('clearCacheBtn');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Clearing...';
            btn.disabled = true;
            
            // Show confirmation dialog
            if (!confirm('Are you sure you want to clear all caches? This will clear:\n\n• Application Cache\n• Configuration Cache\n• Route Cache\n• View Cache\n• Compiled Views\n• Session Cache\n• Temporary Files\n\nThis action cannot be undone.')) {
                btn.innerHTML = originalText;
                btn.disabled = false;
                return;
            }
            
            // Make AJAX request to clear cache
            fetch('{{ route("super-admin.cache.clear") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', 'Cache cleared successfully!', data.message);
                    
                    // Show what was cleared
                    if (data.cleared_caches) {
                        const cacheList = data.cleared_caches.join(', ');
                        showAlert('info', 'Cleared caches:', cacheList);
                    }
                } else {
                    showAlert('error', 'Failed to clear cache', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'An error occurred while clearing cache');
            })
            .finally(() => {
                // Restore button state
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
        
        function showAlert(type, title, message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            
            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
            
            alertDiv.innerHTML = `
                <i class="fas fa-${icon} me-2"></i>
                <strong>${title}</strong><br>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert alert at the top of main content
            const mainContent = document.querySelector('.main-content');
            const firstChild = mainContent.firstElementChild;
            mainContent.insertBefore(alertDiv, firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Migration Management Functions
        function getMigrationStatus() {
            const btn = document.querySelector('[onclick="getMigrationStatus()"]');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking...';
            
            fetch('{{ route("super-admin.migrations.status") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const status = data.data;
                    let message = `Migration Status:\n\n`;
                    message += `• Total Migration Files: ${status.total_migration_files}\n`;
                    message += `• Ran Migrations: ${status.ran_migrations}\n`;
                    message += `• Pending Migrations: ${status.pending_migrations}\n`;
                    message += `• Migrations Table Exists: ${status.migrations_table_exists ? 'Yes' : 'No'}\n`;
                    
                    if (status.pending_migrations > 0) {
                        message += `\nPending Migrations:\n`;
                        status.pending_list.forEach(migration => {
                            message += `• ${migration}\n`;
                        });
                    }
                    
                    showAlert('info', 'Migration Status', message);
                } else {
                    showAlert('error', 'Failed to get migration status', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'An error occurred while checking migration status');
            })
            .finally(() => {
                // Restore button state
                btn.innerHTML = originalText;
            });
        }
        
        function runMigrations() {
            if (!confirm('Are you sure you want to run pending migrations? This will update your database structure.')) {
                return;
            }
            
            const btn = document.querySelector('[onclick="runMigrations()"]');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Running...';
            
            fetch('{{ route("super-admin.migrations.run") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = data.message;
                    if (data.data.migrations_run > 0) {
                        message += `\n\nMigrations Run: ${data.data.migrations_run}`;
                        if (data.data.ran_migrations) {
                            message += `\n\nRan Migrations:\n`;
                            data.data.ran_migrations.forEach(migration => {
                                message += `• ${migration}\n`;
                            });
                        }
                    }
                    showAlert('success', 'Migrations Completed', message);
                } else {
                    showAlert('error', 'Failed to run migrations', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'An error occurred while running migrations');
            })
            .finally(() => {
                // Restore button state
                btn.innerHTML = originalText;
            });
        }
        
        function syncMigrations() {
            if (!confirm('This will mark existing tables as migrated without running the migration code. Use this when tables already exist but aren\'t tracked in the migrations table.\n\nAre you sure you want to sync existing tables?')) {
                return;
            }
            
            const btn = document.querySelector('[onclick="syncMigrations()"]');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Syncing...';
            
            fetch('{{ route("super-admin.migrations.sync") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = data.message;
                    if (data.data.migrations_synced > 0) {
                        message += `\n\nSynced: ${data.data.migrations_synced} migration(s)`;
                        if (data.data.synced_migrations) {
                            message += `\n\nSynced Migrations:\n`;
                            data.data.synced_migrations.forEach(migration => {
                                message += `• ${migration}\n`;
                            });
                        }
                        if (data.data.remaining_pending && data.data.remaining_pending.length > 0) {
                            message += `\n\nRemaining Pending:\n`;
                            data.data.remaining_pending.forEach(migration => {
                                message += `• ${migration}\n`;
                            });
                        }
                    }
                    showAlert('success', 'Sync Completed', message);
                } else {
                    showAlert('error', 'Failed to sync migrations', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'An error occurred while syncing migrations');
            })
            .finally(() => {
                // Restore button state
                btn.innerHTML = originalText;
            });
        }
        
        function rollbackMigrations() {
            if (!confirm('Are you sure you want to rollback the last migration batch? This will undo recent database changes.')) {
                return;
            }
            
            const btn = document.querySelector('[onclick="rollbackMigrations()"]');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Rolling back...';
            
            fetch('{{ route("super-admin.migrations.rollback") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = data.message;
                    if (data.data.migrations_rolled_back > 0) {
                        message += `\n\nRolled Back: ${data.data.migrations_rolled_back} migration(s)`;
                        if (data.data.rolled_back_migrations) {
                            message += `\n\nRolled Back Migrations:\n`;
                            data.data.rolled_back_migrations.forEach(migration => {
                                message += `• ${migration}\n`;
                            });
                        }
                    }
                    showAlert('success', 'Rollback Completed', message);
                } else {
                    showAlert('error', 'Failed to rollback migrations', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'An error occurred while rolling back migrations');
            })
            .finally(() => {
                // Restore button state
                btn.innerHTML = originalText;
            });
        }
        
        function resetMigrations() {
            if (!confirm('⚠️ DANGER: This will reset ALL migrations (rollback all and re-run them).\n\nThis action will:\n• Drop all tables\n• Re-run all migrations\n• Potentially cause data loss\n\nAre you absolutely sure you want to continue?')) {
                return;
            }
            
            const btn = document.querySelector('[onclick="resetMigrations()"]');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
            
            fetch('{{ route("super-admin.migrations.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = data.message;
                    if (data.data.migrations_reset > 0) {
                        message += `\n\nReset: ${data.data.migrations_reset} migration(s)`;
                    }
                    showAlert('success', 'Migrations Reset', message);
                } else {
                    showAlert('error', 'Failed to reset migrations', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'An error occurred while resetting migrations');
            })
            .finally(() => {
                // Restore button state
                btn.innerHTML = originalText;
            });
        }
        
        // Add some CSS for better button styling
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                #clearCacheBtn:hover {
                    background-color: #dc3545 !important;
                    border-color: #dc3545 !important;
                    color: white !important;
                }
                
                #clearCacheBtn:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                }
                
                #migrationDropdown:hover {
                    background-color: #6f42c1 !important;
                    border-color: #6f42c1 !important;
                    color: white !important;
                }
                
                .dropdown-menu {
                    min-width: 200px;
                }
                
                .dropdown-item.text-danger:hover {
                    background-color: #dc3545 !important;
                    color: white !important;
                }
                
                .alert {
                    margin-bottom: 1rem;
                    border-radius: 8px;
                    white-space: pre-line;
                }
                
                .btn-group .btn {
                    margin-right: 5px;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    
    @stack('scripts')
</body>
</html>