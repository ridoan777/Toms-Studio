<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait AttachFileUrlTrait
{
    // Define the property dynamically on the model
    protected $appends = [];

    // When the model is converting to JSON, append *_url automatically
    public function toArray()
    {
        $array = parent::toArray();

        // Get columns to transform
        $columns = property_exists($this, 'imageColumns') ? $this->imageColumns : [];

        foreach ($columns as $column) {
            $key = "{$column}_url";
            $array[$key] = $this->{$column} ? Storage::url($this->{$column}) : null;
        }

        return $array;
    }
}
