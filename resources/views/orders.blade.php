@extends('layouts.app')

@section('title', 'Pesanan Saya - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Riwayat Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->orders && auth()->user()->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(auth()->user()->orders->latest()->get() as $order)
                                        <tr>
                                            <td>
                                                <strong>#{{ $order->id }}</strong>
                                            </td>
                                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                            <td>
                                                @switch($order->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Menunggu</span>
                                                        @break
                                                    @case('processing')
                                                        <span class="badge bg-info">Diproses</span>
                                                        @break
                                                    @case('shipped')
                                                        <span class="badge bg-primary">Dikirim</span>
                                                        @break
                                                    @case('delivered')
                                                        <span class="badge bg-success">Diterima</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">Dibatalkan</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt display-1 text-muted"></i>
                            <h4 class="mt-3">Belum Ada Pesanan</h4>
                            <p class="text-muted">Anda belum melakukan pesanan apa pun</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="bi bi-shop"></i> Mulai Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
main.py-4 {
    min-height: calc(100vh - 76px - 80px); /* Push footer to bottom */
}
</style>
@endpush
@endsection