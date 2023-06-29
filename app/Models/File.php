<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public static function checkNameExists($user_id, $file_name)
    {
        return File::query()
            ->whereRelation('accesses', 'fk_author', $user_id)
            ->where('name', $file_name)
            ->first();
    }

    public static function toName($user_id, $file)
    {
        $file_name = $file->getClientOriginalName();
        $check_name_exists = File::checkNameExists($user_id, $file_name);
        for ($count = 1; $check_name_exists; $count++) {
            $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                . " ($count)." . $file->getClientOriginalExtension();
            $check_name_exists = File::checkNameExists($user_id, $file_name);
        }
        return $file_name;
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'fk_author');
    }

    public function accesses()
    {
        return $this->hasMany(FileAccess::class, 'fk_file');
    }
}
