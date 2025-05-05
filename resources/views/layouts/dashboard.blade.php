<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Trung tâm Tiếng Trung') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Feather Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Thêm style cho component -->
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }
        
        .pagination li {
            margin: 0 2px;
        }
        
        .pagination li a, .pagination li span {
            display: inline-block;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            color: #4a5568;
            font-size: 0.875rem;
            text-decoration: none;
        }
        
        .pagination li.active span {
            background-color: #4299e1;
            color: white;
            border-color: #4299e1;
        }
        
        .pagination li.disabled span {
            color: #a0aec0;
            cursor: not-allowed;
        }
        
        .pagination li a:hover {
            background-color: #f7fafc;
        }
        
        /* Dropdown styling */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
        
        .dropdown-menu {
            position: absolute;
            right: 0;
            z-index: 1000;
            display: none;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            font-size: 0.875rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-menu-end {
            right: 0;
            left: auto;
        }
        
        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            color: #1e2125;
            background-color: #f8f9fa;
            text-decoration: none;
        }
        
        .dropdown-item.active, .dropdown-item:active {
            color: #fff;
            text-decoration: none;
            background-color: #0d6efd;
        }
        
        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid #e9ecef;
        }
        
        /* Avatar styling */
        .avatar {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-online::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 25%;
            height: 25%;
            border-radius: 50%;
            border: 2px solid #fff;
            background-color: #0abb87;
        }
        
        .avatar-sm {
            width: 36px;
            height: 36px;
        }
        
        .avatar-circle {
            border-radius: 50%;
        }
        
        .rounded-circle {
            border-radius: 50% !important;
        }
        
        /* Alignments */
        .d-flex {
            display: flex !important;
        }
        
        .align-items-center {
            align-items: center !important;
        }
        
        .ms-3 {
            margin-left: 1rem !important;
        }
        
        .mb-0 {
            margin-bottom: 0 !important;
        }
        
        /* Text styles */
        .text-muted {
            color: #6c757d !important;
        }
        
        .fs-6 {
            font-size: 0.875rem !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        /* Icon stuff */
        .fe {
            font-family: feather !important;
            font-style: normal;
            font-weight: 400;
            font-variant: normal;
            text-transform: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
        }
        
        .dropdown-item-icon {
            margin-right: 0.5rem;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div id="app" class="min-h-screen flex">
        <x-sidebar :active="$active ?? ''" :role="$role ?? ''"></x-sidebar>
        
        <div class="flex-1 ml-64">
            <header class="bg-white shadow-sm">
                <div class="px-4 py-3 flex justify-between items-center">
                    <h1 class="text-xl font-semibold">@yield('page-heading', 'Dashboard')</h1>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Dropdown thông báo -->
                        <div class="relative" x-data="notificationData()">
                            <button @click="toggleNotifications" class="relative p-1 text-gray-600 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="unreadCount > 0" x-transition class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full" x-text="unreadCount"></span>
                            </button>
                            
                            <div x-show="open" 
                                @click.away="open = false" 
                                x-transition:enter="transition ease-out duration-100" 
                                x-transition:enter-start="transform opacity-0 scale-95" 
                                x-transition:enter-end="transform opacity-100 scale-100" 
                                x-transition:leave="transition ease-in duration-75" 
                                x-transition:leave-start="transform opacity-100 scale-100" 
                                x-transition:leave-end="transform opacity-0 scale-95" 
                                class="absolute right-0 w-80 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                style="max-height: 400px; overflow-y: auto;">
                                
                                <div class="py-2 px-3 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="text-sm font-semibold text-gray-800">Thông báo</h3>
                                    <button x-show="unreadCount > 0" @click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800">
                                        Đánh dấu tất cả đã đọc
                                    </button>
                                </div>
                                
                                <div x-show="loading" class="py-4 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Đang tải...
                                </div>
                                
                                <div x-show="!loading && notifications.length === 0" class="py-4 text-center text-gray-500">
                                    Không có thông báo nào
                                </div>
                                
                                <template x-for="notification in notifications" :key="notification.id">
                                    <div @click="readNotification(notification)" 
                                        :class="{ 'bg-blue-50': !notification.da_doc }"
                                        class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-medium text-gray-900" x-text="notification.tieu_de"></p>
                                            <span x-show="!notification.da_doc" class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2" x-text="notification.noi_dung"></p>
                                        <p class="text-xs text-gray-500 mt-1" x-text="formatDateTime(notification.created_at)"></p>
                                    </div>
                                </template>
                                
                                <div x-show="hasMore" class="py-2 text-center border-t border-gray-100">
                                    <button @click="loadMore" class="text-xs text-blue-600 hover:text-blue-800">
                                        Xem thêm thông báo
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dropdown -->
                        <div class="dropdown">
                            <a href="#" class="avatar avatar-sm avatar-online dropdown-toggle" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                @if (session('avatar'))
                                    <img src="{{ session('avatar') }}" alt="avatar" class="avatar-img rounded-circle">
                                @else
                                    <img src="{{ asset('assets/img/avatars/placeholder.jpg') }}" alt="avatar" class="avatar-img rounded-circle">
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-item">
                                    <div class="d-flex align-items-center">
                                        @if (session('avatar'))
                                            <div class="avatar avatar-sm avatar-circle">
                                                <img src="{{ session('avatar') }}" alt="avatar" class="avatar-img">
                                            </div>
                                        @else
                                            <div class="avatar avatar-sm avatar-circle">
                                                <img src="{{ asset('assets/img/avatars/placeholder.jpg') }}" alt="avatar" class="avatar-img">
                                            </div>
                                        @endif
                                        <div class="ms-3">
                                            <h6 class="mb-0">
                                                @if (session('user_full_name'))
                                                    {{ session('user_full_name') }}
                                                @else
                                                    {{ session('ten_dang_nhap') ?? 'Người dùng' }}
                                                @endif
                                            </h6>
                                            <span class="text-muted fs-6">
                                                @switch(session('vai_tro'))
                                                    @case('quan_tri_vien')
                                                        Quản trị viên
                                                        @break
                                                    @case('giao_vien')
                                                        Giáo viên
                                                        @break
                                                    @case('tro_giang')
                                                        Trợ giảng
                                                        @break
                                                    @case('hoc_vien')
                                                        Học viên
                                                        @break
                                                    @default
                                                        {{ session('vai_tro') ?? '' }}
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <hr class="dropdown-divider">
                              
                                @php
                                    $profileRoute = '';
                                    if (session('vai_tro') == 'quan_tri_vien') {
                                        $profileRoute = route('admin.profile.index');
                                    } elseif (session('vai_tro') == 'giao_vien') {
                                        $profileRoute = route('giao-vien.profile.index');
                                    } elseif (session('vai_tro') == 'tro_giang') {
                                        $profileRoute = route('tro-giang.profile.index');
                                    } elseif (session('vai_tro') == 'hoc_vien') {
                                        $profileRoute = route('hoc-vien.profile.index');
                                    }
                                @endphp
                                
                                <a class="dropdown-item" href="{{ $profileRoute }}">
                                    <i class="fe fe-user dropdown-item-icon"></i> Thông tin cá nhân
                                </a>
                                
                                <hr class="dropdown-divider">
                                <a class="dropdown-item" href="{{ route('notifications.index') }}">
                                    <i class="fe fe-bell dropdown-item-icon"></i> Thông báo
                                </a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fe fe-log-out dropdown-item-icon"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js Notification Component -->
    <script>
        function notificationData() {
            return {
                open: false,
                loading: false,
                notifications: [],
                unreadCount: 0,
                page: 1,
                hasMore: false,
                
                init() {
                    this.loadNotifications();
                    this.updateUnreadCount();
                    
                    // Cập nhật số lượng thông báo chưa đọc mỗi 30 giây
                    setInterval(() => {
                        this.updateUnreadCount();
                    }, 30000);
                },
                
                toggleNotifications() {
                    this.open = !this.open;
                    if (this.open && this.notifications.length === 0) {
                        this.loadNotifications();
                    }
                },
                
                loadNotifications() {
                    this.loading = true;
                    
                    fetch(`/api/notifications?page=${this.page}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (this.page === 1) {
                                    this.notifications = data.data.data;
                                } else {
                                    this.notifications = [...this.notifications, ...data.data.data];
                                }
                                
                                this.hasMore = data.data.current_page < data.data.last_page;
                            }
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Lỗi khi tải thông báo:', error);
                            this.loading = false;
                        });
                },
                
                loadMore() {
                    this.page++;
                    this.loadNotifications();
                },
                
                updateUnreadCount() {
                    fetch('/api/notifications/unread-count')
                        .then(response => response.json())
                        .then(data => {
                            this.unreadCount = data.unread_count;
                        })
                        .catch(error => {
                            console.error('Lỗi khi tải số lượng thông báo chưa đọc:', error);
                        });
                },
                
                readNotification(notification) {
                    if (!notification.da_doc) {
                        fetch(`/api/notifications/${notification.id}/mark-as-read`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                notification.da_doc = true;
                                this.updateUnreadCount();
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi khi đánh dấu đã đọc:', error);
                        });
                    }
                    
                    // Chuyển hướng đến URL của thông báo nếu có
                    if (notification.url) {
                        window.location.href = notification.url;
                    }
                },
                
                markAllAsRead() {
                    fetch('/api/notifications/mark-all-read', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.notifications.forEach(notification => {
                                notification.da_doc = true;
                            });
                            this.unreadCount = 0;
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi đánh dấu tất cả đã đọc:', error);
                    });
                },
                
                formatDateTime(dateTime) {
                    const date = new Date(dateTime);
                    const now = new Date();
                    const diffInMs = now - date;
                    const diffInSecs = Math.floor(diffInMs / 1000);
                    const diffInMins = Math.floor(diffInSecs / 60);
                    const diffInHours = Math.floor(diffInMins / 60);
                    const diffInDays = Math.floor(diffInHours / 24);
                    
                    if (diffInSecs < 60) {
                        return 'Vừa xong';
                    } else if (diffInMins < 60) {
                        return `${diffInMins} phút trước`;
                    } else if (diffInHours < 24) {
                        return `${diffInHours} giờ trước`;
                    } else if (diffInDays < 7) {
                        return `${diffInDays} ngày trước`;
                    } else {
                        return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    }
                }
            };
        }
    </script>

    <!-- Dropdown Bootstrap -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý dropdown bootstrap
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    
                    // Đóng tất cả các dropdown khác
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                        }
                    });
                    
                    // Toggle dropdown hiện tại
                    dropdownMenu.classList.toggle('show');
                });
            });
            
            // Đóng dropdown khi click bên ngoài
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        });
    </script>

    <!-- Extra Scripts -->
    @stack('scripts')
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html> 