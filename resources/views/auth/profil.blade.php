@extends('layouts.app')
@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
<style>
    .form-input { width:100%;background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.75rem 1rem;font-size:.875rem;font-family:'Karla',sans-serif;transition:border-color .2s;outline:none; }
    .form-input:focus { border-color:var(--water); }
    .form-label { display:block;font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.45rem; }
</style>

<div style="max-width:520px;margin:0 auto;">
    <div style="background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:2rem;">

        {{-- Avatar --}}
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid rgba(139,94,60,.1);">
            <div style="width:52px;height:52px;border-radius:50%;background:var(--soil);display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:1.3rem;font-weight:700;color:var(--straw);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:700;color:var(--soil);">{{ $user->name }}</p>
                <p style="font-size:.78rem;color:var(--textlt);">{{ $user->getRoleNames()->join(', ') }}</p>
            </div>
        </div>

        @if(session('success'))
        <div style="background:rgba(90,122,71,.08);border:1px solid rgba(90,122,71,.2);color:#4a6741;border-radius:8px;padding:.75rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.75rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('profil.update') }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Password Baru <span style="font-weight:300;color:var(--textlt);">(kosongkan jika tidak diganti)</span></label>
                <input type="password" name="password" class="form-input" placeholder="Min. 8 karakter">
            </div>

            <div style="margin-bottom:1.75rem;">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>

            <button type="submit" style="background:var(--soil);color:var(--straw);padding:.8rem 2rem;border-radius:8px;border:none;font-family:'Karla',sans-serif;font-size:.9rem;font-weight:600;cursor:pointer;">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
