<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class File extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'src',
        'file_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (File $file) {
            Storage::delete($file->src);
        });
    }

    public static function checkNameExists($user_id, $file_name)
    {
        return File::query()
            ->whereRelation('accesses', 'fk_author', $user_id)
            ->where('name', $file_name)
            ->first();
    }

    public function accesses()
    {
        return $this->hasMany(FileAccess::class, 'fk_file');
    }
}
