<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\TroGiang;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho trợ giảng
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return redirect()->route('tro-giang.lop-hoc.index');
    }
} 