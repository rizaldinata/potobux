<?php

namespace App\Application\Services;

use App\Domain\Interfaces\FrameRepositoryInterface;

class GetFramesService
{
    protected FrameRepositoryInterface $frameRepository;

    public function __construct(FrameRepositoryInterface $frameRepository)
    {
        $this->frameRepository = $frameRepository;
    }

    public function execute()
    {
        return $this->frameRepository->getActiveFrames();
    }
}
