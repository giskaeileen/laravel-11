<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Products - SantriKoding.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: lightgray">

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <form id="updateForm" method="POST" enctype="multipart/form-data">
                            
                            @csrf
                            {{-- @method('PUT') --}}

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">IMAGE</label>
                                <input type="file" id="product-image" class="form-control @error('image') is-invalid @enderror" name="image">
                            
                                <!-- error message untuk image -->
                                @error('image')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">TITLE</label>
                                <input type="text" id="product-title" class="form-control @error('title') is-invalid @enderror" name="title" value="" placeholder="Masukkan Judul Product">
                                {{-- <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $product->title) }}" placeholder="Masukkan Judul Product"> --}}
                            
                                <!-- error message untuk title -->
                                @error('title')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">DESCRIPTION</label>
                                <textarea id="product-description" class="form-control @error('description') is-invalid @enderror" name="description" rows="5" placeholder="Masukkan Description Product"></textarea>
                                {{-- <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="5" placeholder="Masukkan Description Product">{{ old('description', $product->description) }}</textarea> --}}
                            
                                <!-- error message untuk description -->
                                @error('description')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">PRICE</label>
                                        <input type="number" id="product-price" class="form-control @error('price') is-invalid @enderror" name="price" value="" placeholder="Masukkan Harga Product">
                                        {{-- <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $product->price) }}" placeholder="Masukkan Harga Product"> --}}
                                    
                                        <!-- error message untuk price -->
                                        @error('price')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">STOCK</label>
                                        <input type="number" id="product-stock" class="form-control @error('stock') is-invalid @enderror" name="stock" value="" placeholder="Masukkan Stock Product">
                                        {{-- <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', $product->stock) }}" placeholder="Masukkan Stock Product"> --}}
                                    
                                        <!-- error message untuk stock -->
                                        @error('stock')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold" for="">File</label>
                                    <input type="file" id="product-file" class="form-control @error('file') is-invalid @enderror" name="file" value="" placeholder="Masukan File">
                                    {{-- <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" value="{{ old('file', $product->file)}}" placeholder="Masukan File"> --}}
                                    @error('file')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                            <button type="reset" class="btn btn-md btn-warning">RESET</button>

                        </form> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
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

        async function getData () {
            const id = {{ request()->route('id')}}
            const token = localStorage.getItem('token')
            console.log(id)
            console.log(token)

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

                const data = await response.json()

                if (response.status === 200) {
                    document.getElementById('product-title').value = data.products.title;
                    // document.getElementById('product-description').value = data.products.description;
                    CKEDITOR.instances['product-description'].setData(data.products.description);
                    document.getElementById('product-price').value = data.products.price;
                    document.getElementById('product-stock').value = data.products.stock;
                }

            } catch (error) {
                if (error.message.includes('401')) {
                    await refreshToken()

                    getData()
                    
                }
            }

        }

        document.addEventListener('DOMContentLoaded', getData)




        async function updateForm (event) {
            event.preventDefault();

            CKEDITOR.instances['product-description'].updateElement();

            const formData = new FormData(this)
            // formData.forEach((value, key) => {
            //     console.log(key, value);
            // });
            const id = {{ request()->route('id') }};
            const token = localStorage.getItem('token');

            console.log('Title:', formData.get('title'));
            console.log('Description:', formData.get('description'));
            console.log('Price:', formData.get('price'));
            console.log('Stock:', formData.get('stock'));



            try {
                const response = await fetch (`http://127.0.0.1:8000/api/products-update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData,
                });

                if (response.status === 200) {
                    window.location.href = "{{ route ('products.index') }}"
                } else {
                    alert(data.message)
                }
            } catch (error) {
                if (error.message.includes('401')) {
                    await refreshToken()

                    updateForm()
                    
                }
            }
        }

        document.getElementById('updateForm').addEventListener('submit', updateForm)

    </script>
</body>
</html>
