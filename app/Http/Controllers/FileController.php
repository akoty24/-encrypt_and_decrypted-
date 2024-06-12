<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EncryptionService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    protected $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function index()
    {
        return view('index');
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $chunkNumber = $request->input('resumableChunkNumber');
        $chunkSize = $request->input('resumableChunkSize');
        $totalSize = $request->input('resumableTotalSize');
        $identifier = $request->input('resumableIdentifier');
        $filename = $request->input('resumableFilename');
        $totalChunks = $request->input('resumableTotalChunks');

        try {
            $result = $this->encryptionService->uploadFile($file, $chunkNumber, $chunkSize, $totalSize, $identifier, $filename, $totalChunks);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['message' => 'File upload failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function encrypt(Request $request)
    {
        $filePath = $request->input('path');

        try {
            $result = $this->encryptionService->encryptFile($filePath);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['message' => 'File encryption failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function decrypt(Request $request)
    {
        $filePath = $request->input('path');

        try {
            $result = $this->encryptionService->decryptFile($filePath);
        
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['message' => 'File decryption failed', 'error' => $e->getMessage()], 500);
        }
    }
      public function downloadEncrypted($filename)
    {
        $path = storage_path('app/encrypted/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
    public function downloadDecrypted($filename)
    {
        $path = storage_path('app/decrypted/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
    
}
