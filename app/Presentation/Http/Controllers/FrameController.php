<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Services\GetFramesService;
use App\Application\Services\UploadFrameService;
use App\Domain\Interfaces\FrameRepositoryInterface;
use Illuminate\Http\Request;

class FrameController extends Controller
{
    protected GetFramesService $getFramesService;
    protected UploadFrameService $uploadFrameService;
    protected FrameRepositoryInterface $frameRepo;

    public function __construct(
        GetFramesService $getFramesService, 
        UploadFrameService $uploadFrameService,
        FrameRepositoryInterface $frameRepo
    ) {
        $this->getFramesService = $getFramesService;
        $this->uploadFrameService = $uploadFrameService;
        $this->frameRepo = $frameRepo;
    }

    public function landing()
    {
        $popular = $this->frameRepo->getPopularFrames(4);
        $recent = $this->frameRepo->getRecentFrames(4);
        return view('landing', compact('popular', 'recent'));
    }

    public function gallery()
    {
        $frames = $this->frameRepo->getActiveFrames();
        return view('frames.index', compact('frames'));
    }

    public function studio()
    {
        return view('studio');
    }

    public function index()
    {
        $frames = $this->getFramesService->execute();
        return $this->successResponse($frames, 'Frames fetched successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|image|mimes:png|max:10240',
            'slots' => 'nullable|string',
        ]);

        $slots = $request->filled('slots') ? json_decode($request->input('slots'), true) : null;

        $frame = $this->uploadFrameService->execute(
            $request->input('name'),
            $request->file('file'),
            $slots
        );

        return $this->successResponse($frame, 'Frame uploaded successfully.', 201);
    }

    public function download(int $id)
    {
        $this->frameRepo->incrementDownloadCount($id);
        return $this->successResponse(null, 'Download count updated.');
    }
}