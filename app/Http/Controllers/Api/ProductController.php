<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product; 
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;


class ProductController extends Controller
{
    public function role() 
    {
        // $user = auth()->guard('api')->user();
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }

        $role = $user->role;

        return response()->json([
            'role' => $role
        ], 200);

    }

    public function getData(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'message' => 'Data dikirim',
            'products' => $product,
        ], 200);
    }

    // public function getProductsData()
    // {
    //     // kalo milih2
    //     $products = Product::select('id', 'title', 'description', 'price', 'stock', 'file')->get();
    //     //kalo butuh semua data tanpa milih
    //     $products = Product::all();
    //     $role = Auth::guard('api')->user()->role;
    //     return DataTables::of($products)
    //         ->addColumn('image', function($row) {
    //             return '<img src="' . asset('/storage/products/' . $row->image) . '" style="width: 100px; height: auto;">';
    //         })

    //         ->editColumn('price', function($product) {
    //             return 'Rp. ' . number_format($product->price, 2, ',', '.'); // Format harga dengan prefix
    //         })   
            
    //         ->addColumn('actions', function($row) use($role) {
    //             if ($role === 'admin') {
    //                 return '<a href="' . route('products.show', $row->id) . '" class="btn btn-sm btn-secondary">Show</a>
    //                 <a href="' . route('products.edit', $row->id) . '" class="btn btn-success btn-sm">Edit</a>
    //                 <form action="' . route('products.destroy', $row->id) . '" method="POST" style="display:inline;">
    //                     ' . csrf_field() . method_field('DELETE') . '
    //                     <button type="submit" class="btn btn-danger btn-sm delete-product" data-id="' . $row->id .'">Delete</button>
    //                 </form>';
    //             }
    //             return '<a href="' . route('products.show', $row->id) . '" class="btn btn-sm btn-dark">Show</a>';
    //             // <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-dark">SHOW</a>
    //         })
    //         ->rawColumns(['image','description','actions'])
    //         ->make(true);
    // }

    // public function getProductsData(Request $request)
    // {
    //     // Tentukan jumlah data per halaman sesuai dengan permintaan
    //     $length = $request->input('length', 10); // Default 10 data per halaman
    //     $start = $request->input('start', 0); // Dari data mana memulai
    //     $page = ($start / $length) + 1; // Tentukan halaman berdasarkan start dan length
    
    //     // Ambil data produk dengan pagination
    //     $products = Product::select('id', 'title', 'price', 'stock', 'file', 'image')
    //         ->paginate($length, ['*'], 'page', $page);
    
    //     // Dapatkan role pengguna yang sedang login
    //     $role = Auth::guard('api')->user()->role;
    
    //     // Kirimkan respons JSON yang sesuai dengan struktur DataTables
    //     return response()->json([
    //         'draw' => $request->input('draw'),
    //         'recordsTotal' => $products->total(),
    //         'recordsFiltered' => $products->total(),
    //         'data' => $products->map(function($product) use($role) {
    //             return [
    //                 'image' => '<img src="' . asset('/storage/products/' . $product->image) . '" style="width: 100px; height: auto;">',
    //                 'title' => $product->title,
    //                 'price' => 'Rp. ' . number_format($product->price, 2, ',', '.'),
    //                 'stock' => $product->stock,
    //                 'file' => $product->file, // Menampilkan nama file tanpa tautan
    //                 'actions' => $this->getActionButtons($product, $role)
    //             ];
    //         }),
    //     ]);
    // }
    

    //     // Method untuk menampilkan tombol sesuai dengan role
    //     private function getActionButtons($product, $role)
    //     {
    //         if ($role === 'admin') {
    //             return '
    //                 <a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-secondary">Show</a>
    //                 <a href="' . route('products.edit', $product->id) . '" class="btn btn-success btn-sm">Edit</a>
    //                 <form action="' . route('products.destroy', $product->id) . '" method="POST" style="display:inline;">
    //                     ' . csrf_field() . method_field('DELETE') . '
    //                     <button type="submit" class="btn btn-danger btn-sm delete-product" data-id="' . $product->id .'">Delete</button>
    //                 </form>';
    //         }
    
    //         return '<a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-dark">Show</a>';
    //     }

    // public function getProductsData(Request $request)
    // {
    //     // Tentukan jumlah data per halaman sesuai dengan permintaan
    //     $length = $request->input('length', 5); // Default 5 data per halaman
    //     $start = $request->input('start', 0); // Dari data mana memulai
    //     $page = ($start / $length) + 1; // Tentukan halaman berdasarkan start dan length

    //     // Ambil data produk dengan pagination
    //     $products = Product::select('id', 'title', 'price', 'stock', 'file', 'image')
    //         ->paginate($length, ['*'], 'page', $page);

    //     // Dapatkan role pengguna yang sedang login
    //     $role = Auth::guard('api')->user()->role;

    //     // Kirimkan respons JSON yang sesuai dengan struktur untuk manual pagination
    //     return response()->json([
    //         'total' => $products->total(), // Total records in the database
    //         'current_page' => $products->currentPage(), // Current page number
    //         'last_page' => $products->lastPage(), // Total number of pages
    //         'data' => $products->map(function ($product) use ($role) {
    //             return [
    //                 'image' => '<img src="' . asset('/storage/products/' . $product->image) . '" style="width: 100px; height: auto;">',
    //                 'title' => $product->title,
    //                 'price' => 'Rp. ' . number_format($product->price, 2, ',', '.'),
    //                 'stock' => $product->stock,
    //                 'file' => $product->file, // Menampilkan nama file tanpa tautan
    //                 'actions' => $this->getActionButtons($product, $role)
    //             ];
    //         }),
    //     ]);
    // }

    // // Method untuk menampilkan tombol sesuai dengan role
    // private function getActionButtons($product, $role)
    // {
    //     if ($role === 'admin') {
    //         return '
    //             <a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-secondary">Show</a>
    //             <a href="' . route('products.edit', $product->id) . '" class="btn btn-success btn-sm">Edit</a>
    //             <form action="' . route('products.destroy', $product->id) . '" method="POST" style="display:inline;">
    //                 ' . csrf_field() . method_field('DELETE') . '
    //                 <button type="submit" class="btn btn-danger btn-sm delete-product" data-id="' . $product->id .'">Delete</button>
    //             </form>';
    //     }

    //     return '<a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-dark">Show</a>';
    // }

    // public function getProductsData(Request $request)
    // {
    //     // Ambil jumlah data per halaman dari request
    //     $length = $request->input('length', 5); // Default 5 data per halaman
    //     $start = $request->input('start', 0); // Mulai dari data ke berapa
    //     $page = ($start / $length) + 1; // Tentukan halaman berdasarkan start dan length

    //     // Ambil data produk dengan pagination
    //     $products = Product::select('id', 'title', 'price', 'stock', 'file', 'image')
    //         ->paginate($length, ['*'], 'page', $page);

    //     // Dapatkan role pengguna yang sedang login
    //     $role = Auth::guard('api')->user()->role;

    //     // Kirimkan respons JSON sesuai dengan struktur untuk pagination manual
    //     return response()->json([
    //         'total' => $products->total(), // Total data di database
    //         'current_page' => $products->currentPage(), // Halaman saat ini
    //         'last_page' => $products->lastPage(), // Total halaman
    //         'data' => $products->map(function ($product) use ($role) {
    //             return [
    //                 'image' => '<img src="' . asset('/storage/products/' . $product->image) . '" style="width: 100px; height: auto;">',
    //                 'title' => $product->title,
    //                 'price' => 'Rp. ' . number_format($product->price, 2, ',', '.'),
    //                 'stock' => $product->stock,
    //                 'file' => $product->file, // Menampilkan nama file tanpa tautan
    //                 'actions' => $this->getActionButtons($product, $role)
    //             ];
    //         }),
    //     ]);
    // }

    // public function getProductsData(Request $request)
    // {
    //     // Ambil jumlah data per halaman dari request
    //     $length = $request->input('length', 5); // Default 5 data per halaman
    //     $start = $request->input('start', 0); // Mulai dari data ke berapa
    //     $page = ($start / $length) + 1; // Tentukan halaman berdasarkan start dan length

    //     // Ambil parameter untuk order by
    //     $orderByColumn = $request->input('order_by', 'id'); // Default column to order by
    //     $orderByDirection = $request->input('order_direction', 'asc'); // Default direction

    //     // Validasi kolom dan arah pengurutan
    //     $allowedColumns = ['id', 'title', 'price', 'stock']; // Add any other columns you want to allow for ordering
    //     if (!in_array($orderByColumn, $allowedColumns)) {
    //         $orderByColumn = 'id'; // Fallback to default column
    //     }
    //     if (!in_array(strtolower($orderByDirection), ['asc', 'desc'])) {
    //         $orderByDirection = 'asc'; // Fallback to default direction
    //     }

    //     // Ambil data produk dengan pagination dan order by
    //     $products = Product::select('id', 'title', 'price', 'stock', 'file', 'image')
    //         ->orderBy($orderByColumn, $orderByDirection)
    //         ->paginate($length, ['*'], 'page', $page);

    //     // Dapatkan role pengguna yang sedang login
    //     $role = Auth::guard('api')->user()->role;

    //     // Kirimkan respons JSON sesuai dengan struktur untuk pagination manual
    //     return response()->json([
    //         'total' => $products->total(), // Total data di database
    //         'current_page' => $products->currentPage(), // Halaman saat ini
    //         'last_page' => $products->lastPage(), // Total halaman
    //         'data' => $products->map(function ($product) use ($role) {
    //             return [
    //                 'image' => '<img src="' . asset('/storage/products/' . $product->image) . '" style="width: 100px; height: auto;">',
    //                 'title' => $product->title,
    //                 'price' => 'Rp. ' . number_format($product->price, 2, ',', '.'),
    //                 'stock' => $product->stock,
    //                 'file' => $product->file, // Menampilkan nama file tanpa tautan
    //                 'actions' => $this->getActionButtons($product, $role)
    //             ];
    //         }),
    //     ]);
    // }

    // public function getProductsData(Request $request)
    // {
    //     $length = $request->input('length', 5);
    //     $start = $request->input('start', 0);
    //     $page = ($start / $length) + 1;

    //     $orderByColumn = $request->input('order_by', 'id');
    //     $orderByDirection = $request->input('order_direction', 'asc');

    //     $allowedColumns = ['id', 'title', 'price', 'stock'];
    //     if (!in_array($orderByColumn, $allowedColumns)) {
    //         $orderByColumn = 'id';
    //     }
    //     if (!in_array(strtolower($orderByDirection), ['asc', 'desc'])) {
    //         $orderByDirection = 'asc';
    //     }

    //     $query = Product::select('id', 'title', 'price', 'stock', 'file', 'image')
    //         ->orderBy($orderByColumn, $orderByDirection);

    //     if ($request->filled('search')) {
    //         $query->where('title', 'like', '%' . $request->input('search') . '%');
    //     }

    //     $products = $query->paginate($length, ['*'], 'page', $page);
    //     $role = Auth::guard('api')->user()->role;

    //     return response()->json([
    //         'total' => $products->total(),
    //         'current_page' => $products->currentPage(),
    //         'last_page' => $products->lastPage(),
    //         'data' => $products->map(function ($product) use ($role) {
    //             return [
    //                 'image' => '<img src="' . asset('/storage/products/' . $product->image) . '" style="width: 100px; height: auto;">',
    //                 'title' => $product->title,
    //                 'price' => 'Rp. ' . number_format($product->price, 2, ',', '.'),
    //                 'stock' => $product->stock,
    //                 'file' => $product->file,
    //                 'actions' => $this->getActionButtons($product, $role)
    //             ];
    //         }),
    //     ]);
    // }

    public function getProductsData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $search = $request->input('search');

        
        $products = Product::where('title', 'like', '%' . $search . '%')->orderBy($sortBy, $sortDirection)->paginate($perPage);

        $role = Auth::guard('api')->user()->role;

        return response()->json([
            'data' => $products->map(function ($product) use ($role) {
                return [
                    'id'            => $product->id,
                    'image'        => '<img src="' . asset('storage/products/' . $product->image) . '" style="width: 100px; height: auto;">',
                    'title'         => $product->title,
                    'price'         => 'Rp. ' . number_format($product->price, 2, ',', '.'),
                    'stock'         => $product->stock,
                    'file'          => $product->file,
                    'actions'       => $this->getActionButtons($product, $role)
                ];
            }),
            'pagination' => [
                'total'         => $products->total(),
                'per_page'      => $products->perPage(),
                'current_page'  => $products->currentPage(),
                'last_page'     => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ]
        ], 200);
    }



    // Method untuk menampilkan tombol sesuai role
    // private function getActionButtons($product, $role)
    // {
    //     if ($role === 'admin') {
    //         return '
    //             <a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-secondary">Show</a>
    //             <a href="' . route('products.edit', $product->id) . '" class="btn btn-success btn-sm">Edit</a>
    //             <form action="' . route('products.destroy', $product->id) . '" method="POST" style="display:inline;">
    //                 ' . csrf_field() . method_field('DELETE') . '
    //                 <button type="submit" class="btn btn-danger btn-sm delete-product" data-id="' . $product->id .'">Delete</button>
    //             </form>';
    //     }

    //     return '<a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-dark">Show</a>';
    // }

    private function getActionButtons($product, $role) {
        if ($role === 'admin') {
            return '
                <a href="'. route('products.show', $product->id) .'" class="btn btn-sm btn-secondary">Show</a>
                <a href="'. route('products.edit', $product->id) .'" class="btn btn-success btn-sm">Edit</a>
                <button type="submit" class="btn btn-danger btn-sm delete-product" data-id="' . $product->id .'">Delete</button>
            ';
        }

        return '<a href="'. route('products.show', $product->id) .'" class="btn btn-sm btn-dark">Show</a>';
    }



    // public function store(Request $request)
    // {
    //     Log::info('Updating product', $request->all());

    //     $request->validate([
    //         'image'        => 'required|image|mimes:jpeg,jpg,png|max:2048',
    //         'title'         => 'required|min:5',
    //         'description'   => 'required|min:10',
    //         'price'         => 'required|numeric',
    //         'stock'         => 'required|numeric',
    //         'document'      => 'required|mimes:pdf,doc,docx|max:5120',
    //     ]);
    //     try {

    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $image->hashName());

    //         $document = $request->file('document');
    //         $documentName = Str::uuid() . '.' . $document->getClientOriginalExtension(); // UUID dengan ekstensi file
    //         $document->storeAs('public/products/documents', $documentName);

    //         $product = Product::create([
    //             'image'            => $image->hashName(),
    //             'title'             => $request->title,
    //             'description'       => $request->description,
    //             'price'             => $request->price,
    //             'stock'             => $request->stock,
    //             'document'          => $documentName,
    //         ]);

    //         return response()->json([
    //             'message' => 'Data Berhasil Disimpan!',
    //             'data' => $product,
    //         ], 201); // HTTP status 201 Created

    //     } catch (\Exception $e) {
    //         Log::error('Error storing product: '.$e->getMessage()); // Log detail kesalahan
        
    //         return response()->json([
    //             'message' => 'Data Gagal Disimpan!',
    //             'error' => $e->getMessage(), // Menambahkan detail kesalahan
    //         ], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     Log::info('Updating product', $request->all());

    //     $request->validate([
    //         'image'        => 'required|image|mimes:jpeg,jpg,png|max:2048',
    //         'title'         => 'required|min:5',
    //         'description'   => 'required|min:10',
    //         'price'         => 'required|numeric',
    //         'stock'         => 'required|numeric',
    //         'document'      => 'required|mimes:pdf,doc,docx|max:5120',
    //     ]);
    //     try {
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $image->hashName());

    //         $document = $request->file('document');
    //         if ($document) { // Periksa apakah ada dokumen
    //             $documentName = Str::uuid() . '.' . $document->getClientOriginalExtension(); // UUID dengan ekstensi file
    //             $document->storeAs('public/products/documents', $documentName);
    //         } else {
    //             throw new \Exception('Document file is required.'); // Jika tidak ada dokumen, throw exception
    //         }

    //         $product = Product::create([
    //             'image'            => $image->hashName(),
    //             'title'            => $request->title,
    //             'description'      => $request->description,
    //             'price'            => $request->price,
    //             'stock'            => $request->stock,
    //             'document'         => $documentName, // Pastikan documentName diisi
    //         ]);

    //         return response()->json([
    //             'message' => 'Data Berhasil Disimpan!',
    //             'data' => $product,
    //         ], 201); // HTTP status 201 Created

    //     } catch (\Exception $e) {
    //         Log::error('Error storing product: '.$e->getMessage()); // Log detail kesalahan
        
    //         return response()->json([
    //             'message' => 'Data Gagal Disimpan!',
    //             'error' => $e->getMessage(), // Menambahkan detail kesalahan
    //         ], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     Log::info('Updating product', $request->all());

    //     $request->validate([
    //         'image'        => 'required|image|mimes:jpeg,jpg,png|max:2048',
    //         'title'         => 'required|min:5',
    //         'description'   => 'required|min:10',
    //         'price'         => 'required|numeric',
    //         'stock'         => 'required|numeric',
    //         'file'          => 'required|mimes:pdf,doc,docx|max:5120',
    //     ]);
    //     try {

    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $image->hashName());

    //         $file = $request->file('file');
    //         $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension(); // UUID dengan ekstensi file
    //         $file->storeAs('public/products/documents', $fileName);

    //         $product = Product::create([
    //             'image'            => $image->hashName(),
    //             'title'             => $request->title,
    //             'description'       => $request->description,
    //             'price'             => $request->price,
    //             'stock'             => $request->stock,
    //             'file'              => $fileName,
    //         ]);

    //         return response()->json([
    //             'message' => 'Data Berhasil Disimpan!',
    //             'data' => $product,
    //         ], 201); // HTTP status 201 Created

    //     } catch (\Exception $e) {
    //         Log::error('Error storing product: '.$e->getMessage()); // Log detail kesalahan
        
    //         return response()->json([
    //             'message' => 'Data Gagal Disimpan!',
    //             'error' => $e->getMessage(), // Menambahkan detail kesalahan
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        Log::info('Updating product', $request->all());

        $request->validate([
            'image'        => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric',
            'file'          => 'required|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            // Simpan produk tanpa image dan file terlebih dahulu
            $product = Product::create([
                'title'        => $request->title,
                'description'  => $request->description,
                'price'        => $request->price,
                'stock'        => $request->stock,
                'image'       => 'temp.jpg', // Nilai sementara untuk kolom image
            ]);

            // Menyimpan image dengan menambahkan ID produk
            // $image = $request->file('image');
            // $imageName = $product->id . '_' . $image->hashName();
            // $image->storeAs('public/products', $imageName);
            $image = $request->file('image');
            $imageName = $product->id . '_' . $image->hashName();
            $image->storeAs('public/products', $imageName);

            $product->update(['image' => $imageName]);


            // Menyimpan file dengan nama yang mengandung UUID
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/products/documents', $fileName);

            // Update produk dengan nama file image dan dokumen
            $product->update([
                'image' => $imageName,
                'file'  => $fileName,
            ]);

            return response()->json([
                'message' => 'Data Berhasil Disimpan!',
                'data'    => $product,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing product: ' . $e->getMessage());

            return response()->json([
                'message' => 'Data Gagal Disimpan!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'image'        => 'required|image|mimes:jpeg,jpg,png|max:2048',
    //         'title'         => 'required|min:5',
    //         'description'   => 'required|min:10',
    //         'price'         => 'required|numeric',
    //         'stock'         => 'required|numeric',
    //         'document'      => 'required|mimes:pdf,doc,docx|max:5120',
    //     ]);

    //     try {
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $image->hashName());

    //         $document = $request->file('document');
    //         $documentName = Str::uuid() . '.' . $document->getClientOriginalExtension(); 
    //         $document->storeAs('public/products/documents', $documentName);

    //         $product = Product::create([
    //             'image'            => $image->hashName(),
    //             'title'             => $request->title,
    //             'description'       => $request->description,
    //             'price'             => $request->price,
    //             'stock'             => $request->stock,
    //             'document'          => $documentName,
    //         ]);

    //         return response()->json([
    //             'message' => 'Data Berhasil Disimpan!',
    //             'data' => $product,
    //         ], 201);

    //     } catch (\Exception $e) {
    //         \Log::error('Error storing product: ' . $e->getMessage()); // Log the error message
    //         return response()->json([
    //             'message' => 'Data Gagal Disimpan!',
    //             'error' => $e->getMessage(), // Return error for debugging
    //         ], 500);
    //     }
    // }

    // public function edit(Request $request, $id)
    // {
    //     // Validasi form input
    //     $request->validate([
    //         'image'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
    //         'file'          => 'nullable|mimes:pdf,doc,docx|max:2048',  // Validasi dokumen
    //         'title'         => 'required|min:5',
    //         'description'   => 'required|min:10',
    //         'price'         => 'required|numeric',
    //         'stock'         => 'required|numeric'
    //     ]);

    //     // Dapatkan produk berdasarkan ID
    //     $product = Product::findOrFail($id);

    //     // Proses upload gambar jika ada
    //     if ($request->hasFile('image')) {
    //         // Upload gambar baru
    //         $image = $request->file('image');
    //         $imageName = $image->hashName();
    //         $image->storeAs('public/products', $imageName);

    //         // Hapus gambar lama
    //         Storage::delete('public/products/'.$product->image);

    //         // Update produk dengan gambar baru
    //         $product->image = $imageName;
    //     }

    //     // Proses upload dokumen jika ada
    //     if ($request->hasFile('file')) {
    //         // Upload dokumen baru
    //         $file = $request->file('file');
    //         $fileName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
    //         $file->storeAs('public/products/documents', $fileName);

    //         // Hapus dokumen lama jika ada
    //         if ($product->file) {
    //             Storage::delete('public/products/documents/'.$product->file);
    //         }

    //         // Update produk dengan dokumen baru
    //         $product->file = $fileName;
    //     }

    //     // Update produk dengan data lain
    //     $product->title = $request->title;
    //     $product->description = $request->description;
    //     $product->price = $request->price;
    //     $product->stock = $request->stock;
    //     $product->save();

    //     // Mengembalikan response dalam bentuk JSON
    //     return response()->json([
    //         'message' => 'Product successfully updated!',
    //         'product' => $product,
    //     ], 200);
    // }
    
    public function update (Request $request, $id)
    {
        // Log::info('Updating product', $request->all());
        Log::info('Updating product', ['id' => $id, 'request_data' => $request->all()]);

        $request->validate([
            'image'        => 'image|mimes:jpeg,jpg,png,img|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric',
            'file'          => 'mimes:pdf,doc,docx|max:5120',
        ]);

        $product = Product::findOrFail($id);

        // if ($request->file('image')) {
        //     // delete
        //     Storage::delete('public/products/'.$product->image);

        //     $image = $request->file('image');
        //     $image->storeAs('/public/products', $image->hashName());

        //     $product->image = $image->hashName();

        // }

        if ($request->file('image')) {
            // Hapus gambar lama
            Storage::delete('public/products/' . $product->image);
        
            $image = $request->file('image');
            $imageName = $product->id . '_' . $image->hashName(); // Menambahkan ID produk pada nama file
            $image->storeAs('public/products', $imageName);
        
            $product->image = $imageName;
        }

        if ($request->file('file')) {
            Storage::delete('public/products/documents/'.$product->file);

            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension(); // UUID dengan ekstensi file
            $file->storeAs('public/products/documents', $fileName);


            $product->file = $fileName;

        }

        $product->update([
            'title'                 => $request->title,
            'description'           => $request->description,
            'price'                 => $request->price,
            'stock'                 => $request->stock,
        ]);

        return response()->json([
            'message'   => 'Data berhasil diperbarui',
            'data'      => $product,      
        ], 200);
    }


    public function destroy($id)
    {
        $product =  Product::findOrFail($id);

        Storage::delete('public/products/'. $product->image);
        Storage::delete('public/products/documents'. $product->file);

        $product->delete();

        return response()->json([
            'message'   => 'Data berhasil dihapus'
        ], 200);
    }



}
