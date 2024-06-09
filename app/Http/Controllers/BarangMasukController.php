<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Barang;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BarangMasukController extends Controller
{
    use ValidatesRequests;
    
    public function index()
    {
        $barangmasuks = BarangMasuk::with('barang')->paginate(10);

        return view('BarangMasuk.index', compact('barangmasuks'));
    }

    public function create()
    {
        $barangs = Barang::all();

        return view('BarangMasuk.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tgl_masuk' => 'required|date',
            'qty_masuk' => 'required|integer|min:1',
            'barang_id' => 'required|exists:barang,id',
        ]);

        $barangmasuk = BarangMasuk::create($request->all());

        // $barang = Barang::find($request->barang_id);
        // $barang->stok += $request->qty_masuk;
        // $barang->save();

        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Barang Masuk Berhasil Disimpan!']);
    }

    public function show($id)
    {
        $barangmasuk = BarangMasuk::findOrFail($id);

        return view('BarangMasuk.show', compact('barangmasuk'));
    }

    public function edit($id)
    {
        $barangmasuk = BarangMasuk::findOrFail($id);
        $barangs = Barang::all();

        return view('BarangMasuk.edit', compact('barangmasuk', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tgl_masuk' => 'required|date',
            'qty_masuk' => 'required|integer|min:1',
            'barang_id' => 'required|exists:barang,id',
        ]);

        $barangmasuk = BarangMasuk::findOrFail($id);
        $barangmasuk->update($request->all());

        // Update stok barang terkait
        // $barang = Barang::find($request->barang_id);
        // $barang->stok += $request->qty_masuk - $barangmasuk->qty_masuk;
        // $barang->save();

        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Barang Masuk Berhasil Diperbarui!']);
    }

    public function destroy($id)
    {
        $barangmasuk = BarangMasuk::findOrFail($id);
        $barangKeluarCount = BarangKeluar::where('barang_id', $barangmasuk->barang_id)
        ->where('tgl_keluar', '>=', $barangmasuk->tgl_masuk)
        ->count();

        // Jika ada catatan barangkeluar terkait, kembalikan pesan kesalahan
        if ($barangKeluarCount > 0) {
        return redirect()->route('barangmasuk.index')->withErrors(['error' => 'Data Barang Masuk tidak dapat dihapus karena ada Barang Keluar yang terkait.']);
        }
        // Mengurangi stok barang terkait
        // $barang = Barang::find($barangmasuk->barang_id);
        // $barang->stok -= $barangmasuk->qty_masuk;
        // $barang->save();
        $barangmasuk->delete();
        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Barang Masuk Berhasil Dihapus!']);
    }
}