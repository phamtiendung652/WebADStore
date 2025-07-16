<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Transaction extends Model
{
    protected $guarded = [''];
    protected $table = 'transactions'; // Đảm bảo đúng tên bảng

    protected $fillable = [
        'tst_name',
        'tst_phone',
        'tst_address',
        'tst_email',
        'tst_note',
        'tst_user_id',
        'tst_total_money',
        'tst_type',
        'tst_status', // Đảm bảo có cột này và trong fillable
        // Thêm các cột khác nếu có
    ];

    const TYPE_COD = 1;
    const TYPE_ONLINE   = 2;

    const STATUS_DEFAULT = 1;
    const STATUS_PROCESS = 2;
    const STATUS_SUCCESS = 3;
    const STATUS_CANCEL = -1;

    protected $status = [
        '1' => [
            'class' => 'default',
            'name'  => 'Tiếp nhận'
        ],
        '2' => [
            'class' => 'info',
            'name'  => 'Đang vận chuyển'
        ],
        '3' => [
            'class' => 'success',
            'name'  => 'Hoàn thành'
        ],
        '-1' => [
            'class' => 'danger',
            'name'  => 'Đã Huỷ'
        ],
    ];

    public function getStatus()
    {
        return Arr::get($this->status, $this->tst_status, "[N\A]");
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tst_user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'tst_admin_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'od_transaction_id');
    }
}
