<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    // Menggunakan trait ValidatesRequests untuk memudahkan validasi
    use \Illuminate\Foundation\Validation\ValidatesRequests;

    /**
     * Menampilkan daftar barang
     */
    public function index(Request $request)
    {
        // Mengambil data Barang bersama Kategori terkait, diurutkan berdasarkan waktu terbaru dan dipaginasi setiap 10 item
        $rsetBarang = Barang::with('kategori')->latest()->paginate(10);

        // Mengembalikan view Barang.index dengan data barang dan penomoran halaman
        return view('Barang.index', compact('rsetBarang'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Menampilkan form untuk membuat barang baru
     */
    public function create()
    {
        // Mengambil semua data kategori
        $akategori = Kategori::all();
        
        // Mengembalikan view Barang.create dengan data kategori
        return view('Barang.create', compact('akategori'));
    }

    /**
     * Menyimpan barang baru ke database
     */
    public function store(Request $request)
    {
        // Memvalidasi input request
        $this->validate($request, [
            'merk' => 'required',
            'seri' => 'required',
            'spesifikasi' => 'required',
            'kategori_id' => 'required|not_in:blank',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // barangsamaerror
        $existingBarang = Barang::where('merk', $request->merk)
                            ->where('seri', $request->seri)
                            ->where('spesifikasi', $request->spesifikasi)
                            ->first();

        if ($existingBarang) {
            // Redirect back with an error message
            return redirect()->back()->withErrors(['error' => 'Barang dengan merk, seri, dan spesifikasi yang sama sudah ada.']);
        }

        // Mengupload gambar ke public/foto dan menyimpan nama hash-nya
        $foto = $request->file('foto');
        $foto->storeAs('public/foto', $foto->hashName());

        // Membuat record baru di tabel Barang
        Barang::create([
            'merk' => $request->merk,
            'seri' => $request->seri,
            'spesifikasi' => $request->spesifikasi,
            'kategori_id' => $request->kategori_id,
            'foto' => $foto->hashName()
        ]);

        // Redirect ke index dengan pesan sukses
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Menampilkan detail barang
     */
    public function show(string $id)
    {
        // Mengambil data Barang berdasarkan ID
        $rsetBarang = Barang::find($id);

        // Mengembalikan view Barang.show dengan data barang
        return view('Barang.show', compact('rsetBarang'));
    }

    /**
     * Menampilkan form untuk mengedit barang
     */
    public function edit(string $id)
    {
        // Mengambil semua data kategori dan data barang berdasarkan ID
        $akategori = Kategori::all();
        $rsetBarang = Barang::find($id);
        $selectedKategori = Kategori::find($rsetBarang->kategori_id);

        // Mengembalikan view Barang.edit dengan data barang, kategori, dan kategori terpilih
        return view('Barang.edit', compact('rsetBarang', 'akategori', 'selectedKategori'));
    }

    /**
     * Memperbarui barang di database
     */
    public function update(Request $request, string $id)
    {
        // Memvalidasi input request
        $this->validate($request, [
            'merk' => 'required',
            'seri' => 'required',
            'spesifikasi' => 'required',
            'stok' => 'required',
            'kategori_id' => 'required|not_in:blank',
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Mengambil data barang berdasarkan ID
        $rsetBarang = Barang::find($id);

        // Jika ada gambar baru yang diupload, upload gambar baru, hapus gambar lama, dan perbarui data barang termasuk gambar baru
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $foto->storeAs('public/foto', $foto->hashName());
            Storage::delete('public/foto/' . $rsetBarang->foto);
            $rsetBarang->update([
                'merk' => $request->merk,
                'seri' => $request->seri,
                'spesifikasi' => $request->spesifikasi,
                'stok' => $request->stok,
                'kategori_id' => $request->kategori_id,
                'foto' => $foto->hashName()
            ]);
        } else {
            // Jika tidak ada gambar baru, hanya perbarui data barang tanpa mengubah gambar
            $rsetBarang->update([
                'merk' => $request->merk,
                'seri' => $request->seri,
                'spesifikasi' => $request->spesifikasi,
                'stok' => $request->stok,
                'kategori_id' => $request->kategori_id
            ]);
        }

        // Redirect ke index dengan pesan sukses
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * Menghapus barang dari database
     */
    public function destroy(string $id)
    {
        // Mengecek apakah barang terkait dengan transaksi masuk, keluar, atau masih memiliki stok
        // $barangmasuk = BarangMasuk::where('barang_id', $id)->exists();
        // $barangkeluar = BarangKeluar::where('barang_id', $id)->exists();
        // $stokBarang = Barang::where('id', $id)->where('stok', '>', 0)->exists();

        // // Jika ada transaksi terkait atau stok tidak kosong, redirect ke index dengan pesan error
        // if ($barangmasuk || $barangkeluar || $stokBarang) {
        //     return redirect()->route('Barang.index')->with(['error' => 'Barang tidak dapat dihapus karena terkait dengan transaksi atau masih memiliki stok.']);
        // } else {
        //     // Jika tidak ada, hapus barang dan redirect ke index dengan pesan sukses
        //     $barangSeed = Barang::find($id);
        //     $barangSeed->delete();
            $barang = Barang::findOrFail($id);

            //delete image
            Storage::delete('public/foto/'. $barang->image);
    
            //delete product
            $barang->delete();

            return redirect()->route('barang.index')->with(['success' => 'Barang berhasil dihapus.']);
    }
}
