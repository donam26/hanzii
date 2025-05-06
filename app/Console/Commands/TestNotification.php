<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NguoiDung;
use App\Models\LienHe;
use App\Notifications\LienHeNotification;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi thông báo test về liên hệ mới';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            $users = NguoiDung::where('id', $userId)->get();
        } else {
            $users = NguoiDung::whereHas('vaiTros', function($query) {
                $query->where('ten', 'quan_tri');
            })->get();
        }

        if ($users->count() == 0) {
            $this->error('Không tìm thấy người dùng admin!');
            return 1;
        }

        // Tạo một liên hệ giả lập
        $lienHe = new LienHe([
            'ho_ten' => 'Người Test',
            'email' => 'test@example.com',
            'chu_de' => 'Thông báo thử nghiệm',
            'noi_dung' => 'Đây là nội dung thông báo thử nghiệm.',
            'trang_thai' => 'chua_doc'
        ]);

        // Lưu vào database
        $lienHe->save();

        // Gửi thông báo
        foreach ($users as $user) {
            $user->notify(new LienHeNotification($lienHe));
            $this->info("Đã gửi thông báo đến người dùng: {$user->ho} {$user->ten} (ID: {$user->id})");
        }

        $this->info('Đã gửi thông báo thành công!');
        return 0;
    }
}
