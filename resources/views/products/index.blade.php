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
        @if ($role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-md btn-primary mb-3">Add Product</a>
        @endif
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
                    <!-- @forelse ($products as $product)
                        <tr>
                            <td class="text-center">
                                <img src="{{ asset('/storage/products/'.$product->image) }}" class="rounded" style="width: 150px">
                            </td>
                            <td>{{ $product->title }}</td>
                            <td>{{ "Rp " . number_format($product->price,2,',','.') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td class="text-start">
                                @if ($product->document)
                                    {{ $product->document }}
                                @else
                                    <span class="text-danger">No Document</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('products.destroy', $product->id) }}" method="POST">
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-dark">Show</a>
                                    @if ($role === 'admin')
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-success">Edit</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="alert alert-danger">
                                    Data Products belum Tersedia.
                                </div>
                            </td>
                        </tr>
                    @endforelse -->
                </tbody>
            </table>
            {{ $products->links() }}    
        </div>
    </div>
</div>
@endsection

@push('scripts')

    <script>
        //message with sweetalert
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

            
    </script>

    <script>

        $(document).ready(function() {
            $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('products.data') }}',
                columns: [
                    {data: 'image', name: 'image', orderable: false},
                    {data: 'title', name: 'title'},
                    {data: 'price', name: 'price'},
                    {data: 'stock', name: 'stock'}, // Perbaikan kolom
                    {data: 'document', name: 'document'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false} // Perbaikan typo
                ]
            });
        });

    </script>


@endpush
