<?php

namespace VCComponent\Laravel\Script\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use VCComponent\Laravel\Script\Events\ScriptCreatedByAdminEvent;
use VCComponent\Laravel\Script\Events\ScriptDeletedByAdminEvent;
use VCComponent\Laravel\Script\Events\ScriptUpdatedByAdminEvent;
use VCComponent\Laravel\Script\Repositories\ScriptRepository;
use VCComponent\Laravel\Script\Transformers\ScriptTransformer;
use VCComponent\Laravel\Script\Validators\ScriptValidator;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;

class ScriptController extends ApiController
{
    protected $repository;
    protected $validator;
    public function __construct(ScriptRepository $repository, ScriptValidator $validator)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();
        $this->validator  = $validator;
        if (config('script.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('script.auth_middleware.admin.middleware'),
                ['except' => config('script.auth_middleware.admin.except')]
            );
        }

        $this->transformer = ScriptTransformer::class;
    }

    public function hasPosition($request, $query)
    {
        if ($request->has('position')) {
            $query = $query->where('position', $request->position);

        }
        return $query;

    }
    public function hasStatus($request, $query)
    {
        if ($request->has('status')) {
            $query = $query->where('status', $request->status);
        }
        return $query;

    }

    public function index(Request $request)
    {
        $query = $this->entity;

        // dd($query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['title'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $query = $this->hasStatus($request, $query);
        $query = $this->hasPosition($request, $query);

        $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $scripts  = $query->paginate($per_page);

        return $this->response->paginator($scripts, new $this->transformer());
    }

    function list(Request $request) {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['title'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $scripts = $query->get();

        return $this->response->collection($scripts, new $this->transformer());
    }

    public function store(Request $request)
    {
        $this->validator->isValid($request, 'RULE_CREATE');
        $data   = $request->all();
        $script = $this->repository->create($data);

        event(new ScriptCreatedByAdminEvent($script));

        return $this->response->item($script, new $this->transformer());
    }
    public function show($id)
    {
        $script = $this->entity->find($id);

        if (!$script) {
            throw new NotFoundException('script');
        }

        $script = $this->repository->findById($id);
        return $this->response->item($script, new $this->transformer());
    }

    public function update(Request $request, $id)
    {
        $this->validator->isValid($request, 'RULE_UPDATE');
        $data   = $request->all();
        $script = $this->repository->update($data, $id);

        event(new ScriptUpdatedByAdminEvent($script));

        return $this->response->item($script, new $this->transformer());
    }

    public function destroy($id)
    {
        $script = $this->entity->find($id);
        if (!$script) {
            throw new NotFoundException('script');
        }

        $this->repository->delete($id);

        event(new ScriptDeletedByAdminEvent($script));

        return $this->success();
    }

    public function bulkUpdateStatus(Request $request)
    {
        $scripts = $this->entity->whereIn("id", $request->id)->get();

        if (count($request->id) > $scripts->count()) {
            throw new NotFoundException("script");
        }
        $this->repository->bulkUpdateStatus($request);
        return $this->success();
    }

    public function updateStatus(Request $request, $id)
    {
        $script = $this->entity->find($id);

        if (!$script) {
            throw new NotFoundException('script');
        }

        $this->repository->updateStatus($request, $id);

        return $this->success();
    }

    public function updatePosition(Request $request, $id)
    {
        $script = $this->entity->find($id);
        if (!$script) {
            throw new NotFoundException('script');
        }
        $this->repository->updatePosition($request, $id);

        return $this->success();
    }
}
