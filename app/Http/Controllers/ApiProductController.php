<?php

namespace App\Http\Controllers;

//import model product
use App\Models\Product; 

use Yajra\DataTables\Facades\DataTables;

use App\Models\User;

//import return type View
use Illuminate\View\View;

//import return type redirectResponse
use Illuminate\Http\Request;

//import Http Request
use Illuminate\Http\RedirectResponse;

//import Facades Storage
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

use Tymon\JWTAuth\Facades\JWTAuth;

class ApiProductController extends Controller
{
    public function role() 
    {
        // $user = auth()->guard('api')->user();
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 401);
        }

        $role = $user->role;

        return response()->json([
            'success' => true,
            'role' => $role
        ], 200);

    }

    public function getProductsData()
    {
        // kalo milih2
        // $products = Product::select('id', 'title', 'description', 'price', 'stock', 'file')->get();
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
                    return '<a href="' . route('products.show', $row->id) . '" class="btn btn-sm btn-dark">Show</a>
                    <a href="' . route('products.edit', $row->id) . '" class="btn btn-warning btn-sm">Edit</a>
                    <form action="' . route('products.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>';
                }
                return '<a href="' . route('products.show', $row->id) . '" class="btn btn-sm btn-dark">Show</a>';
                // <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-dark">SHOW</a>
            })
            ->rawColumns(['image','description','actions'])
            ->make(true);
    }

    // public function store(Request $request): JsonResponse
    // {
    //     // Validasi form input
    //     $validator = Validator::make($request->all(), [
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'document' => 'required|mimes:pdf,doc,docx|max:2048',  // validasi untuk dokumen
    //         'title' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'price' => 'required|numeric',
    //         'stock' => 'required|numeric',
    //     ]);

    //     // Cek validasi
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     // Cek apakah user memiliki role admin
    //     // if (Auth::user()->role !== 'admin') {
    //     //     return response()->json([
    //     //         'status' => 'error',
    //     //         'message' => 'Unauthorized action.',
    //     //     ], 403);
    //     // }

    //     // Menangani upload file gambar
    //     $image = $request->file('image');
    //     $imagePath = $image->storeAs('public/products', $image->hashName());

    //     // Menangani upload file dokumen
    //     $document = $request->file('document');
    //     $documentName = Str::uuid()->toString() . '.' . $document->getClientOriginalExtension();
    //     $documentPath = $document->storeAs('products/documents', $documentName, 'public');

    //     // Menyimpan data produk ke database
    //     $product = new Product();
    //     $product->image = $image->hashName();  // simpan path gambar
    //     $product->document = $documentName;  // simpan path dokumen
    //     $product->title = $request->title;
    //     $product->description = $request->description;
    //     $product->price = $request->price;
    //     $product->stock = $request->stock;
    //     $product->save();

    //     // Response JSON berhasil
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Product berhasil ditambahkan',
    //         'product' => $product
    //     ], 201);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'image'        => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric',
            'document'      => 'required|mimes:pdf,doc,docx|max:5120',
        ]);
        try {

            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $document = $request->file('document');
            $documentName = Str::uuid() . '.' . $document->getClientOriginalExtension(); // UUID dengan ekstensi file
            $document->storeAs('public/products/documents', $documentName);

            $product = Product::create([
                'image'            => $image->hashName(),
                'title'             => $request->title,
                'description'       => $request->description,
                'price'             => $request->price,
                'stock'             => $request->stock,
                'document'          => $documentName,
            ]);

            return response()->json([
                'message' => 'Data Berhasil Disimpan!',
                'data' => $product,
            ], 201); // HTTP status 201 Created

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Data Gagal Disimpan!',
            ], 500);
        }
    }


}
