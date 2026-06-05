@extends('layouts.app')

@section('title', 'Penilaian Produk - Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card card-round">
            <div class="card-body">
                <div class="text-muted small">Total Penilaian</div>
                <h3 class="mb-0">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card card-round">
            <div class="card-body">
                <div class="text-muted small">Rata-rata Rating</div>
                <h3 class="mb-0">
                    {{ number_format($stats['average'], 1) }}
                    <i class="fas fa-star text-warning" style="font-size: 1rem;"></i>
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-star me-2"></i>Penilaian dari Pelanggan
                    </div>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('admin.reviews.index') }}" class="d-flex gap-2">
                            <select name="rating" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Bintang</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                                @endfor
                            </select>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control form-control-sm" placeholder="Cari produk...">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Pelanggan</th>
                                <th>Rating</th>
                                <th>Komentar</th>
                                <th>No. Pesanan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    <td><small>{{ $review->created_at->format('d M Y, H:i') }}</small></td>
                                    <td>{{ $review->product->name ?? '-' }}</td>
                                    <td>{{ $review->user->name ?? '-' }}</td>
                                    <td style="white-space: nowrap;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </td>
                                    <td>{{ $review->comment ?: '-' }}</td>
                                    <td><small>{{ $review->order->order_number ?? '-' }}</small></td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                            onsubmit="return confirm('Hapus penilaian ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Belum ada penilaian dari pelanggan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
