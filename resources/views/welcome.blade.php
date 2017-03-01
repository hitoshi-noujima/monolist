@extends('layouts.app')

@section('cover') 
    {{-- ＠yield('cover') に埋め込まれる --}}
    <div class="cover">
        <div class="cover-inner">
            <div class="cover-contents">
                <h1>素敵なモノと出会う場所</h1>
                @if (!Auth::check())
                    <a href="{{ route('signup.get') }}" class="btn btn-success btn-lg">モノリストを始める</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('content')
    {{-- ＠yield('content') に埋め込まれる --}}
    テスト
@endsection