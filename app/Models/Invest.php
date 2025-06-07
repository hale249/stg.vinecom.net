<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Invest extends Model
{
    public function deposit()
    {
        return $this->hasOne(Deposit::class, 'invest_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', Status::INVEST_COMPLETED);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', Status::INVEST_CLOSED);
    }

    public function scopeTotalInvest($query)
    {
        return $query->sum('total_price');
    }

    public function scopeTotalEarn($query)
    {
        return $query->sum('total_earning');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn() => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == Status::INVEST_PENDING) {
            $html = '<span class="badge badge--status badge--warning">' . trans('Pending') . '</span>';
        } elseif ($this->status == Status::INVEST_COMPLETED) {
            $html = '<span class="badge badge--status badge--success">' . trans('Completed') . '</span>';
        } elseif ($this->status == Status::INVEST_ACCEPT) {
            $html = '<span class="badge badge--status badge--primary">' . trans('Accepted') . '</span>';
        } elseif ($this->status == Status::INVEST_RUNNING) {
            $html = '<span class="badge badge--status badge--info">' . trans('Running') . '</span>';
        } elseif ($this->status == Status::INVEST_CLOSED) {
            $html = '<span class="badge badge--status badge--dark">' . trans('Closed') . '</span>';
        } else {
            $html = '<span class="badge badge--status badge--danger">' . trans('Canceled') . '</span>';
        }
        return $html;
    }


    public function scopeRunning($query)
    {
        return $query->where('status', Status::INVEST_RUNNING);
    }

    public function paymentTypeBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->payment_type == Status::PAYMENT_WALLET) {
                $html = '<span class="badge badge--info">' . trans('Wallet') . '</span>';
            } else {
                $html = '<span class="badge badge--primary">' . trans('Online') . '</span>';
            }

            return $html;
        });
    }

    public function paymentStatusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->payment_status == Status::PAYMENT_SUCCESS) {
                $html = '<span class="badge badge--success">' . trans('Success') . '</span>';
            } else {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            }

            return $html;
        });
    }

    public function capitalBackBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->capital_back == Status::YES) {
                $html = '<span class="badge badge--success">' . trans('Yes') . '</span>';
            } else {
                $html = '<span class="badge badge--warning">' . trans('No') . '</span>';
            }

            return $html;
        });
    }

    public function isBackedBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->capital_status == Status::YES) {
                $html = '<span class="badge badge--primary">' . trans('Yes') . '</span>';
            } else {
                $html = '<span class="badge badge--dark">' . trans('No') . '</span>';
            }

            return $html;
        });
    }
}
