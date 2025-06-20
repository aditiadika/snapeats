@extends('layouts.mobile-app')

@section('content')
    @livewire('menu-list', ['qrCode' => $qr_code])
@endsection
