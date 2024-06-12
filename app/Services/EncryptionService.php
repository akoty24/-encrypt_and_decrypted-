<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\File;

class EncryptionService
{
    private $encryptionKey;

    public function __construct()
    {
        $this->encryptionKey = 'your256bitkeyyour256bitkeyyour256bitkey12'; // 32 characters for 256-bit key
    }

    public function uploadFile($file, $chunkNumber, $chunkSize, $totalSize, $identifier, $filename, $totalChunks)
    {
        $chunkDir = storage_path('app/chunks');
        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0777, true);
        }
        $chunkFile = $chunkDir . '/' . $identifier . '.part' . $chunkNumber;

        $file->move($chunkDir, $chunkFile);

        $chunkFiles = glob($chunkDir . '/' . $identifier . '.part*');
        if (count($chunkFiles) == $totalChunks) {
            $finalPath = storage_path('app/uploads') . '/' . $filename;
            $final = fopen($finalPath, 'w');
            for ($i = 1; $i <= $totalChunks; $i++) {
                $part = fopen($chunkDir . '/' . $identifier . '.part' . $i, 'r');
                stream_copy_to_stream($part, $final);
                fclose($part);
            }
            fclose($final);
            array_map('unlink', $chunkFiles);

            return [
                'name' => $filename,
                'size' => $totalSize,
                'extension' => pathinfo($filename, PATHINFO_EXTENSION),
                'path' => $finalPath
            ];
        }

        return 'chunk uploaded';
    }

    public function encryptFile($filePath)
    {
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $encryptedFilePath = storage_path('app/encrypted/' . $fileName . '.' . $fileExtension);

        try {
            $data = File::get($filePath);
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt($data, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
            $encryptedData = base64_encode($iv . $encrypted);

            File::put($encryptedFilePath, $encryptedData);

            return ['message' => 'File encrypted successfully', 'path' => $encryptedFilePath];
        } catch (Exception $e) {
            throw new Exception('File encryption failed: ' . $e->getMessage());
        }
    }

    public function decryptFile($filePath)
    {
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $decryptedFilePath = storage_path('app/decrypted/' . $fileName . '.' . $fileExtension);

        try {
            $data = File::get($filePath);
            $data = base64_decode($data);
            $ivLength = openssl_cipher_iv_length('aes-256-cbc');
            $iv = substr($data, 0, $ivLength);
            $encryptedData = substr($data, $ivLength);

            $decrypted = openssl_decrypt($encryptedData, 'aes-256-cbc', $this->encryptionKey, 0, $iv);

            File::put($decryptedFilePath, $decrypted);

            return ['message' => 'File decrypted successfully', 'path' => $decryptedFilePath];
        } catch (Exception $e) {
            throw new Exception('File decryption failed: ' . $e->getMessage());
        }
    }
}
