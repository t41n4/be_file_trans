<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\UuidTrait;

class files extends Model
{
    use HasApiTokens, HasFactory, Notifiable, UuidTrait;

    protected $table = 'files';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'file_signature',
        'file_link',
        'file_name',
        'user_id_from',
        'user_id_to',
        'time_upload',
        'aes_key',
        'aes_iv',
    ];
}
