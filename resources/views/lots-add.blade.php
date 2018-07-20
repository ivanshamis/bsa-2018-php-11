@extends('layouts.app')

@section('title', 'Currency market - adding lot')

@section('content-title', 'Currency market - adding lot')

@section('content')
    <div class="media-body">
    {!! Form::open(['route' => ['lots.store']]) !!}
    
    @include('parts/lot-form')       
    
    {!! Form::close() !!}
    </div>
@endsection
