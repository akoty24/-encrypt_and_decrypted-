@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg p-3 mb-5 rounded card-background">
        <div class="card-body">
            <h2 class="card-title text-center mb-4" style="color: #ffc107">File Encryption and Decryption</h2>
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="fileInput">
                    <label class="custom-file-label" for="fileInput">Choose file</label>
                </div>
            </div>
            <div id="fileDetails" class="alert alert-info" role="alert" style="display: none; background-color: rgba(0, 123, 255, 0.7); color: #ffc107;" ></div>

            <div class="text-center">
                <button id="uploadBtn" class="btn btn-primary mt-3" disabled>Upload</button>
            </div>
            <div id="loader" class="loader"></div>

            <!-- Custom Path and File Name inputs -->
            <div id="customInputsContainer" class="mt-4" style="display: none;">
                <div class="text-center">
                    <button id="encryptBtn" class="btn btn-success mt-3">Encrypt</button>
                    <button id="decryptBtn" class="btn btn-warning mt-3">Decrypt</button>
                </div>
            </div>

            <div id="result" class="alert alert-info mt-3" role="alert" style="display: none;"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var fileExtension = '';

    $('#fileInput').change(function() {
        var file = this.files[0];
        
        if (file) {
            fileExtension = file.name.split('.').pop();
            var fileSizeMB = (file.size / (1024 * 1024)).toFixed(2); // Convert to MB
            var fileSizeGB = (file.size / (1024 * 1024 * 1024)).toFixed(2); // Convert to GB

            var fileSizeDisplay = fileSizeMB + ' MB';
            if (file.size >= 1024 * 1024 * 1024) {
                fileSizeDisplay = fileSizeGB + ' GB';
            }

            $('#fileDetails').show().html(`
                <p><strong>File Name:</strong> ${file.name}</p>
                <p><strong>File Size:</strong> ${fileSizeDisplay}</p>
                <p><strong>File Extension:</strong> ${fileExtension}</p>
            `);
            $('#uploadBtn').prop('disabled', false);
        } else {
            $('#fileDetails').hide();
            $('#uploadBtn').prop('disabled', true);
        }
    });

    $('#uploadBtn').click(function() {
    // Show loader
    $('#loader').show();

    var formData = new FormData();
    formData.append('file', $('#fileInput')[0].files[0]);

    $.ajax({
        url: '{{ route('upload') }}',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            $('#loader').hide();
                var fileSizeMB = (response.size / (1024 * 1024)).toFixed(2); // Convert to MB
                var fileSizeGB = (response.size / (1024 * 1024 * 1024)).toFixed(2); // Convert to GB

                var fileSizeDisplay = fileSizeMB + ' MB';
                if (response.size >= 1024 * 1024 * 1024) {
                    fileSizeDisplay = fileSizeGB + ' GB';
                }

                $('#fileDetails').show().html(`
                    <p><strong>File Name:</strong> ${response.name}</p>
                    <p><strong>File Size:</strong> ${fileSizeDisplay}</p>
                    <p><strong>File Extension:</strong> ${response.extension}</p>
                `);
                $('#encryptBtn').show().data('path', response.path).data('extension', response.extension);
                $('#decryptBtn').show().data('path', response.path).data('extension', response.extension);
                $('#customInputsContainer').show(); // Show custom inputs
                
                // Display SweetAlert
                Swal.fire("Upload successful", `File uploaded successfully: ${response.name}`, "success");
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                Swal.fire("Upload failed", `Status: ${status}, Error: ${error}`, "error");
            }
        });
    });

    $('#encryptBtn').click(function() {
        $('#loader').show();
        var filePath = $(this).data('path');
        var customPath = $('#customPath').val();
        var customFileName = $('#customFileName').val();
        var fileExtension = $(this).data('extension');

        $.ajax({
            url: '{{ route('encrypt') }}',
            type: 'POST',
            data: {
                file_path: filePath,
                custom_path: customPath,
                custom_file_name: customFileName,
                file_extension: fileExtension
            },
            success: function(response) {
                $('#loader').hide();
                Swal.fire("Encryption successful", `Encrypted file saved at: ${response.encrypted_path}`, "success");
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                Swal.fire("Encryption failed", `${xhr.responseText}`, "error");
            }
        });
    });

    $('#decryptBtn').click(function() {
        $('#loader').show();
        var filePath = $(this).data('path');
        var customPath = $('#customPath').val();
        var customFileName = $('#customFileName').val();
        var fileExtension = $(this).data('extension');

        $.ajax({
            url: '{{ route('decrypt') }}',
            type: 'POST',
            data: {
                file_path: filePath,
                custom_path: customPath,
                custom_file_name: customFileName,
                file_extension: fileExtension
            },
            success: function(response) {
                $('#loader').hide();
                Swal.fire("Decryption successful", `Decrypted file saved at: ${response.decrypted_path}`, "success");
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                Swal.fire("Decryption failed", `${xhr.responseText}`, "error");
            }
        });
    });
});

</script>
@endsection
