<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:'.($request->input('maxSize', 10240))],
        ]);

        $disk = $request->input('disk', config('studio.uploads.disk', 'public'));
        $directory = $request->input('directory', config('studio.uploads.directory', 'studio-uploads'));

        $file = $request->file('file');
        $path = $file->store($directory, $disk);

        return response()->json([
            'path' => $path,
            'url' => Storage::disk($disk)->url($path),
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);
    }

    public function destroy(Request $request): Response
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        $disk = $request->input('disk', config('studio.uploads.disk', 'public'));
        $path = $request->input('path');

        Storage::disk($disk)->delete($path);

        return response()->noContent();
    }
}
