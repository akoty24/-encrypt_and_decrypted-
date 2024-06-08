<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

class EncryptionService
{
    private $encryptionKey = 'your256bitkeyyour256bitkeyyour256bitkey12'; // 32 characters for 256-bit key

    public function encryptFile($filePath, $customPath, $customFileName, $fileExtension)
    {
        if (empty($filePath)) {
            throw new Exception('File path is missing.');
        }

        if (!$customFileName) {
            throw new Exception('Custom file name is missing.');
        }

        $encryptedFileName = $customFileName . '.' . $fileExtension;
        $fileContent = Storage::get($filePath);

        if ($fileContent === false) {
            throw new Exception('File content could not be retrieved.');
        }

        $iv = openssl_random_pseudo_bytes(16);
        $encryptedContent = openssl_encrypt($fileContent, 'aes-256-cbc', $this->encryptionKey, 0, $iv);

        if ($encryptedContent === false) {
            throw new Exception('Encryption failed due to an unknown error.');
        }

        $encryptedContent = base64_encode($iv . $encryptedContent);
        $encryptedPath = $customPath . '/' . $encryptedFileName;
        Storage::put($encryptedPath, $encryptedContent);

        return $encryptedPath;
    }

    public function decryptFile($filePath, $customPath, $customFileName, $fileExtension)
    {
        if (empty($filePath)) {
            throw new Exception('File path is missing.');
        }

        if (!$customFileName) {
            throw new Exception('Custom file name is missing.');
        }

        $decryptedFileName = $customFileName . '.' . $fileExtension;
        $encryptedContent = Storage::get($filePath);

        if ($encryptedContent === false) {
            throw new Exception('Encrypted file content could not be retrieved.');
        }

        $encryptedContent = base64_decode($encryptedContent);
        $iv = substr($encryptedContent, 0, 16);
        $encryptedData = substr($encryptedContent, 16);
        $decryptedContent = openssl_decrypt($encryptedData, 'aes-256-cbc', $this->encryptionKey, 0, $iv);

        if ($decryptedContent === false) {
            throw new Exception('Decryption failed due to an unknown error.');
        }

        $decryptedPath = $customPath . '/' . $decryptedFileName;
        Storage::put($decryptedPath, $decryptedContent);

        return $decryptedPath;
    }
}
