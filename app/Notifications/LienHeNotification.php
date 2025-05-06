<?php

namespace App\Notifications;

use App\Models\LienHe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LienHeNotification extends Notification
{
    use Queueable;

    protected $lienHe;

    /**
     * Khởi tạo thông báo mới.
     *
     * @param  LienHe  $lienHe
     * @return void
     */
    public function __construct(LienHe $lienHe)
    {
        $this->lienHe = $lienHe;
    }

    /**
     * Nhận các kênh gửi thông báo.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Lấy nội dung mail thông báo.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tin nhắn liên hệ mới từ ' . $this->lienHe->ho_ten)
            ->greeting('Xin chào!')
            ->line('Bạn vừa nhận được một tin nhắn liên hệ mới từ ' . $this->lienHe->ho_ten . '.')
            ->line('Chủ đề: ' . $this->lienHe->chu_de)
            ->line('Email: ' . $this->lienHe->email)
            ->action('Xem chi tiết', route('admin.lien-he.show', $this->lienHe->id))
            ->line('Cảm ơn bạn đã sử dụng hệ thống quản lý Trung tâm Tiếng Trung Hanzii!');
    }

    /**
     * Lấy thông tin lưu vào database.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'tieu_de' => 'Tin nhắn mới từ: ' . $this->lienHe->ho_ten,
            'noi_dung' => 'Chủ đề: ' . $this->lienHe->chu_de,
            'url' => route('admin.lien-he.show', $this->lienHe->id)
        ];
    }

    /**
     * Lấy thông tin thông báo dạng mảng.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->lienHe->id,
            'ho_ten' => $this->lienHe->ho_ten,
            'chu_de' => $this->lienHe->chu_de,
            'loai' => 'lien_he',
            'url' => route('admin.lien-he.show', $this->lienHe->id)
        ];
    }
} 