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
                {{-- @if ($role === 'admin')
                    <a href="{{ route('products.create') }}" class="btn btn-md btn-success mb-3">ADD PRODUCT</a>
                @endif --}}
                <div class="d-flex justify-content-between align-items-center">
                    <div id="roleActions"></div>
                    <div id="filterAction">
                        <button class="btn btn-md btn-success mb-3" id="filterButton">FILTER</button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div id="show-perPage">
                        <label for="perPage">Show per page:</label>
                        <select id="perPage" class="custom-select custom-select-sm form-control form-control-sm" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div id="filterContent" class="d-flex">
                        <div class="" id="orderByPage">
                            <label for="sortBy">Sort By:</label>
                            <select id="sortBy" class="custom-select custom-select-sm form-control form-control-sm" style="width: auto;">
                                <option value="id" selected>ID</option> 
                                <option value="title">Judul</option>
                                <option value="price">Harga</option>
                                <option value="stock">Stok</option>
                            </select>
        
                            <label for="sortDirection"></label>
                            <select id="sortDirection" class="custom-select custom-select-sm form-control form-control-sm" style="width: auto;">
                                <option value="asc" selected>Ascending</option> 
                                <option value="desc">Descending</option>
                            </select>
                        </div>
                        <div id="searchData" class="d-flex ml-4">
                            <label for="search">Search:</label>
                            <input type="text" id="search" class="form-control form-control-sm ml-1">
                        </div>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table id="productsTable" class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="data-info">
                            Total Products: <span id="total-data">0</span>
                        </div>                
                        <div class="btn-group" role="group" id="pagination-controls">
                            <button class="btn btn-sm form-control" id="prev-page" disabled>Previous</button>
                            <span class="btn btn-sm" id="current-page">1</span>
                            <button class="btn btn-sm form-control" id="next-page" disabled>Next</button>
                        </div>
                    </div>
                </div>

                {{-- {{ $products->links() }}
                <div class="pagination">
                    {{ $products->links() }}
                </div> --}}

            </div>
    </div>
    
@endsection

@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', function() {
        getRole();
        renderTable();
        document.getElementById('filterButton').addEventListener('click', filterButton);
    });

    let currentPage = 1;
    let itemsPerPage = 10;
    let sortBy = 'id';
    let sortDirection = 'asc';
    let search = '';

    async function renderTable(page = currentPage, perPage = itemsPerPage, itemsSortBy = sortBy, itemsSortDirection = sortDirection, searchItem = search) {
        const token = localStorage.getItem('token')

        try {
            const response = await fetch(`http://127.0.0.1:8000/api/products-data-api?page=${page}&per_page=${perPage}&sort_by=${itemsSortBy}&sort_direction=${itemsSortDirection}&search=${encodeURIComponent(searchItem)}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
            })

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`)
            }

            const data = await response.json()

            const tbody = document.querySelector('#productsTable tbody');
            tbody.innerHTML = '';

            if (!data.data.length) {
                const row = `
                    <tr>
                        <td colspan="8" style="text-align: center;">Data Not Found</td>
                    </tr>
                `;

                tbody.insertAdjacentHTML('beforeend', row);
                document.getElementById('total-data').textContent = 0;
                return;
            }

            if (data.pagination.total === 0) {
                const row = `
                    <tr>
                        <td colspan="8" style="text-align: center;">Data Not Found</td>
                    </tr>
                `;

                tbody.insertAdjacentHTML('beforeend', row);     
                return;
            }

            //jquery
            // const tbody = $('#productsTable tbody')
            // tbody.empty()

    
            data.data.forEach(product => {
                const row = `
                    <tr>
                        <input type="hidden" id="product-id" value="${product.id}">
                        <td>${product.image}</td>    
                        <td>${product.title}</td>    
                        <td>${product.price}</td>    
                        <td>${product.stock}</td>    
                        <td>${product.file}</td>    
                        <td>${product.actions}</td>    
                    </tr>
                `
                tbody.insertAdjacentHTML('beforeend', row);

                // tbody.appendChild(row);
                //jquery
                // tbody.append(row)
            });

            document.getElementById('total-data').textContent = data.pagination.total;

            document.getElementById('current-page').textContent = data.pagination.current_page;

            document.getElementById('prev-page').disabled = !data.pagination.prev_page_url;
            document.getElementById('next-page').disabled = !data.pagination.next_page_url;

        } catch (error) {
            await refreshToken(401)
            renderTable()
        }
    }

    document.getElementById('prev-page').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable(currentPage, itemsPerPage, sortBy, sortDirection, search);
        }
    });

    document.getElementById('next-page').addEventListener('click', () => {
        currentPage++;
        renderTable(currentPage, itemsPerPage, sortBy, sortDirection, search);
    });

    async function filterButton() {
        itemsPerPage = document.getElementById('perPage').value;
        currentPage = 1;

        sortBy = document.getElementById('sortBy').value;
        sortDirection = document.getElementById('sortDirection').value;

        search = document.getElementById('search').value;

        await renderTable(currentPage, itemsPerPage, sortBy, sortDirection), search;
    }

    // renderTable(currentPage, itemsPerPage, sortBy, sortDirection);


    document.getElementById('productsTable').addEventListener('click', async function(event) {
        if (event.target.classList.contains('delete-product')) {
            // const id = $(this).data('id')
            const id = event.target.closest('tr').querySelector('#product-id').value
            const token = localStorage.getItem('token')

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Product ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`http://127.0.0.1:8000/api/products-delete/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.status === 200) {
                            Swal.fire(
                                'Dihapus!',
                                'Produk berhasil dihapus',
                                'success',
                            ).then(() => {
                                renderTable();
                            })
                        } else {
                            Swal.fire(
                                'Gagal',
                                'Gagal meghapus produk',
                                'error'
                            )
                        }
                    } catch (error) {
                        await refreshToken(401)
                        getRole()               
                    }
                }
            })

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
            await refreshToken(401)
            getRole()

            // if (error.message.includes('401')) {
            // }
        }
    };


</script>



@endpush