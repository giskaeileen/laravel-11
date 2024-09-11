<?php

namespace App\Http\Controllers;

//import model product
use App\Models\Product; 

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

class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index() : View
    {
        //get all products
        $products = Product::latest()->paginate(10);
        $role = Auth::user()->role;

        //render view with products
        // return view('products.index', compact('products'));
        return view('products.index', compact('products', 'role'));
    }

    /**
     * create
     *
     * @return View
     */
    public function create(): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('products.create');
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     //validate form
    //     $request->validate([
    //         'image'         => 'required|image|mimes:jpeg,jpg,png|max:2048',
    //         'title'         => 'required|min:5',
    //         'description'   => 'required|min:10',
    //         'price'         => 'required|numeric',
    //         'stock'         => 'required|numeric'
    //     ]);

    //     //upload image
    //     $image = $request->file('image');
    //     $image->storeAs('public/products', $image->hashName());

    //     //create product
    //     Product::create([
    //         'image'         => $image->hashName(),
    //         'title'         => $request->title,
    //         'description'   => $request->description,
    //         'price'         => $request->price,
    //         'stock'         => $request->stock
    //     ]);

    //     //redirect to index
    //     return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
    // }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Validasi form input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'document' => 'required|mimes:pdf,doc,docx|max:2048',  // validasi untuk dokumen
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // Menangani upload file gambar
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        // Menangani upload file dokumen
        if ($request->hasFile('document')) {
            $document = $request->file('document');
            $documentName = Str::uuid()->toString() . '.' . $document->getClientOriginalExtension();
            $documentPath = $document->storeAs('products/documents', $documentName, 'public');
        }

        // Menyimpan data produk ke database
        $product = new Product();
        $product->image = $image->hashName();  // simpan path gambar
        $product->document = $documentName;  // simpan path dokumen
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->save();

        // Redirect atau response setelah menyimpan
        return redirect()->route('products.index')->with('success', 'Product berhasil ditambahkan');
    }
    
    /**
     * show
     *
     * @param  mixed $id
     * @return View
     */
    public function show(string $id): View
    {
        //get product by ID
        $product = Product::findOrFail($id);

        //render view with product
        return view('products.show', compact('product'));
    }
    
    /**
     * edit
     *
     * @param  mixed $id
     * @return View
     */
    public function edit(string $id): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        //get product by ID
        $product = Product::findOrFail($id);

        //render view with product
        return view('products.edit', compact('product'));
    }
        
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return RedirectResponse
     */
    // public function update(Request $request, $id): RedirectResponse
    // {
    //     //validate form
    //     $request->validate([
    //         'image'         => 'image|mimes:jpeg,jpg,png|max:2048',
    //         'title'         => 'required|min:5',
    //         'description'   => 'required|min:10',
    //         'price'         => 'required|numeric',
    //         'stock'         => 'required|numeric'
    //     ]);

    //     //get product by ID
    //     $product = Product::findOrFail($id);

    //     //check if image is uploaded
    //     if ($request->hasFile('image')) {

    //         //upload new image
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $image->hashName());

    //         //delete old image
    //         Storage::delete('public/products/'.$product->image);

    //         //update product with new image
    //         $product->update([
    //             'image'         => $image->hashName(),
    //             'title'         => $request->title,
    //             'description'   => $request->description,
    //             'price'         => $request->price,
    //             'stock'         => $request->stock
    //         ]);

    //     } else {

    //         //update product without image
    //         $product->update([
    //             'title'         => $request->title,
    //             'description'   => $request->description,
    //             'price'         => $request->price,
    //             'stock'         => $request->stock
    //         ]);
    //     }

    //     //redirect to index
    //     return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah!']);
    // }

    public function update(Request $request, $id): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        // Validasi form input
        $request->validate([
            'image'         => 'image|mimes:jpeg,jpg,png|max:2048',
            'document'      => 'mimes:pdf,doc,docx|max:2048',  // Validasi dokumen
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        // Dapatkan produk berdasarkan ID
        $product = Product::findOrFail($id);

        // Proses upload gambar
        if ($request->hasFile('image')) {
            // Upload gambar baru
            $image = $request->file('image');
            $imageName = $image->hashName();
            $image->storeAs('public/products', $imageName);

            // Hapus gambar lama
            Storage::delete('public/products/'.$product->image);

            // Update produk dengan gambar baru
            $product->image = $imageName;
        }

        // Proses upload dokumen
        if ($request->hasFile('document')) {
            // Upload dokumen baru
            $document = $request->file('document');
            $documentName = Str::uuid()->toString() . '.' . $document->getClientOriginalExtension();
            $document->storeAs('public/products/documents', $documentName);

            // Hapus dokumen lama jika ada
            if ($product->document) {
                Storage::delete('public/products/documents/'.$product->document);
            }

            // Update produk dengan dokumen baru
            $product->document = $documentName;
        }

        // Update produk dengan data lain
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->save();

        // Redirect ke index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    
    /**
     * destroy
     *
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        //get product by ID
        $product = Product::findOrFail($id);

        //delete image
        Storage::delete('public/products/'. $product->image);
        Storage::delete('public/products/documents'. $product->document);

        //delete product
        $product->delete();

        //redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}