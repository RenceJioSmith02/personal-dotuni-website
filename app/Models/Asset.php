<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kind',
        'file_name',
        'storage_path',
        'mime_type',
        'file_size_kb',
        'alt_text',
        'uploaded_by',
        'updated_by'
    ];

    public function programs()
    {
        return $this->hasMany(Program::class, 'program_asset_id');
    }
}
