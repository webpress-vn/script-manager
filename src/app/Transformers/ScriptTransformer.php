<?php

namespace VCComponent\Laravel\Script\Transformers;

use League\Fractal\TransformerAbstract;

class ScriptTransformer extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'         => (int) $model->id,
            'title'      => $model->title,
            'position'   => $model->position,
            'content'    => $model->content,
            'status'     => $model->status,
            'timestamps' => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }
}
