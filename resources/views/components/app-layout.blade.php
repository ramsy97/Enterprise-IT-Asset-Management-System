@extends('layouts.app')

@section('title', $title ?? config('app.name', 'ITAMS Enterprise'))

@section('content')
    {{ $slot }}
@endsection
