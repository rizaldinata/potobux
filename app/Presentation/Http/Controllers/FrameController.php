<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Services\GetFramesService;
use App\Application\Services\UploadFrameService;
use Illuminate\Http\Request;

class FrameController extends Controller
{
    protected GetFramesService $getFramesService;
    protected UploadFrameService $uploadFrameService;

    public function __construct(GetFramesService $getFramesService, UploadFrameService $uploadFrameService)
    {
        $this->getFramesService = $getFramesService;
        $this->uploadFrameService = $uploadFrameService;
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

        $slots = null;
        if ($request->filled('slots')) {
            $slotsDecoded = json_decode($request->input('slots'), true);
            if (is_array($slotsDecoded)) {
                $slots = $slotsDecoded;
            }
        }

        $frame = $this->uploadFrameService->execute(
            $request->input('name'),
            $request->file('file'),
            $slots
        );

        return $this->successResponse($frame, 'Frame uploaded successfully.', 201);
    }
}
