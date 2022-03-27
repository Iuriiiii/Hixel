{{-- Este archivo es utilizado por la API para retornar la estructura de posts --}}

@foreach($official_posts as $item)
@include('module.post')
@endforeach

@foreach($highlight_posts as $item)
@include('module.post')
@endforeach

@foreach($posts as $item)
@include('module.post')
@endforeach