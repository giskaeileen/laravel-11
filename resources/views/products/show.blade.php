<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Show Products - SantriKoding.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: lightgray">

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <img id="product-image" class="rounded" style="width: 100%">
                        {{-- src="{{ asset('/storage/products/'.$product->image) }}" --}}
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <h3 id="product-title"></h3>
                        <hr/>
                        <p id="product-price"></p>
                        <code>
                            <p id="product-description"></p>
                        </code>
                        <hr/>
                        <p id="product-stock"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
                console.error('Token refresh failed:', error);
                window.location.href = "{{ route('login') }}";
            }
        }



        async function getData() {
            const id = {{ request()->route('id') }};
            const token = localStorage.getItem('token');

            try {
                const response = await fetch(`http://127.0.0.1:8000/api/products-data-show-api/${id}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                })

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log(data);

                if (response.status === 200) {
                    const imageUrl = `{{ asset('/storage/products/') }}/${data.products.image}`;

                    document.getElementById('product-image').src = imageUrl;
                    document.getElementById('product-title').textContent = data.products.title;
                    document.getElementById('product-description').textContent = data.products.description;
                    document.getElementById('product-stock').textContent = `Stock: ${data.products.stock}`;
                    const priceInRupiah = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                    }).format(data.products.price);

                    document.getElementById('product-price').textContent = `Price: ${priceInRupiah}`;
                }else {
                    console.error('Kesalahan:', error);;
                }
            } catch (error) {
                if (error.message.includes('401')) {
                    await refreshToken()

                    getData()
                }
            }
        }

        document.addEventListener('DOMContentLoaded', getData)

    </script>
</body>
</html>