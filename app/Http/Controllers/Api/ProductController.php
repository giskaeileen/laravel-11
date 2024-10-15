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

    public function getProductsData()
    {
        // kalo milih2
        $products = Product::select('id', 'title', 'description', 'price', 'stock', 'file')->get();
        //kalo butuh semua data tanpa milih
        $products = Product::all();
        $role = Auth::guard('api')->user()->role;
        return DataTables::of($products)
            ->addColumn('image', function($row) {
                return '<img src="' . asset('/storage/products/' . $row->image) . '" style="width: 100px; height: auto;">';
            })

            ->editColumn('price', function($product) {
                return 'Rp. ' . number_format($product->price, 2, ',', '.'); // Format harga dengan prefix
            })   
            
            ->addColumn('actions', function($row) use($role) {
                if ($role === 'admin') {
                    return '<a href="' . route('products.show', $row->id) . '" class="btn btn-sm btn-secondary">Show</a>
                    <a href="' . route('products.edit', $row->id) . '" class="btn btn-success btn-sm">Edit</a>
                    <form action="' . route('products.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm delete-product" data-id="' . $row->id .'">Delete</button>
                    </form>';
                }
                return '<a href="' . route('products.show', $row->id) . '" class="btn btn-sm btn-dark">Show</a>';
                // <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-dark">SHOW</a>
            })
            ->rawColumns(['image','description','actions'])
            ->make(true);
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

            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension(); // UUID dengan ekstensi file
            $file->storeAs('public/products/documents', $fileName);

            $product = Product::create([
                'image'            => $image->hashName(),
                'title'             => $request->title,
                'description'       => $request->description,
                'price'             => $request->price,
                'stock'             => $request->stock,
                'file'              => $fileName,
            ]);

            return response()->json([
                'message' => 'Data Berhasil Disimpan!',
                'data' => $product,
            ], 201); // HTTP status 201 Created

        } catch (\Exception $e) {
            Log::error('Error storing product: '.$e->getMessage()); // Log detail kesalahan
        
            return response()->json([
                'message' => 'Data Gagal Disimpan!',
                'error' => $e->getMessage(), // Menambahkan detail kesalahan
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
            'file'          => 'mimes:pdf|mimes:pdf,doc,docx|max:5120',
        ]);

        $product = Product::findOrFail($id);

        if ($request->file('image')) {
            // delete
            Storage::delete('public/products/'.$product->image);

            $image = $request->file('image');
            $image->storeAs('/public/products', $image->hashName());

            $product->image = $image->hashName();

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
