@inject('usermodel','App\Models\User')
@php
$user = $usermodel->getUser(Request::ip());
@endphp
<html>
    <head>
        <title>@yield('title',env('APP_NAME'))</title>
        <meta property="og:title" content="{{ env('APP_NAME') }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="{{ url('') }}"/>
        <meta property="og:image" content="{{ url('/public/hixel-logo.webp') }}"/>
        <meta property="og:description" content="@yield('page.description','Somos una comunidad anónima en donde compartimos ideas, pensamientos, estudios y proyectos.')"/>
        <meta name="keywords" content="Anonimato, publicar, compartir, ideas, creación, publicaciones, creatividad, tablón, imágenes, audio, voz, mensajes de voz, mensajes, mensaje"/>
        <meta name="theme-color" content="#FF0000">
        <!-- LINKS -->
        <link rel="shortcut icon" href="{{ url('public/favicon.ico') }}">
        <link href="{{ $user->getThemeUrl() }}" rel="stylesheet" type="text/css" id="theme">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    </head>
    <body>
        <script>
            setTimeout(() => {
                location.href = "http://www.temti.net/";
            }, 3000);
        </script>
        <h1>503</h1>
        <h5>Nuestro mísero hosting no soporta a tantas personas.</h5>

        <h5 style="color:red">Discord: <a href="https://discord.gg/jc5E7BXmtj">clic aquí</a></h5>
        <br>
        <h1>Servidor de Half Life</h1>
        <h5>Descargar HL: <a href="https://mega.nz/file/QZl0iKKS#V1cTksmyeotFR1WpbnDmVwOJXdw5SQSgZa9ngc4xgv8" target="_blank">clic aquí</a></h5>
        <h5>Descargar Pack de mapas: <a href="https://www.mediafire.com/file/2bk8y19d6hwzfbv/Avalve.zip/file" target="_blank">clic aquí</a></h5>
        <h5>Servidor: hixel.net:27016</h5>
    </body>
</html>