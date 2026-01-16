<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'parent_id',
        'class_group_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    public function child(): HasOne
    {
        return $this->hasOne(User::class, 'parent_id');
    }

   
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

   
    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }
}
