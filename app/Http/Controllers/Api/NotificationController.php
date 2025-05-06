<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\NguoiDung;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo của người dùng đang đăng nhập
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Kiểm tra đăng nhập bằng session
        if ($request->session()->has('nguoi_dung_id')) {
            $userId = $request->session()->get('nguoi_dung_id');
            $user = NguoiDung::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 404);
            }
            
            $notifications = DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        }
        
        // Kiểm tra bằng Auth nếu không có session
        $authUser = Auth::user();
        if ($authUser) {
            $notifications = DatabaseNotification::where('notifiable_id', $authUser->id)
                ->where('notifiable_type', get_class($authUser))
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Người dùng chưa đăng nhập'
        ], 401);
    }

    /**
     * Lấy số lượng thông báo chưa đọc
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unreadCount(Request $request)
    {
        // Kiểm tra đăng nhập bằng session
        if ($request->session()->has('nguoi_dung_id')) {
            $userId = $request->session()->get('nguoi_dung_id');
            $user = NguoiDung::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 404);
            }
            
            $unreadCount = DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
        }
        
        // Kiểm tra bằng Auth nếu không có session
        $authUser = Auth::user();
        if ($authUser) {
            $unreadCount = DatabaseNotification::where('notifiable_id', $authUser->id)
                ->where('notifiable_type', get_class($authUser))
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Người dùng chưa đăng nhập'
        ], 401);
    }

    /**
     * Đánh dấu thông báo đã đọc
     *
     * @param  string  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id, Request $request)
    {
        $userId = null;
        
        // Kiểm tra đăng nhập bằng session
        if ($request->session()->has('nguoi_dung_id')) {
            $userId = $request->session()->get('nguoi_dung_id');
        } else if (Auth::check()) {
            $userId = Auth::id();
        }
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng chưa đăng nhập'
            ], 401);
        }
        
        $notification = DatabaseNotification::find($id);

        if ($notification && $notification->notifiable_id == $userId) {
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu thông báo là đã đọc'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy thông báo'
        ], 404);
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead(Request $request)
    {
        // Kiểm tra đăng nhập bằng session
        if ($request->session()->has('nguoi_dung_id')) {
            $userId = $request->session()->get('nguoi_dung_id');
            $user = NguoiDung::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 404);
            }
            
            DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu tất cả thông báo là đã đọc'
            ]);
        }
        
        // Kiểm tra bằng Auth nếu không có session
        $authUser = Auth::user();
        if ($authUser) {
            DatabaseNotification::where('notifiable_id', $authUser->id)
                ->where('notifiable_type', get_class($authUser))
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu tất cả thông báo là đã đọc'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Người dùng chưa đăng nhập'
        ], 401);
    }
}
