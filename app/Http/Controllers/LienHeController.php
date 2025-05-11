<?php

namespace App\Http\Controllers;

use App\Models\LienHe;
use App\Models\NguoiDung;
use App\Notifications\LienHeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LienHeController extends Controller
{
    /**
     * Hiển thị trang liên hệ
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('lien-he');
    }

    /**
     * Lưu thông tin liên hệ
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'chu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên của bạn',
            'email.required' => 'Vui lòng nhập địa chỉ email',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'chu_de.required' => 'Vui lòng nhập chủ đề',
            'noi_dung.required' => 'Vui lòng nhập nội dung tin nhắn',
        ]);

        // Tạo liên hệ mới
        $lienHe = LienHe::create([
            'ho_ten' => $validated['ho_ten'],
            'email' => $validated['email'],
            'chu_de' => $validated['chu_de'],
            'noi_dung' => $validated['noi_dung'],
            'trang_thai' => 'chua_doc',
        ]);

        Log::info('Đã tạo liên hệ mới với ID: ' . $lienHe->id);

        // Gửi thông báo cho admin
        try {
            $this->guiThongBaoChoAdmin($lienHe);
            Log::info('Đã gửi thông báo thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi thông báo: ' . $e->getMessage());
        }

        // Chuyển hướng về trang chủ với thông báo thành công
        return redirect()->route('welcome')->with('success', 'Cảm ơn bạn đã liên hệ với chúng tôi. Chúng tôi sẽ phản hồi trong thời gian sớm nhất!');
    }

    /**
     * Gửi thông báo cho tất cả admin
     *
     * @param  \App\Models\LienHe  $lienHe
     * @return void
     */
    private function guiThongBaoChoAdmin($lienHe)
    {
        // Logging chi tiết
        Log::info('Bắt đầu gửi thông báo cho admin về liên hệ ID: ' . $lienHe->id);
        
        // Kiểm tra cả hai loại vai trò (quan_tri và admin) để đảm bảo tìm đúng người dùng
        $admins = NguoiDung::whereHas('vaiTros', function($query) {
            $query->whereIn('ten', ['quan_tri', 'admin']);
        })->get();
        
        Log::info('Tìm thấy số lượng admin: ' . $admins->count());
        
        // Nếu không tìm thấy admin nào, kiểm tra xem có vấn đề với cấu hình vai trò không
        if ($admins->count() == 0) {
            Log::warning('Không tìm thấy người dùng admin nào. Kiểm tra bảng vai_tro và vai_tro_nguoi_dungs.');
            
            // Kiểm tra các vai trò hiện có
            $vaiTros = DB::table('vai_tros')->pluck('ten')->toArray();
            Log::info('Danh sách vai trò hiện có: ' . implode(', ', $vaiTros ?: ['Không có vai trò nào']));
            
            // Trường hợp không có admin, gửi thông báo cho tất cả người dùng
            $allUsers = NguoiDung::take(5)->get(); // Lấy 5 người dùng đầu tiên
            if ($allUsers->count() > 0) {
                Log::info('Không tìm thấy admin, gửi thông báo cho ' . $allUsers->count() . ' người dùng đầu tiên');
                foreach ($allUsers as $user) {
                    try {
                        Log::info('Thử gửi thông báo cho người dùng: ' . $user->email);
                        $user->notify(new LienHeNotification($lienHe));
                        Log::info('Đã gửi thông báo cho người dùng: ' . $user->email);
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi gửi thông báo cho người dùng ' . $user->id . ': ' . $e->getMessage());
                    }
                }
            }
            return;
        }
        
        // Gửi thông báo đến từng admin
        foreach ($admins as $admin) {
            try {
                Log::info('Đang gửi thông báo cho admin: ' . $admin->email);
                
                // Sử dụng hệ thống thông báo của Laravel 
                // Điều này tự động tạo bản ghi trong bảng notifications và gửi email
                $admin->notify(new LienHeNotification($lienHe));
                Log::info('Đã gửi thông báo cho admin: ' . $admin->email);
                
                // Kiểm tra xem thông báo đã được lưu vào bảng notifications chưa
                $notificationCount = DB::table('notifications')
                    ->where('notifiable_id', $admin->id)
                    ->where('notifiable_type', get_class($admin))
                    ->count();
                    
                Log::info('Số lượng thông báo đã lưu cho admin ' . $admin->id . ': ' . $notificationCount);
            } catch (\Exception $e) {
                Log::error('Lỗi khi gửi thông báo cho admin ' . $admin->id . ': ' . $e->getMessage());
                Log::error('Chi tiết lỗi: ' . $e->getTraceAsString());
            }
        }
    }

    /**
     * Hiển thị danh sách liên hệ cho admin
     *
     * @return \Illuminate\View\View
     */
    public function danhSach()
    {
        $lienHes = LienHe::orderBy('tao_luc', 'desc')->paginate(10);
        return view('admin.lien-he.index', compact('lienHes'));
    }

    /**
     * Hiển thị chi tiết liên hệ cho admin
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $lienHe = LienHe::findOrFail($id);
        
        // Nếu liên hệ chưa đọc, cập nhật trạng thái thành đã đọc
        if ($lienHe->trang_thai == 'chua_doc') {
            $lienHe->update(['trang_thai' => 'da_doc']);
        }
        
        return view('admin.lien-he.show', compact('lienHe'));
    }

    /**
     * Cập nhật trạng thái liên hệ
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function capNhatTrangThai($id)
    {
        $lienHe = LienHe::findOrFail($id);
        $lienHe->update(['trang_thai' => 'da_phan_hoi']);
        
        return redirect()->route('admin.lien-he.show', $id)->with('success', 'Đã cập nhật trạng thái thành công!');
    }

    /**
     * Hàm test gửi thông báo (chỉ dùng để kiểm tra)
     *
     * @return \Illuminate\Http\Response
     */
    public function testNotification()
    {
        try {
            // Tạo một liên hệ test
            $lienHe = LienHe::create([
                'ho_ten' => 'Người Test ' . now()->format('H:i:s'),
                'email' => 'test_' . now()->timestamp . '@example.com',
                'chu_de' => 'Thông báo test lúc ' . now()->format('H:i:s'),
                'noi_dung' => 'Đây là thông báo test được tạo lúc ' . now()->format('d/m/Y H:i:s'),
                'trang_thai' => 'chua_doc',
            ]);
            
            Log::info('Đã tạo liên hệ test ID: ' . $lienHe->id);
            
            // Gửi thông báo
            $this->guiThongBaoChoAdmin($lienHe);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã tạo và gửi thông báo test thành công!',
                'lien_he' => [
                    'id' => $lienHe->id,
                    'ho_ten' => $lienHe->ho_ten,
                    'chu_de' => $lienHe->chu_de,
                    'created_at' => $lienHe->tao_luc
                ],
                'check_route' => route('admin.lien-he.show', $lienHe->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi test thông báo: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi test thông báo: ' . $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Gửi email phản hồi cho người liên hệ
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResponse(Request $request, $id)
    {
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'subject.required' => 'Vui lòng nhập tiêu đề email',
            'message.required' => 'Vui lòng nhập nội dung phản hồi',
        ]);
        
        // Tìm thông tin liên hệ
        $lienHe = LienHe::findOrFail($id);
        
        try {
            // Gửi email
            Mail::send('emails.response', [
                'noiDungPhanHoi' => $validated['message'],
                'lienHe' => $lienHe
            ], function ($mail) use ($lienHe, $validated) {
                $mail->to($lienHe->email, $lienHe->ho_ten)
                    ->subject($validated['subject'])
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            // Cập nhật trạng thái liên hệ
            $lienHe->update(['trang_thai' => 'da_phan_hoi']);
            
            // Ghi log
            Log::info('Đã gửi email phản hồi cho liên hệ ID: ' . $lienHe->id . ', Email: ' . $lienHe->email);
            
            return redirect()->route('admin.lien-he.show', $id)
                ->with('success', 'Đã gửi email phản hồi thành công đến ' . $lienHe->email);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi email phản hồi: ' . $e->getMessage());
            
            return redirect()->route('admin.lien-he.show', $id)
                ->with('error', 'Không thể gửi email phản hồi: ' . $e->getMessage());
        }
    }
} 