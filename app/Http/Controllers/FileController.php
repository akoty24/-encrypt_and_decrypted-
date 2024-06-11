<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EncryptionService;
use App\Http\Requests\EncryptFileRequest;
use App\Http\Requests\DecryptFileRequest;
use Exception;

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
        $path = $file->storeAs('uploads', $file->getClientOriginalName());

        return response()->json([
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'path' => $path,
        ]);
    }

    public function encrypt(EncryptFileRequest $request)
    {
        try {
            $filePath = $request->input('file_path');
            // $customPath = $request->input('custom_path') ?: 'encrypted';
            // $customFileName = $request->input('custom_file_name');
            $fileExtension = $request->input('file_extension');

            $encryptedPath = $this->encryptionService->encryptFile($filePath, $fileExtension);

            return response()->json(['encrypted_path' => $encryptedPath]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function decrypt(DecryptFileRequest $request)
    {
        try {
            $filePath = $request->input('file_path');
           // $customPath = $request->input('custom_path') ?: 'decrypted';
            //$customFileName = $request->input('custom_file_name');
            $fileExtension = $request->input('file_extension');

            $decryptedPath = $this->encryptionService->decryptFile($filePath, $fileExtension);

            return response()->json(['decrypted_path' => $decryptedPath]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
