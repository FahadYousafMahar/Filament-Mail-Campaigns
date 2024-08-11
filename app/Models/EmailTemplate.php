<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['server_id', 'template','heading_new','paragraph_new','heading_renew','paragraph_renew'];
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
