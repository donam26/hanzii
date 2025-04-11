<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use Illuminate\Http\Request;

class DangKyHocController extends Controller
{
    /**
     * Xác nhận đăng ký học
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function xacNhan($id)
    {
        try {
            $dangKyHoc = DangKyHoc::findOrFail($id);
            
            // Cập nhật trạng thái
            $dangKyHoc->update([
                'trang_thai' => 'da_xac_nhan'
            ]);
            
            return redirect()->back()
                    ->with('success', 'Đã xác nhận đăng ký học thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                    ->with('error', 'Không thể xác nhận đăng ký học. Lỗi: ' . $e->getMessage());
        }
    }
} 