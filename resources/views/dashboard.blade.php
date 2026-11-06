@extends('app')
@section('content')
<h2>Dashboard admin</h2>
<p>Halo, {{ auth()->user()->name }}. Dashboard dibuat di langkah 6.</p>
@endsection
