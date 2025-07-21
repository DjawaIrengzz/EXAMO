<?php

namespace App\Http\Controllers;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Barang::query();

        //filtering
        if($request->has('nama')){
            $query->where('nama', 'like', '%' . $request->nama .'%');
        }
        if($request->has('min_harga')){
            $query->where('harga','>=' , $request->min_harga, '%');
        }
        if($request->has('max_harga')){
            $query->where('harga', '<=', $request->max_harga, '%');
        }

        //sorting
        $sortBy= $request->get('sortBy', 'nama');
        $sortOrder= $request->get('sortOder', 'asc');
        $allowSorts = ['nama', 'harga', 'stock', 'created_at'];
        if(in_array($sortBy, $allowSorts)){
            $query->orderBy($sortBy, $sortOrder);
        }

        //pagination
        $perPage = (int) $request->get('per_page', 10);
        $barang = $query->paginate($perPage);

        return response()->json([
            'message' => 'berhasil',
            'data' => $barang
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama'=> 'required|string|max:50',
            'harga' =>'required|integer',
            'deskripsi' => 'required|string|max:300',
            'stock' => 'required|integer'
        ]);
        $barang = Barang::create($validate);
        
        return response()->json([
            'message' => 'berhasil',
            'data' => $barang
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barang = Barang::findOrFail($id);
        return response()->json([
            'message' => 'berhasil',
            'data' => $barang,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang    $barang)
    {
        $validate = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga'=> 'required|integer'
        ]);

        $barang->update($validate);
       
        return response()->json([
            'message'=> 'berhasil',
            'data' => $barang,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return response()->json([
            'message'=> 'berhasil',
            'data'=> $barang
        ]);
    }
}
