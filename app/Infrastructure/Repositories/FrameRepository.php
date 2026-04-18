<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\FrameRepositoryInterface;
use App\Domain\Models\Frame;

class FrameRepository extends BaseRepository implements FrameRepositoryInterface
{
    public function __construct(Frame $model)
    {
        parent::__construct($model);
    }

    public function getActiveFrames()
    {
        return $this->model->where('is_active', true)->orderBy('created_at', 'desc')->get();
    }
}
