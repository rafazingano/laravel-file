<?php

namespace ConfrariaWeb\File\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'path',
        'name',
        'type',
        'size',
        'mime_type',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
