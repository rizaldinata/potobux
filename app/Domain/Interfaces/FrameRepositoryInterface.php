<?php

namespace App\Domain\Interfaces;

interface FrameRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveFrames();
}
