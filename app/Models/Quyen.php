<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quyen extends Model
{
    use HasFactory;

    protected $table = 'quyens';

    protected $fillable = [
        'ten',
        'ma_quyen',
        'mo_ta',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với vai trò
     */
    public function vaiTros(): BelongsToMany
    {
        return $this->belongsToMany(VaiTro::class, 'quyen_vai_tros', 'quyen_id', 'vai_tro_id')
            ->withTimestamps('tao_luc', 'cap_nhat_luc');
    }
}
