<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuyenVaiTro extends Model
{
    use HasFactory;

    protected $table = 'quyen_vai_tros';

    protected $fillable = [
        'vai_tro_id',
        'quyen_id',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với vai trò
     */
    public function vaiTro(): BelongsTo
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }

    /**
     * Quan hệ với quyền
     */
    public function quyen(): BelongsTo
    {
        return $this->belongsTo(Quyen::class, 'quyen_id');
    }
}
