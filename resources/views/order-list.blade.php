@extends('layouts.mobile-app')

@section('content')
    @livewire('order-list', ['qrCode' => $qr_code])
@endsection