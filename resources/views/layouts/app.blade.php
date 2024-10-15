<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Laravel')</title>

    <!-- Custom fonts for this template -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">

        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                @include('layouts.header')
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script> 

    <!-- Page level custom scripts -->
    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>

    <script>

        // async function refreshToken() {
        //     const refreshToken = localStorage.getItem('refresh_token');

        //     if (!refreshToken) {
        //         console.log('Refresh token tidak ditemukan. Redirect ke halaman login.');
        //     }

        //     try {
        //         const response = await fetch('http://127.0.0.1:8000/api/refresh-token-api', {
        //             method: 'POST',
        //             headers: {
        //                 'Authorization': `Bearer ${refreshToken}`,
        //                 'Content-Type': 'application/json',
        //             },
        //         });

        //         if (response.ok) {
        //             const data = await response.json();
        //             localStorage.setItem('access_token', data.access_token);
        //             return data.access_token;
        //         } else {
        //             window.location.href = "{{ route('login') }}";
        //         }
        //     } catch (error) {
        //         console.error('Token refresh failed:', error);
        //         window.location.href = "{{ route('login') }}"; // Arahkan ke login jika ada error
        //     }
        // }

        // refreshToken();

        async function refreshToken() {
            const token = localStorage.getItem('token');

            if (!token) {
                console.log('Token not found');
            }

            try {
                const response = await fetch('http://127.0.0.1:8000/api/refresh-token-api', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                });
                const data = await response.json();
                console.log(data)

                if (response.ok) {
                    localStorage.setItem('token', data.token);
                    return data.token;
                } else {
                    window.location.href = "{{ route('login') }}";
                }
            } catch (error) {
                window.location.href = "{{ route('login') }}";
                // console.error('Token refresh failed:', error);
            }
        }



        function checkLogin() {
            const token = localStorage.getItem('token');
            const email = sessionStorage.getItem('email');

            if (!token || !email) {
                window.location.href = "{{ route('login') }}"; // Ganti dengan rute login yang sesuai
            }
        }

        checkLogin();
    </script>



    @stack('scripts')

</body>

</html>
