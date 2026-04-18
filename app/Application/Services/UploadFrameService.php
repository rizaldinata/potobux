<?php

namespace App\Application\Services;

use App\Domain\Interfaces\FrameRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadFrameService
{
    protected FrameRepositoryInterface $frameRepository;

    public function __construct(FrameRepositoryInterface $frameRepository)
    {
        $this->frameRepository = $frameRepository;
    }

    public function execute(string $name, UploadedFile $file, ?array $slots = null)
    {
        $directory = 'frames';
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs($directory, $filename, 'public');

        return $this->frameRepository->create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'name' => $name,
            'image_path' => '/storage/' . $path,
            'slots' => $slots,
            'is_active' => true,
        ]);
    }
}
