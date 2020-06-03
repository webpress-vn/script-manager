<?php

namespace VCComponent\Laravel\Script\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Script\Entities\Script;
use VCComponent\Laravel\Script\Repositories\ScriptRepository;

/**
 * Class AccountantRepositoryEloquent.
 */
class ScriptRepositoryEloquent extends BaseRepository implements ScriptRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Script::class;
    }

    public function getEntity()
    {
        return $this->model;
    }
    public function findById($id)
    {
        $script = $this->find($id);
        return $script;
    }
    public function updateStatus($request, $id)
    {
        $script = $this->find($id);

        $script->status = $request->input('status');
        $script->save();
    }

    public function updatePosition($request, $id)
    {
        $script = $this->find($id);

        $script->position = $request->input('position');
        $script->save();
    }

    public function bulkUpdateStatus($request)
    {
        $data = $request->all();

        $result = $this->whereIn("id", $request->id)->update(['status' => $data['status']]);

        return $result;
    }

    public function filter($request)
    {
        $filter = $this->when($request->has('status'), function ($query) use ($request) {
            return $query->where('status', $request->status);
        })->when($request->has('position'), function ($query) use ($request) {
            return $query->where('position', $request->position);
        });
        return $filter;

    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
