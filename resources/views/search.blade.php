@extends('basic')

@push('js')
var publications = @json($posts);
@endpush

@section('content')
<div class="content" id="content"></div>
@endsection