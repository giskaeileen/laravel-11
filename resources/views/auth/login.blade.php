<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100">
            <div class="col-md-6 col-lg-5 col-xl-5 mx-auto">
                <div class="card shadow-sm">
                    <div class="text-center py-3">
                        <h3 class="mb-0">Login</h3>
                    </div>
                    <div class="card-body p-4">
                        <!-- Login Form -->
                        <form method="POST" id="loginForm">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                                <div id="emailError" class="invalid-feedback"></div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                <div id="passwordError" class="invalid-feedback"></div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">Remember Me</label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">Login</button>

                            <!-- Error Message -->
                            <div id="loginError" class="text-danger mt-3" style="display:none;"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for handling form submission -->
    <!-- <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Mencegah refresh halaman saat form disubmit

            // Ambil data dari form
            const formData = new FormData(this);

            try {
                // Kirim request login ke API
                const response = await fetch('{{ route('login.post') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    // body: JSON.string5ify({email, password})
                    body: formData
                });

                // Ambil respons JSON dari server
                const data = await response.json();
                // console.log('Response Data:', data)

                // Jika login berhasil, redirect ke route product.post
                if (response.ok) {
                    // Simpan token JWT di localStorage atau sessionStorage jika diperlukan
                    localStorage.setItem('token', data.token);
                    console.log('Token:', data.token);

                    // Redirect ke halaman produk
                    window.location.href = "{{ route('products.index') }}";
                    // window.location.href = '/products';
                } else {
                    // Tampilkan error jika login gagal
                    alert(data.error || 'Login failed');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            }
        });
    </script> -->

    <!-- <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('http://127.0.0.1:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', 
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await response.json();
                console.log('Response Data:', data);

                if (response.ok) {
                    localStorage.setItem('token', data.token);

                    sessionStorage.setItem('email', data.user.email);

                    window.location.href = "{{ route('products.index') }}";
                } else {
                    console.error('Error:', data.message);
                }
            } catch (error) {
                console.error('Login failed:', error);
            }
        });
    </script> -->

    <!-- <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('http://127.0.0.1:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', 
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await response.json();
                console.log('Response Data:', data);

                if (response.ok) {
                    // localStorage.setItem('access_token', data.access_token);
                    // localStorage.setItem('refresh_token', data.refresh_token);
                    localStorage.setItem('token', data.token);
                    
                    sessionStorage.setItem('email', data.user.email);
                    sessionStorage.setItem('role', data.user.role);

                    window.location.href = "{{ route('products.index') }}";
                } else {
                    console.error('Error:', data.message);
                }
            } catch (error) {
                console.error('Login failed:', error);
            }
        });
    </script> -->

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            // Ambil referensi ke tombol login
            const loginButton = document.querySelector('button[type="submit"]');

            // Ubah status tombol menjadi tidak bisa dipencet dan ubah teksnya menjadi "Loading..."
            loginButton.disabled = true;
            loginButton.innerHTML = 'Loading...';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('http://127.0.0.1:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', 
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await response.json();
                console.log('Response Data:', data);

                if (response.ok) {
                    // Simpan token dan redirect
                    localStorage.setItem('token', data.token);
                    sessionStorage.setItem('email', data.user.email);
                    sessionStorage.setItem('role', data.user.role);

                    window.location.href = "{{ route('products.index') }}";
                } else {
                    // Jika terjadi kesalahan, tampilkan error dan aktifkan kembali tombol login
                    console.error('Error:', data.message);
                    loginButton.disabled = false;
                    loginButton.innerHTML = 'Login';
                }
            } catch (error) {
                // Jika ada error dalam proses fetch, tampilkan error dan aktifkan kembali tombol
                console.error('Login failed:', error);
                loginButton.disabled = false;
                loginButton.innerHTML = 'Login';
            }
        });
    </script>


</body>
</html>
