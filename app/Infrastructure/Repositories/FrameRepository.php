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
        return $this->model->with('user')->where('is_active', true)->orderBy('created_at', 'desc')->get();
    }

    public function getRecentFrames(int $limit = 4)
    {
        return $this->model->with('user')->where('is_active', true)->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function getPopularFrames(int $limit = 4)
    {
        return $this->model->with('user')->where('is_active', true)->orderBy('download_count', 'desc')->limit($limit)->get();
    }

    public function incrementDownloadCount(int $id): void
    {
        $this->model->where('id', $id)->increment('download_count');
    }
}