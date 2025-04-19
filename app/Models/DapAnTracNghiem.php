<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DapAnTracNghiem extends Model
{
    use HasFactory;

    protected $table = 'dap_an_trac_nghiems';

    protected $fillable = [
        'ket_qua_id',
        'cau_hoi_id',
        'lua_chon_da_chon_id',
        'la_dap_an_dung',
    ];

    protected $casts = [
        'la_dap_an_dung' => 'boolean',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

}
