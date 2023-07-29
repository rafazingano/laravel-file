<?php

namespace ConfrariaWeb\File\Traits;

use ConfrariaWeb\File\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait FileTrait
{

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
