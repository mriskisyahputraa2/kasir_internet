{{-- resources/views/absen.blade.php --}}
@extends('layouts.app')

@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle m-4">
        <h1 class="fs-2">Absen</h1>
    </div>

    <div class="m-4">
        <p>Silakan absen terlebih dahulu.</p>
        <form action="{{ route('absen.lanjut') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Lanjut</button>
        </form>
    </div>
@endsection
