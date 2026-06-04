@extends('layouts.app')

@section('title', 'Tambah Resep Baru')

@section('content')

    {{-- ========== HERO ========== --}}
    <section class="page-hero" style="padding:2.5rem 0 2rem;">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb" style="font-size:0.82rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('recipes.index') }}" style="color:var(--cp-pink-dark);">
                            <i class="bi bi-house-heart me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color:var(--cp-muted);">Tambah Resep</li>
                </ol>
            </nav>
            <h1 class="page-hero-title" style="font-size:1.9rem;">
                <i class="bi bi-journal-plus me-2" style="color:var(--cp-pink-dark);"></i>Tambah Resep Baru
            </h1>
            <p class="page-hero-sub">Bagikan resep kue favoritmu ke CakePedia!</p>
        </div>
    </section>

    {{-- ========== FORM ========== --}}
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                {{-- Validation errors summary --}}
                @if ($errors->any())
                    <div class="alert-cp-error mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Mohon periksa kembali isian berikut:</strong>
                        </div>
                        <ul class="mb-0 ps-3" style="font-size:0.85rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('recipes.store') }}" method="POST" id="recipeForm">
                    @csrf

                    {{-- ===== SECTION: Informasi Dasar ===== --}}
                    <div class="cp-card mb-4">
                        <h5 style="margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                            <span style="font-size:1.3rem;"></span> Informasi Dasar
                        </h5>

                        {{-- Judul --}}
                        <div class="mb-4">
                            <label for="title" class="form-label-cp">
                                Judul Resep <span style="color:var(--cp-pink-dark);">*</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   class="form-control-cp w-100 @error('title') is-invalid @enderror"
                                   placeholder="cth: Croissant Mentega Klasik"
                                   value="{{ old('title') }}"
                                   maxlength="255">
                            @error('title')
                                <div class="invalid-feedback d-block" style="font-size:0.8rem; color:#C0392B;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-3">
                            <label for="category" class="form-label-cp">
                                Kategori <span style="color:var(--cp-pink-dark);">*</span>
                            </label>
                            <select id="category"
                                    name="category"
                                    class="form-select-cp w-100 @error('category') is-invalid @enderror">
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>
                                    — Pilih kategori —
                                </option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                                        @if ($cat === 'Pastry') 
                                        @elseif ($cat === 'Cookies') 
                                        @else 
                                        @endif
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback d-block" style="font-size:0.8rem; color:#C0392B;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- ===== SECTION: Gambar ===== --}}
                    <div class="cp-card mb-4">
                        <h5 style="margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                            <span style="font-size:1.3rem;"></span> Gambar Resep
                        </h5>

                        <div class="mb-2">
                            <label for="image_url" class="form-label-cp">URL Gambar</label>
                            <input type="url"
                                   id="image_url"
                                   name="image_url"
                                   class="form-control-cp w-100 @error('image_url') is-invalid @enderror"
                                   placeholder="https://example.com/gambar-kue.jpg"
                                   value="{{ old('image_url') }}">
                            @error('image_url')
                                <div class="invalid-feedback d-block" style="font-size:0.8rem; color:#C0392B;">
                                    {{ $message }}
                                </div>
                            @enderror
                            <p class="form-hint">Opsional. Masukkan URL gambar dari internet (Unsplash, dll).</p>
                        </div>

                        {{-- Preview gambar --}}
                        <div id="imagePreviewWrapper" style="display:none; margin-top:1rem;">
                            <p class="form-hint mb-2">Preview:</p>
                            <img id="imagePreview"
                                 src=""
                                 alt="Preview"
                                 style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1.5px solid var(--cp-border);">
                        </div>
                    </div>

                    {{-- ===== SECTION: Bahan-Bahan ===== --}}
                    <div class="cp-card mb-4">
                        <h5 style="margin-bottom:0.4rem; display:flex; align-items:center; gap:0.5rem;">
                            <span style="font-size:1.3rem;"></span> Bahan-Bahan
                            <span style="color:var(--cp-pink-dark);">*</span>
                        </h5>
                        <p class="form-hint mb-3">Tulis satu bahan per baris. Untuk sub-judul (misal: "— SELAI —"), awali dengan tanda "—".</p>

                        <textarea id="ingredients"
                                  name="ingredients"
                                  class="form-control-cp w-100 @error('ingredients') is-invalid @enderror"
                                  rows="10"
                                  placeholder="250 gr tepung terigu&#10;100 gr mentega tawar&#10;— SELAI —&#10;200 gr selai nanas&#10;...">{{ old('ingredients') }}</textarea>
                        @error('ingredients')
                            <div class="invalid-feedback d-block" style="font-size:0.8rem; color:#C0392B;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- ===== SECTION: Cara Membuat ===== --}}
                    <div class="cp-card mb-4">
                        <h5 style="margin-bottom:0.4rem; display:flex; align-items:center; gap:0.5rem;">
                            <span style="font-size:1.3rem;"></span> Cara Membuat
                            <span style="color:var(--cp-pink-dark);">*</span>
                        </h5>
                        <p class="form-hint mb-3">Tulis satu langkah per baris. Awali tiap langkah dengan nomor (misal: "1. ...") atau langsung tuliskan langkahnya.</p>

                        <textarea id="instructions"
                                  name="instructions"
                                  class="form-control-cp w-100 @error('instructions') is-invalid @enderror"
                                  rows="12"
                                  placeholder="1. Panaskan oven pada suhu 180°C.&#10;2. Campur semua bahan kering.&#10;3. Masukkan mentega dan aduk rata.&#10;...">{{ old('instructions') }}</textarea>
                        @error('instructions')
                            <div class="invalid-feedback d-block" style="font-size:0.8rem; color:#C0392B;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- ===== ACTION BUTTONS ===== --}}
                    <div class="d-flex gap-3 justify-content-end flex-wrap">
                        <a href="{{ route('recipes.index') }}" class="btn-cp-outline">
                            <i class="bi bi-arrow-left me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn-cp-primary" id="submitBtn">
                            <i class="bi bi-floppy2 me-2"></i>Simpan Resep
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Preview gambar dari URL
    const imageUrlInput = document.getElementById('image_url');
    const imagePreview  = document.getElementById('imagePreview');
    const previewWrapper = document.getElementById('imagePreviewWrapper');

    function updatePreview() {
        const url = imageUrlInput.value.trim();
        if (url) {
            imagePreview.src = url;
            previewWrapper.style.display = 'block';
            imagePreview.onerror = () => { previewWrapper.style.display = 'none'; };
        } else {
            previewWrapper.style.display = 'none';
        }
    }

    imageUrlInput.addEventListener('input', updatePreview);
    updatePreview(); // on page load (for old value)

    // Loading state on submit
    document.getElementById('recipeForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });
</script>
@endpush