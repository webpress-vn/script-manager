<?php

namespace VCComponent\Laravel\Script\Entities;

use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    protected $fillable = [
        'title',
        'position',
        'content',
        'status',
    ];

    public static function get_Script($position)
    {
        $scripts = self::where('position', $position)->where('status', 1)->get();
        $data = [];

        if ($scripts) {
            foreach ($scripts as $script) {
                $value = $script->content;
                array_push($data, $value);
            }

            return implode(' ', array_merge($data));
        }

        return '';
    }
}
