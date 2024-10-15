@extends('layouts.app')

@section('title', 'Products')

@section('content')

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Tables</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
    </div>
    <div class="card-body">
        <div id="roleActions"></div>
        <div class="table-responsive">
            <table class="table table-bordered" id="productsTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Document</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Document</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table> 
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>

    // SweetAlert message
    @if(session('success'))
        Swal.fire({
            icon: "success",
            title: "BERHASIL",
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });
    @elseif(session('error'))
        Swal.fire({
            icon: "error",
            title: "GAGAL!",
            text: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    $(document).ready(function() {

        function loadDatatable() {

            const token = localStorage.getItem('token');
    
            $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: '{{ route('products.data.api') }}',
                    type: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    error: async function(xhr, status, error) {
                        if (xhr.status === 401) {
                            await refreshToken()

                            loadDatatable(); 
                        } else {
                            console.error('Error fetching data:', error);
                        }
                    }
                },
                columns: [
                    {data: 'image', name: 'image', orderable: false},
                    {data: 'title', name: 'title'},
                    {data: 'price', name: 'price', orderable: false, searchable: false},
                    {data: 'stock', name: 'stock', orderable: false, searchable: false},
                    {data: 'file', name: 'file', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ]
            });
        }

        loadDatatable()

    });

    $('#productsTable').on('click', '.delete-product', async function() {

        const id = $(this).data('id'); 
        const token = localStorage.getItem('token'); 


        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/products-delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 200) {
                    window.location.href = "{{ route ('products.index') }}"
                    alert('Produk berhasil dihapus!');
                } else {
                    alert('Gagal menghapus produk.');
                }
            } catch (error) {
                console.error('Kesalahan:', error);
            }
        }
    });

    async function getRole() {
        const token = localStorage.getItem('token');
        console.log(token)

        try {
            const response = await fetch('http://127.0.0.1:8000/api/products-role-api', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const roleData = await response.json();
            const role = roleData.role;
            console.log(role);

            if (role === 'admin') {
                document.getElementById('roleActions').innerHTML = `
                    <a href="{{ route('products.create') }}" class="btn btn-md btn-success mb-3">ADD PRODUCT</a>
                `;
            }

        } catch (error) {
            if (error.message.includes('401')) {
                await refreshToken()
                // console.log(error)
                getRole()
            }
        }
    };

    document.addEventListener('DOMContentLoaded', getRole);
</script>

    


@endpush
