<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaiLieuController extends Controller
{
    /**
     * Hiển thị danh sách tài liệu
     */
    public function index()
    {
        return view('admin.tai-lieu.index');
    }

    /**
     * Hiển thị form tạo tài liệu mới
     */
    public function create()
    {
        return view('admin.tai-lieu.create');
    }

    /**
     * Lưu tài liệu mới
     */
    public function store(Request $request)
    {
        // Xử lý lưu tài liệu mới
        return redirect()->route('admin.tai-lieu.index')
            ->with('success', 'Tạo tài liệu thành công');
    }

    /**
     * Hiển thị thông tin chi tiết tài liệu
     */
    public function show($id)
    {
        return view('admin.tai-lieu.show');
    }

    /**
     * Hiển thị form chỉnh sửa tài liệu
     */
    public function edit($id)
    {
        return view('admin.tai-lieu.edit');
    }

    /**
     * Cập nhật tài liệu
     */
    public function update(Request $request, $id)
    {
        // Xử lý cập nhật tài liệu
        return redirect()->route('admin.tai-lieu.index')
            ->with('success', 'Cập nhật tài liệu thành công');
    }

    /**
     * Xóa tài liệu
     */
    public function destroy($id)
    {
        // Xử lý xóa tài liệu
        return redirect()->route('admin.tai-lieu.index')
            ->with('success', 'Xóa tài liệu thành công');
    }
} 