@extends('layouts.adm-main')

@section('content')
    <div class="container">
        <h1>Edit Barang Keluar</h1>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('barangkeluar.update', $barangkeluar->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="tgl_keluar">Tanggal Keluar:</label>
                        <input type="date" name="tgl_keluar" id="tgl_keluar" class="form-control" value="{{ $barangkeluar->tgl_keluar }}" required>
                    </div>

                    <div class="form-group">
                        <label for="qty_keluar">Jumlah Keluar:</label>
                        <input type="number" name="qty_keluar" id="qty_keluar" class="form-control" value="{{ $barangkeluar->qty_keluar }}" required>
                    </div>

                    <!-- Jika ingin mengganti barang yang keluar -->
                    <div class="form-group">
                        <label for="barang_id">Barang:</label>
                        <select name="barang_id" id="barang_id" class="form-control" required>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}" {{ $barangkeluar->barang_id == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->merk }} - {{ $barang->seri }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('barangkeluar.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection