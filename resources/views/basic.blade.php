@inject('usermodel','App\Models\User')
@inject('categories','App\Models\Category')
{{-- @inject('boards','App\Http\Controllers\IndexController') --}}
@inject('themes','App\Models\Theme')
@php
$user = $usermodel->getUser(Request::ip());
@endphp
<html>
    <head>
        <title>@yield('title',env('APP_NAME'))</title>
        <!-- METAS -->
        <meta property="og:title" content="{{ env('APP_NAME') }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="{{ url('') }}"/>
        <meta property="og:image" content="{{ url('/public/hixel-logo.webp') }}"/>
        <meta property="og:description" content="@yield('page.description','Somos una comunidad anónima en donde compartimos ideas, pensamientos, estudios y proyectos.')"/>
        <meta name="keywords" content="Anonimato, publicar, compartir, ideas, creación, publicaciones, creatividad, tablón, imágenes, audio, voz, mensajes de voz, mensajes, mensaje"/>
        <meta name="theme-color" content="#FF0000">
        <!-- LINKS -->
        <link rel="shortcut icon" href="{{ url('public/favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <!-- JS -->
        <script>
        var cid = {{ $category->id }};
        var urlocation = '{{ Route::currentRouteName() }}';
        var urstatus = {{ $user->status }}; 
        var urid = {{ $user->id }}; 
        var categories = @json($categories->all()->toArray());
        var urtheme = {{ $user->theme }};
        var themes = @json($themes->all()->toArray());
        @stack('js')
        </script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <!--<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
        <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>-->
        <!--<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.3.0/dist/lazyload.min.js"></script>-->
        <!--<script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js"></script>-->
        <script src="{{ url('/public/js/hixel.js') }}"></script>
        @stack('script')
        <!-- CSS -->
        <link href="{{ url('/public/css/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
        
        <link href="https://fonts.googleapis.com/css2?family=Enriqueta&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=PT+Serif+Caption&display=swap" rel="stylesheet">
        @if($user->isAdmin())
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>
        <script>
        </script>
        <style>
        </style>
        <link href="{{ $user->getThemeUrl() }}" rel="stylesheet" type="text/css" id="theme">
        @elseif($user->isModerator())
        <!--Tipo de letra-->
        <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
        <!--<link href="/resources/css/temti.css" rel="stylesheet" type="text/css" id="theme">-->
        <link href="{{ $user->getThemeUrl() }}" rel="stylesheet" type="text/css" id="theme">
        @else
        <link href="{{ $user->getThemeUrl() }}" rel="stylesheet" type="text/css" id="theme">
        @endif
        <!-- META -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    </head>
    <body>
        <header>
        @include('modules.navbar')
        </header>
        
        @include('modules.messages')

        @yield('body')
        
        <section class="wrapper">
            <div class="btn-discord"><a href="https://discord.gg/jc5E7BXmtj" target="_blank"><img src="/public/img/discord-logo.webp"></a></div>
            @include('modules.search')
            @yield('content')
        </section>
    </body>
</html>