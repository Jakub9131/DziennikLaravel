<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    use HasFactory;

    // Definiujemy nazwę tabeli, jeśli jest inna niż liczba mnoga class_groups
    protected $table = 'class_groups';

    // Pola, które można masowo wypełniać
    protected $fillable = ['name'];
}
