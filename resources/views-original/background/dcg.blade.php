
{{-- La variable $category se utiliza para saber en cuál tablón estamos en el momento --}}

@push('css')
    {{--
        En esta sección van los "link" a los .CSS
        También puede ir una un <style></style> sin problema.
    --}}
    <style>

#particles-js {
    width:100%;
    height:100%;
    position:fixed;
    z-index:-1;
    top:0px;
    left:0px;
    background:black;
}    

    </style>
@endpush

@push('js')
    {{--
        En esta sección van los "script" a los .JS
        También puede ir una un <script></script> sin problema.
    --}}
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.js" type="text/javascript"></script>
@endpush

@push('script')
    <script>
        particlesJS.load('particles-js', '/resources/json/particle-{{ $category }}.json');
    </script>
@endpush

@section('background')
    {{--
        En esta sección van el código dentro del body, si se require
    --}}
    <div id="particles-js"><canvas class="particles-js-canvas-el"></canvas></div>
@endsection