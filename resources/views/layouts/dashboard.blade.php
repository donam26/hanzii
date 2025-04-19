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
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                <span>{{ auth()->user() ? auth()->user()->ho . ' ' . auth()->user()->ten : 'Người dùng' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div @click.away="open = false" x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    <i class="fas fa-bell mr-2"></i> Thông báo
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Thông tin cá nhân</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Cài đặt</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Đăng xuất</button>
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

    <!-- Extra Scripts -->
    @stack('scripts')
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html> 