<?php

namespace App\Modules\Uploads\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Uploads\Requests\UploadRequest;
use App\Modules\Uploads\Services\UploadService;
use Exception;

class UploadController extends Controller
{
    private UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index()
    {
        return view('upload');
    }

    public function process(UploadRequest $request)
    {
        try {
            $people = $this->uploadService->processUploadedFile($request->file('csv_file'));

            return response()->json([
                'success' => true,
                'people' => $people,
                'count' => count($people)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
