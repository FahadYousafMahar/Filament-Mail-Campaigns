<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'name',
        'url',
    ];
    public function emailTemplate()
    {
        return $this->hasOne(EmailTemplate::class);
    }
}
