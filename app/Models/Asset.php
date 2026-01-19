<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        'updated_by',
    ];

    /**
     * Return the public URL of this asset
     */
    public function getPublicUrl(): string
    {
        return Storage::url($this->storage_path);
    }

    /**
     * Relationships
     */
    public function programs()
    {
        return $this->hasMany(Program::class, 'program_asset_id');
    }

    public function linkages()
    {
        return $this->hasMany(Linkage::class, 'logo_asset_id');
    }
}
