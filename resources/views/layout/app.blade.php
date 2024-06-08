<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Encryption App</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
      
        .custom-file-label::after {
    content: "Browse";
    background-color: #007bff;
    color: white;
}
.card-background {
    background: url('{{ asset('enp_20231017-encryption-types.webp') }}') no-repeat center center ;
    background-size: cover;
    color: white;
}
    </style>
</head>
<body>
    <div class="container mt-5">
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- SweetAlert initialization -->
    <script>
        @if(Session::has('success'))
            Swal.fire("Success!", "{{ Session::get('success') }}", "success");
        @endif

        @if(Session::has('error'))
            Swal.fire("Error!", "{{ Session::get('error') }}", "error");
        @endif
    </script>
    <!-- Custom Scripts -->
    @yield('scripts')
</body>
</html>
