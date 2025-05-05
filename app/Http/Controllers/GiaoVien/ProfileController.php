<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiaoVien;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Hiển thị thông tin cá nhân của giáo viên đăng nhập
     */
    public function index()
    {
        $user = Auth::user();
        $nguoiDung = NguoiDung::findOrFail($user->id);
        $giaoVien = GiaoVien::where('nguoi_dung_id', $user->id)->first();
        
        return view('giao-vien.profile.index', compact('nguoiDung', 'giaoVien'));
    }
    
    /**
     * Hiển thị form chỉnh sửa thông tin cá nhân
     */
    public function edit()
    {
        $user = Auth::user();
        $nguoiDung = NguoiDung::findOrFail($user->id);
        $giaoVien = GiaoVien::where('nguoi_dung_id', $user->id)->first();
        
        return view('giao-vien.profile.edit', compact('nguoiDung', 'giaoVien'));
    }
    
    /**
     * Cập nhật thông tin cá nhân
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'ho' => 'required|string|max:255',
            'ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:nguoi_dungs,email,'.$user->id,
            'so_dien_thoai' => 'required|string|max:20|unique:nguoi_dungs,so_dien_thoai,'.$user->id,
            'dia_chi' => 'nullable|string|max:255',
            'bang_cap' => 'nullable|string',
            'chuyen_mon' => 'nullable|string|max:255',
            'so_nam_kinh_nghiem' => 'nullable|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $nguoiDung = NguoiDung::findOrFail($user->id);
        $nguoiDung->ho = $request->ho;
        $nguoiDung->ten = $request->ten;
        $nguoiDung->email = $request->email;
        $nguoiDung->so_dien_thoai = $request->so_dien_thoai;
        $nguoiDung->dia_chi = $request->dia_chi;
        $nguoiDung->save();
        
        $giaoVien = GiaoVien::where('nguoi_dung_id', $user->id)->first();
        if ($giaoVien) {
            $giaoVien->bang_cap = $request->bang_cap;
            $giaoVien->chuyen_mon = $request->chuyen_mon;
            $giaoVien->so_nam_kinh_nghiem = $request->so_nam_kinh_nghiem;
            $giaoVien->save();
        }
        
        return redirect()->route('giao-vien.profile.index')->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    }
    
    /**
     * Hiển thị form đổi mật khẩu
     */
    public function showChangePasswordForm()
    {
        return view('giao-vien.profile.change-password');
    }
    
    /**
     * Cập nhật mật khẩu
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = Auth::user();
        $nguoiDung = NguoiDung::findOrFail($user->id);
        
        if (!Hash::check($request->current_password, $nguoiDung->mat_khau)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }
        
        $nguoiDung->mat_khau = Hash::make($request->password);
        $nguoiDung->save();
        
        return redirect()->route('giao-vien.profile.index')->with('success', 'Mật khẩu đã được cập nhật thành công.');
    }
} 