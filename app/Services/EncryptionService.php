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
            // Open input and output file pointers
            $input = fopen($filePath, 'rb');
            $output = fopen($encryptedFilePath, 'wb');
        
            // Generate IV
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            fwrite($output, $iv, strlen($iv));
        
            // Encrypt block by block
            while (!feof($input)) {
                $block = fread($input, 4096); // Adjust block size as needed
                $encryptedBlock = openssl_encrypt($block, 'aes-256-cbc', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
                fwrite($output, $encryptedBlock, strlen($encryptedBlock));
            }
        
            fclose($input);
            fclose($output);
        
            return ['status' => 'success', 'message' => 'File encrypted successfully', 'filename' => $fileName . '.' . $fileExtension];
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
            // Open input and output file pointers
            $input = fopen($filePath, 'rb');
            $output = fopen($decryptedFilePath, 'wb');
        
            // Read IV from the beginning of the file
            $iv = fread($input, openssl_cipher_iv_length('aes-256-cbc'));
        
            // Decrypt block by block
            while (!feof($input)) {
                $block = fread($input, 4096 + openssl_cipher_iv_length('aes-256-cbc')); // Adjust block size as needed
                $decryptedBlock = openssl_decrypt($block, 'aes-256-cbc', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
                fwrite($output, $decryptedBlock);
            }
        
            fclose($input);
            fclose($output);
        
            return ['status' => 'success', 'message' => 'File decrypted successfully', 'filename' => $fileName . '.' . $fileExtension];
        } catch (Exception $e) {
            throw new Exception('File decryption failed: ' . $e->getMessage());
        }
    }
    
          
}
