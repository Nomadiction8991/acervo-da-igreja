<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

final class UserPermission extends Pivot
{
    protected $table = 'user_permissions';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'permission_id',
    ];
}
