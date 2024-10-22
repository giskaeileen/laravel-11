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
        <!-- Dropdown untuk memilih jumlah data per halaman -->
        <div class="mb-3">
            <select id="itemsPerPageSelect" class="custom-select custom-select-sm form-control form-control-sm" style="width: auto;">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label for="itemsPerPageSelect" class="form-label"> data </label>
        </div>
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
            <!-- <div id="pagination" class="mt-3"></div> -->
            <div class="d-flex justify-content-between align-items-center">
                <div id="pagination-info"></div> <!-- Container untuk info pagination -->
                <div id="pagination"></div> <!-- Container untuk tombol pagination -->
            </div>

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

    // const itemsPerPage = 5;
    // let currentPage = 1;

    // $(document).ready(function() {
    //     loadProducts(currentPage);
    //     getRole();

    //     // Pagination button click handler
    //     $(document).on('click', '.pagination-btn', function() {
    //         const page = $(this).data('page');
    //         console.log('Loading products for page:', page); // Log untuk debug
    //         loadProducts(page);
    //     });
    // });

    // // Function to load products
    // async function loadProducts(page) {
    //     const token = localStorage.getItem('token');
    //     const itemsPerPage = 5; // Jumlah item per halaman
    //     const start = (page - 1) * itemsPerPage; // Menghitung start berdasarkan halaman

    //     try {
    //         const response = await fetch(`http://127.0.0.1:8000/api/products-data-api?start=${start}&length=${itemsPerPage}`, {
    //             method: 'GET',
    //             headers: {
    //                 'Authorization': `Bearer ${token}`,
    //                 'Accept': 'application/json'
    //             }
    //         });

    //         if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
    //         const data = await response.json();
    //         console.log(data); // Log data untuk memastikan produk diterima

    //         if (data.data && data.data.length) {
    //             renderTable(data.data);
    //             renderPagination(data.last_page, data.current_page, data.total); // Pass data tambahan
    //         } else {
    //             console.log('No data found for this page.');
    //         }
    //     } catch (error) {
    //         console.error('Error fetching products:', error);
    //     }
    // }

    let itemsPerPage = 5; // Jumlah default data per halaman
    let currentPage = 1;

    $(document).ready(function() {
        loadProducts(currentPage);
        getRole();

        // Event listener untuk perubahan dropdown jumlah data per halaman
        $('#itemsPerPageSelect').on('change', function() {
            itemsPerPage = $(this).val(); // Mengambil nilai dari dropdown
            currentPage = 1; // Reset ke halaman pertama setiap kali jumlah per halaman berubah
            loadProducts(currentPage); // Muat ulang produk dengan jumlah data yang diperbarui
        });

        // Pagination button click handler
        $(document).on('click', '.pagination-btn', function() {
            const page = $(this).data('page');
            console.log('Loading products for page:', page); // Log untuk debug
            loadProducts(page);
        });
    });

    // Function to load products
    async function loadProducts(page) {
        const token = localStorage.getItem('token');
        const start = (page - 1) * itemsPerPage; // Menghitung start berdasarkan halaman

        try {
            const response = await fetch(`http://127.0.0.1:8000/api/products-data-api?start=${start}&length=${itemsPerPage}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            console.log(data); // Log data untuk memastikan produk diterima

            if (data.data && data.data.length) {
                renderTable(data.data);
                renderPagination(data.last_page, data.current_page, data.total); // Pass data tambahan
            } else {
                console.log('No data found for this page.');
            }
        } catch (error) {
            console.error('Error fetching products:', error);
        }
    }


    // Function to render products in the table
    function renderTable(products) {
        const tbody = $('#productsTable tbody'); // Pilih table body
        tbody.empty(); // Kosongkan baris yang ada

        products.forEach(product => {
            const row = `
                <tr>
                    <td>${product.image}</td>
                    <td>${product.title}</td>
                    <td>${product.price}</td>
                    <td>${product.stock}</td>
                    <td>${product.file}</td>
                    <td>${product.actions}</td>
                </tr>
            `;
            tbody.append(row); // Tambahkan baris baru ke table body
        });
    }

    // Function to render pagination and data summary
    // function renderPagination(lastPage, currentPage, totalRecords) {
    //     const paginationContainer = $('#pagination');
    //     const paginationInfo = $('#pagination-info'); // Container untuk info pagination
    //     paginationContainer.empty(); // Kosongkan pagination yang ada

    //     // Hitung posisi data yang sedang ditampilkan
    //     const startRecord = (currentPage - 1) * itemsPerPage + 1;
    //     const endRecord = Math.min(currentPage * itemsPerPage, totalRecords);

    //     // Tampilkan info pagination
    //     paginationInfo.text(`Menampilkan ${startRecord} sampai ${endRecord} dari ${totalRecords} data`);

    //     // Render pagination buttons
    //     for (let i = 1; i <= lastPage; i++) {
    //         const btn = `
    //             <button data-page="${i}" class="pagination-btn btn btn-outline-primary">
    //                 ${i}
    //             </button>
    //         `;
    //         paginationContainer.append(btn);
    //     }
    // }
    function renderPagination(lastPage, currentPage, totalRecords) {
        const paginationContainer = $('#pagination');
        const paginationInfo = $('#pagination-info'); // Container untuk info pagination
        paginationContainer.empty(); // Kosongkan pagination yang ada

        // Hitung posisi data yang sedang ditampilkan
        const startRecord = (currentPage - 1) * itemsPerPage + 1;
        const endRecord = Math.min(currentPage * itemsPerPage, totalRecords);

        // Tampilkan info pagination di sebelah kiri
        paginationInfo.text(`Menampilkan ${startRecord} sampai ${endRecord} dari ${totalRecords} data`);

        // Tambahkan tombol "Sebelumnya"
        if (currentPage > 1) {
            const prevBtn = `
                <button data-page="${currentPage - 1}" class="pagination-btn btn btn-outline-primary">
                    Sebelumnya
                </button>
            `;
            paginationContainer.append(prevBtn);
        }

        // Render tombol nomor halaman
        for (let i = 1; i <= lastPage; i++) {
            const btn = `
                <button data-page="${i}" class="pagination-btn btn btn-outline-primary ${i === currentPage ? 'active' : ''}">
                    ${i}
                </button>
            `;
            paginationContainer.append(btn);
        }

        // Tambahkan tombol "Berikutnya"
        if (currentPage < lastPage) {
            const nextBtn = `
                <button data-page="${currentPage + 1}" class="pagination-btn btn btn-outline-primary">
                    Berikutnya
                </button>
            `;
            paginationContainer.append(nextBtn);
        }
    }




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
                    alert('Produk berhasil dihapus!');
                    loadProducts(currentPage); // Refresh the product list
                } else {
                    alert('Gagal menghapus produk.');
                }
            } catch (error) {
                console.error('Kesalahan:', error);
            }
        }
    });

    // Function to get user role
    async function getRole() {
        const token = localStorage.getItem('token');

        try {
            const response = await fetch('http://127.0.0.1:8000/api/products-role-api', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const roleData = await response.json();
            const role = roleData.role;

            if (role === 'admin') {
                document.getElementById('roleActions').innerHTML = `
                    <a href="{{ route('products.create') }}" class="btn btn-md btn-success mb-3">ADD PRODUCT</a>
                `;
            }
        } catch (error) {
            if (error.message.includes('401')) {
                await refreshToken();
                getRole();
            } else {
                console.error('Error fetching role:', error);
            }
        }
    }
</script>
@endpush


