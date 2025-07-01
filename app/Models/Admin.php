<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'username', 'role', 'password', 'image'
    ];

    /**
     * Kiểm tra xem admin có phải là admin chính thức hay không (có đầy đủ quyền)
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Kiểm tra xem admin có phải là phó tổng giám đốc hay không (chỉ có quyền xem)
     *
     * @return bool
     */
    public function isDeputy()
    {
        return $this->role === 'deputy';
    }

    /**
     * Kiểm tra xem admin có quyền thực hiện hành động hay không
     *
     * @return bool
     */
    public function canPerformAction()
    {
        return $this->isAdmin(); // Chỉ admin mới có quyền thực hiện hành động
    }
}
