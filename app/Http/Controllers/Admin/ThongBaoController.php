<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    /**
     * Hiển thị danh sách thông báo
     */
    public function index()
    {
        return view('admin.thong-bao.index');
    }

    /**
     * Hiển thị form tạo thông báo mới
     */
    public function create()
    {
        return view('admin.thong-bao.create');
    }

    /**
     * Lưu thông báo mới
     */
    public function store(Request $request)
    {
        // Xử lý lưu thông báo mới
        return redirect()->route('admin.thong-bao.index')
            ->with('success', 'Tạo thông báo thành công');
    }

    /**
     * Hiển thị thông tin chi tiết thông báo
     */
    public function show($id)
    {
        return view('admin.thong-bao.show');
    }

    /**
     * Hiển thị form chỉnh sửa thông báo
     */
    public function edit($id)
    {
        return view('admin.thong-bao.edit');
    }

    /**
     * Cập nhật thông báo
     */
    public function update(Request $request, $id)
    {
        // Xử lý cập nhật thông báo
        return redirect()->route('admin.thong-bao.index')
            ->with('success', 'Cập nhật thông báo thành công');
    }

    /**
     * Xóa thông báo
     */
    public function destroy($id)
    {
        // Xử lý xóa thông báo
        return redirect()->route('admin.thong-bao.index')
            ->with('success', 'Xóa thông báo thành công');
    }
} 