<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\NguoiDung;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Debug thông tin yêu cầu vai trò
        Log::debug('Middleware CheckRole: Yêu cầu vai trò: ' . implode(', ', $roles));
        
        // Kiểm tra xác thực bằng Laravel Auth
        if (Auth::check()) {
            $user = Auth::user();
            Log::debug('Middleware CheckRole: Đã đăng nhập với user_id=' . $user->id);
            
            // Nếu yêu cầu role là học viên, kiểm tra loại tài khoản
            if (in_array('hoc_vien', $roles) && $user->loai_tai_khoan === 'hoc_vien') {
                Log::debug('Middleware CheckRole: Cho phép truy cập với vai trò học viên');
                return $next($request);
            }
            
            // Kiểm tra vai trò (sử dụng phương thức hasRole mới)
            if ($user->hasRole($roles)) {
                $roleName = $user->vaiTro ? $user->vaiTro->ten : 'không xác định';
                Log::debug('Middleware CheckRole: Cho phép truy cập với vai trò ' . $roleName);
                return $next($request);
            }
        }
        
        // Kiểm tra xác thực bằng session custom
        elseif ($request->session()->has('nguoi_dung_id')) {
            // Lấy vai trò của người dùng từ session
            $nguoiDungId = $request->session()->get('nguoi_dung_id');
            $userRole = $request->session()->get('vai_tro');
            
            // Kiểm tra vai trò cụ thể (admin, giao_vien, tro_giang)
            if (in_array($userRole, $roles)) {
                Log::debug('Middleware CheckRole: Cho phép truy cập với vai trò ' . $userRole);
                return $next($request);
            }
        }

        Log::debug('Middleware CheckRole: Không có quyền truy cập, chuyển hướng về trang chủ');
        return redirect()->route('welcome')->with('error', 'Bạn không có quyền truy cập vào trang này.');
    }
}
