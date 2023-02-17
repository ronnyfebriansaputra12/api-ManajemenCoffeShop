<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Inventori;


class InventoriController extends Controller
{
    public function index()
    {
        //
        $inventori = Inventori::all();
        return response()->json([
            'success' => true,
            'data' => $inventori
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'nama_barang' => 'required',
            'stok' => 'required',
            'harga' => 'required',
            'satuan' => 'required',
            'kd_barang' => 'required|unique:inventoris',
        ]);

        $inventori = new Inventori();

        $inventori->nama_barang = $request->nama_barang;
        $inventori->stok = $request->stok;
        $inventori->harga = $request->harga;
        $inventori->satuan = $request->satuan;
        $inventori->kd_barang = $request->kd_barang;
        $inventori->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Barang Berhasil Ditambahkan',
            'data' => $inventori
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $inventori = Inventori::find($id);
        return response()->json($inventori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $validate = $request->validate([
            'nama_barang' => 'required',
            'stok' => 'required',
            'harga' => 'required',
            'satuan' => 'required',
            'kd_barang' => 'required',
        ]);


        $result = Inventori::where('id', $id)->update($validate);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil diubah',
            'data' => $result
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $inventori = Inventori::find($id);
        $inventori->delete();
        return response()->json('Barang deleted successfully!');
    }
}
