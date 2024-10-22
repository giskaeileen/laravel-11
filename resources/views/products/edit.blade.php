@extends('layouts.app')

@section('title', 'Products')

@section('content')
 
<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Edit Products</h1>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form id="updateForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">IMAGE</label>
                        <input type="file" id="product-image" class="form-control @error('image') is-invalid @enderror" name="image">
                        @error('image')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">TITLE</label>
                        <input type="text" id="product-title" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Enter Product Title">
                        @error('title')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">DESCRIPTION</label>
                        <textarea id="product-description" class="form-control @error('description') is-invalid @enderror" name="description" rows="5" placeholder="Enter Product Description"></textarea>
                        @error('description')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">PRICE</label>
                                <input type="number" id="product-price" class="form-control @error('price') is-invalid @enderror" name="price" placeholder="Enter Product Price">
                                <!-- <input type="number" id="product-price" class="form-control @error('price') is-invalid @enderror" name="price" value="" placeholder="Masukkan Harga Product"> -->
                                @error('price')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">STOCK</label>
                                <input type="number" id="product-stock" class="form-control @error('stock') is-invalid @enderror" name="stock" placeholder="Enter Product Stock">
                                @error('stock')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">FILE</label>
                        <input type="file" id="product-file" class="form-control @error('file') is-invalid @enderror" name="file">
                        @error('file')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">UPDATE</button>
                    <button type="reset" class="btn btn-warning">RESET</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

    <script>
        CKEDITOR.replace('product-description');

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
                    alert('Data berhasil disimpan!'); // Alert sukses
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

@endpush

