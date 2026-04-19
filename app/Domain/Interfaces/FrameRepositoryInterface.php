<?php

namespace App\Domain\Interfaces;

interface FrameRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveFrames();
    public function getRecentFrames(int $limit = 4);
    public function getPopularFrames(int $limit = 4);
    public function incrementDownloadCount(int $id): void;
}