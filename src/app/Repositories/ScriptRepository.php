<?php

namespace VCComponent\Laravel\Script\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface Repository.
 */
interface ScriptRepository extends RepositoryInterface
{
    public function findById($id);
    public function updateStatus($request,$id);
    public function bulkUpdateStatus($request);
    public function filter($request);
    public function updatePosition($request, $id);
}

