<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaiTapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách ID bài học
        $baiHocIds = DB::table('bai_hocs')->pluck('id')->toArray();
        
        // Chỉ tạo bài tập trắc nghiệm để đơn giản hóa
        foreach ($baiHocIds as $baiHocId) {
            // Số lượng bài tập mỗi bài học từ 1-2 bài tập
            $soLuongBaiTap = rand(1, 2);
            
            for ($i = 0; $i < $soLuongBaiTap; $i++) {
                // Chỉ tạo bài tập trắc nghiệm
                $loai = 'trac_nghiem';
                
                // Lấy thông tin bài học
                $baiHoc = DB::table('bai_hocs')->where('id', $baiHocId)->first();
                
                // Tạo tiêu đề bài tập dựa vào bài học
                $tieuDe = "Bài tập " . ($i + 1) . " - " . $baiHoc->tieu_de;
                
                // Nội dung bài tập
                $noiDung = "Nội dung bài tập " . ($i + 1) . " của bài học " . $baiHoc->tieu_de . ". Hãy hoàn thành bài tập này để củng cố kiến thức.";
                
                // Điểm tối đa của bài tập từ 5-20 điểm
                $diemToiDa = rand(5, 20);
                
                $baiTapId = DB::table('bai_taps')->insertGetId([
                    'bai_hoc_id' => $baiHocId,
                    'tieu_de' => $tieuDe,
                    'loai' => $loai,
                    'noi_dung' => $noiDung,
                    'diem_toi_da' => $diemToiDa,
                    'tao_luc' => now(),
                    'cap_nhat_luc' => now(),
                ]);
                
                // Tạo câu hỏi trắc nghiệm
                // Số lượng câu hỏi từ 3-5 câu
                $soCauHoi = rand(3, 5);
                
                for ($j = 0; $j < $soCauHoi; $j++) {
                    $cauHoiId = DB::table('cau_hoi_trac_nghiems')->insertGetId([
                        'bai_tap_id' => $baiTapId,
                        'noi_dung' => 'Câu hỏi ' . ($j + 1) . ' của bài tập ' . $tieuDe,
                        'giai_thich' => 'Giải thích cho câu hỏi ' . ($j + 1),
                        'tao_luc' => now(),
                        'cap_nhat_luc' => now(),
                    ]);
                    
                    // Tạo 4 lựa chọn cho mỗi câu hỏi
                    $luaChons = [];
                    $dapAnDung = rand(0, 3); // Vị trí đáp án đúng ngẫu nhiên
                    
                    for ($k = 0; $k < 4; $k++) {
                        $luaChons[] = [
                            'cau_hoi_id' => $cauHoiId,
                            'noi_dung_lua_chon' => 'Lựa chọn ' . ($k + 1) . ' của câu hỏi ' . ($j + 1),
                            'la_dap_an_dung' => ($k == $dapAnDung) ? 1 : 0,
                            'so_thu_tu' => $k + 1,
                            'tao_luc' => now(),
                            'cap_nhat_luc' => now(),
                        ];
                    }
                    
                    DB::table('lua_chon_cau_hois')->insert($luaChons);
                }
            }
        }
    }
} 