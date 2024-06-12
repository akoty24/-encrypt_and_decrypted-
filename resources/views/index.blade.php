@extends('layout.app')
@section('content')
<div class="container mt-5">
    <div class="card shadow-lg p-3 mb-5 rounded card-background">
        <div class="card-body">
            <h2 class="card-title text-center mb-4" style="color: #FFC107">File Encryption and Decryption</h2>
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="fileInput" multiple>
                    <label class="custom-file-label" for="fileInput">Choose file</label>
                </div>
            </div>
            <div id="fileDetails" class="alert alert-info" role="alert"
                style="display: none; background-color: rgba(0, 123, 255, 0.1); color: #FFC107;"></div>
            <div class="text-center">
                <button id="uploadBtn" class="btn btn-primary mt-3">Upload</button>
            </div>
            <div class="progress mt-3" style="display: none;">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100"></div>
            </div>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        var r = new Resumable({
            target: '{{ route('upload') }}',
            query: { _token: $('meta[name="csrf-token"]').attr('content') },
            chunkSize: 1 * 1024 * 1024,
            simultaneousUploads: 3,
            testChunks: false,
            throttleProgressCallbacks: 1,
        });

        r.assignBrowse(document.getElementById('fileInput'));

        r.on('fileAdded', function(file) {
            var fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            var fileSizeGB = (file.size / (1024 * 1024 * 1024)).toFixed(2);
            var fileSizeDisplay = fileSizeMB + ' MB';
            if (file.size >= 1024 * 1024 * 1024) {
                fileSizeDisplay = fileSizeGB + ' GB';
            }
            var fileExtension = file.fileName.split('.').pop();

            $('#fileDetails').show().html(`
                <p><strong>File Name:</strong> ${file.fileName}</p>
                <p><strong>File Size:</strong> ${fileSizeDisplay}</p>
                <p><strong>File Extension:</strong> ${fileExtension}</p>
            `);
            $('#uploadBtn').prop('disabled', false);
        });

        r.on('fileProgress', function(file) {
            var progress = Math.floor(file.progress() * 100);
            $('#progressBar').css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
        });

        r.on('fileSuccess', function(file, message) {
            var response = JSON.parse(message);
            var fileSizeMB = (response.size / (1024 * 1024)).toFixed(2);
            var fileSizeGB = (response.size / (1024 * 1024 * 1024)).toFixed(2);
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
            $('#customInputsContainer').show();
            $('.progress').hide();
            Swal.fire("Upload successful", `File uploaded successfully: ${response.name}`, "success");
        });

        r.on('fileError', function(file, message) {
            Swal.fire("Upload failed", message, "error");
            $('.progress').hide();
        });

        $('#uploadBtn').click(function() {
            $('.progress').show();
            r.upload();
        });

        $('#encryptBtn').click(function() {
            var filePath = $(this).data('path');
            $('.progress').show(); // Show the progress bar for encryption
            $.post('{{ route('encrypt') }}', { path: filePath })
            .done(function(response) {
                $('.progress').hide();
                Swal.fire("Encryption successful", 'File encrypted successfully:' + response.path, "success");
            })
            .fail(function(error) {
                $('.progress').hide();
                Swal.fire("Encryption failed", error.responseJSON.message, "error");
            })
            .always(function() {
                $('.progress').hide(); // Hide the progress bar after encryption is complete
            });
        });

        $('#decryptBtn').click(function() {
            var filePath = $(this).data('path');
            $('.progress').show(); // Show the progress bar for decryption
            $.post('{{ route('decrypt') }}', { path: filePath })
            .done(function(response) {
                $('.progress').hide();
                Swal.fire("Decryption successful", `File decrypted successfully: ${response.path}`, "success");

            })
            .fail(function(error) {
                $('.progress').hide();
                Swal.fire("Decryption failed", error.responseJSON.message, "error");
            })
            .always(function() {
                $('.progress').hide(); // Hide the progress bar after decryption is complete
            });
        });

        function downloadFile(filePath) {
            window.location.href = '/download/' + filePath;
        }
    });

</script>
@endsection

