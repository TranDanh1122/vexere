@extends('Core::layouts.app')

@section('head')
    {!! RvMedia::renderHeader() !!}
@endsection

@section('content')
    {!! RvMedia::renderContent() !!}
@endsection

@section('foot')
    {!! RvMedia::renderFooter() !!}
@endsection
