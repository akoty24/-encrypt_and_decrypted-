<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

class EncryptionService
{
    private $encryptionKey = 'your256bitkeyyour256bitkeyyour256bitkey12'; // 32 characters for 256-bit key

    public function encryptFile($filePath,  $fileExtension)
    {
        if (empty($filePath)) {
            throw new Exception('File path is missing.');
        }

  
        $encryptedFileName =  'encryptedFile'. time() .'.' . $fileExtension;
        ////////////////////
        /// 
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
        $encryptedPath = 'en' . '/' . $encryptedFileName;
        Storage::put($encryptedPath, $encryptedContent);

        return $encryptedPath;
    }

    public function decryptFile($filePath,$fileExtension)
    {
        if (empty($filePath)) {
            throw new Exception('File path is missing.');
        }
        $encryptedContent = Storage::get($filePath);


        $decryptedFileName = 'decryptedFile' . time() .'.' . $fileExtension;

        if ($encryptedContent === false) {
            throw new Exception('Encrypted file content could not be retrieved.');
        }

        $ivCipherText = base64_decode($encryptedContent);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($ivCipherText, 0, $ivLength);
        $cipherText = substr($ivCipherText, $ivLength);
        
        $decrypted = openssl_decrypt($cipherText, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
//        $decryptedContent = openssl_decrypt($encryptedData, 'aes-256-cbc', $this->encryptionKey, 0, $iv);

        if ($decrypted === false) {
            throw new Exception('Decryption failed due to an unknown error.');
        }

        $decryptedFileName = 'decrypted_' . time() . '.pdf';
        Storage::put('decrypted/' . $decryptedFileName, $decrypted);

        return $decryptedFileName;
    }
}
