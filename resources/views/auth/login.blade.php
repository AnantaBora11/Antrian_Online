@extends('layouts.public')
@section('title', 'Login — Antrian Online')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Login Admin</h2>
            <p>Masuk ke panel admin antrian</p>
        </div>

        @if($errors->any())
        <div class="auth-alert auth-alert-error">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@antrian.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-group form-check">
                <label class="check-label">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="auth-footer">
            <a href="{{ route('home') }}">← Kembali ke halaman utama</a>
        </div>
    </div>
</div>
@endsection
