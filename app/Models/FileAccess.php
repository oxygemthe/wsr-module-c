<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileAccess extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'type',
        'fk_author',
        'fk_file'
    ];

    protected $with = [
        'author'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'fk_author');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'fk_file');
    }
}
