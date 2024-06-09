@extends('layouts.adm-main')

@section('content')
    <div class="container">
        <h1>Detail Barang Keluar</h1>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tanggal Keluar: {{ $barangkeluar->tgl_keluar }}</h5>
                <p class="card-text">Jumlah Keluar: {{ $barangkeluar->qty_keluar }}</p>
                
                <h5 class="mt-4">Detail Barang</h5>
                <ul>
                    <li>Merk: {{ $barangkeluar->barang->merk }}</li>
                    <li>Seri: {{ $barangkeluar->barang->seri }}</li>
                    <li>Spesifikasi: {{ $barangkeluar->barang->spesifikasi }}</li>
                    <li>Stok: {{ $barangkeluar->barang->stok }}</li>
                    <li>Kategori: {{ $barangkeluar->barang->kategori->deskripsi }}</li>
                </ul>
                
                <a href="{{ route('barangkeluar.index') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>
@endsection