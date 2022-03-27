@inject('boards','App\Http\Controllers\IndexController')
@inject('apiController','App\Http\Controllers\ApiController')
{{-- @includeIf("background.{$category}") --}}
@include('module.navbar')
@include('module.rightside-buttons')
@include('module.publication-modal')
<html>
    <head>
        <title>@yield('tag-title','Hixel')</title>
        <link rel="shortcut icon" href="{{ url('public/favicon.ico') }}">
        {{--
        @if($user->status == 0)
        <link href="{{ url('/resources/css/global.css') }}" rel="stylesheet" type="text/css">
            @if(isset($onindex))
            <link href="{{ url('/resources/css/post.css') }}" rel="stylesheet" type="text/css">
            @else
            <link href="{{ url('/resources/css/comment.css') }}" rel="stylesheet" type="text/css">
            @endif
        @else
            <link href="{{ url('/resources/css/stf/global.css') }}" rel="stylesheet" type="text/css">
            @if(isset($onindex))
            <link href="{{ url('/resources/css/stf/post.css') }}" rel="stylesheet" type="text/css">
            @else
            <link href="{{ url('/resources/css/stf/comment.css') }}" rel="stylesheet" type="text/css">
            @endif
        @endif
        --}}
        <link href="{{ url('/public/css/global-day.css') }}" rel="stylesheet" type="text/css">
        <meta charset="UTF-8">
        <meta property="og:title" content="Hixel" />
        <meta property="og:type" content="website" />
        <meta property="og:description" content="Página de interacción social anónima" />
        <meta property="og:image" content="{{ url('/resources/app/hixel-logo.png') }}" />
        <link rel="logo" type="image/png" href="{{ url('/resources/app/hixel-logo.png') }}"/>
        <meta property="og:url" content="{{ url('') }}" />
        <!---->
        @stack('css')
        <!--<script async src="https://www.googletagmanager.com/gtag/js?id=G-4NWT3GNRJX"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-4NWT3GNRJX');
        </script>-->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script type="text/javascript">var pageurl = '{{ Request::getHost() }}'</script>
        <!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>-->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
        <script type="text/javascript" src="{{ url('/resources/js/rangyinputs-jquery.js') }}"></script>
        <script type="text/javascript" src="{{ url('/resources/js/bbcode.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('/resources/js/packed/speech.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('/resources/js/post.js') }}"></script>
        @isset($onindex)
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
		<script type="text/javascript" src="{{ url('/resources/js/masonry.js') }}" defer></script>
        @endisset
        @stack('js')
        <link rel="stylesheet" href="{{ url('/resources/css/jquery.modal.min.css') }}" />
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Geo&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-KA6wR/X5RY4zFAHpv/CnoG2UW1uogYfdnP67Uv7eULvTveboZJg0qUpmJZb5VqzN" crossorigin="anonymous">
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
        <link href="{{ url('/resources/css/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script defeer>
        
            window.alert = function(message)
            {
                let a = $(".alert");
                let msg = $(".alert .message");
                msg.text(message);
                a.css('display', 'inline-block');
                setTimeout(function(){
                    a.hide();
                },4000)
            };
        </script>
    </head>
    <body>
        <!-- Navbar -->
        <header>
            @yield('body_header')
        </header>
        
        <div id="msearch" class="modal">
            <form id="form-search" action="{{ route('search') }}">
                <input id="form-search-words" type="text" name="words" placeholder="Palabras a buscar"></input>
                <button type="submit" value=""><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">×</span><strong>Error: </strong><span class="message"></span>
        </div>
        
        <div id="ex1" class="modal">
            <div>
                <h3 style="text-align:center;">
                @if(isset($onindex))
                    Publicar
                @else
                    Comentar
                @endif
                </h3><br>
                <form id="post-form" enctype="multipart/form-data">
                    @if(isset($onindex))
                        <select name="category">
                          @foreach($boards->boards() as $board)
                          <option {{ $board->diminutive == $category->diminutive ? 'selected ' : '' }}value="{{ $board->id }}">{{ $board->identifier }}</option>
                          @endforeach
                        </select>
                    @else
                        <input type="hidden" name="category" value="{{ $category->id }}">
                    @endif
                    
                    @if($user->status === 1)
                        <select name="status">
                            <option selected value="0">Estandar</option>
                            <option value="2">Oficial</option>
                            <option value="1">Destacado</option>
                        </select>
                    @endif
                    <input type="{{ isset($onindex) ? 'text' : 'hidden' }}" id="post-title" name="title" placeholder="Título"></input><br>
                    <!-- Botones -->
                    <div class="post-buttons">
                        <button type="button" id="bbspo"><i class="fa fa-window-minimize" aria-hidden="true"></i></button>
                        <button type="button" id="bburl"><i class="fa fa-link" aria-hidden="true"></i></button>
                        <button type="button" id="bbb"><i class="fa fa-bold" aria-hidden="true"></i></button>
                        <button type="button" id="bbund"><i class="fa fa-underline" aria-hidden="true"></i></button>
                        <button type="button" id="bbita"><i class="fa fa-italic" aria-hidden="true"></i></button>
                        <button type="button" id="bbquo"><i class="fa fa-quote-left" aria-hidden="true"></i></button>
                        <button type="button" id="bbvid"><i class="fa fa-file-video-o" aria-hidden="true"></i></button>
                        <button type="button" id="bbtch"><i class="fa fa-strikethrough" aria-hidden="true"></i></button><br>
                        <button type="button" id="bbtwt"><i class="fa fa-twitter"></i></button>
                        <button type="button" id="bbcnt"><i class="fa fa-align-center"></i></button>
                        <button type="button" id="bbrgt"><i class="fa fa-align-right"></i></button>
                    </div>
                    <div class="post-buttons">
                        <span>Colores: </span>
                        <div id="bbcol0" style="display:inline-block;color:green;cursor:default;">&#x2756;</div>
                        <div id="bbcol1" style="display:inline-block;color:gray;cursor:default;">&#x2756;</div>
                        <div id="bbcol2" style="display:inline-block;color:red;cursor:default;">&#x2756;</div>
                        <div id="bbcol3" style="display:inline-block;color:black;cursor:default;">&#x2756;</div>
                        <div id="bbcol4" style="display:inline-block;color:orange;cursor:default;">&#x2756;</div>
                        <div id="bbcol5" style="display:inline-block;color:cyan;cursor:default;">&#x2756;</div>
                        <div id="bbcol6" style="display:inline-block;color:blue;cursor:default;">&#x2756;</div>
                    </div>
                    <textarea name="content" class="post-textarea" placeholder="Mensaje de la publicación"></textarea><br>
                    <input type="hidden" name="postid" value="{{ $post->id ?? 0 }}">
                    <input type="submit" class="post-submit" value="Enviar">
                    <input type="file" id="post-content-file" style="display:none" accept="text/plain">
                    <input type="file" name="front" id="post-image-upload" accept="image/gif, image/png, image/bmp, image/jpeg, image/webp">
                    
                    <div class="record">
                        <button type="button" class="record-record"><i class="fa fa-microphone"></i></button>
                        <button type="button" class="record-stop"><i class="fa fa-stop" ></i></button>
                        <button type="button" class="record-remove"><i class="fa fa-microphone-slash"></i></button>
                    </div>
                    <audio id="record-audio" controls>
                        Tu navegador no soporta controles de audio... (Jjjajaj)
                    </audio>
                    @isset($onindex)
                    <div class="g-recaptcha" data-sitekey="6LdhYuIZAAAAAE29l4UUVcz5MvnyntHviotuvlwa"></div>
                    @endisset
                </form>
            </div>
            <div class="ext1-image">
                <br>
                <i id="post-image-close" class="fa fa-window-close" style="float:right;cursor:pointer;"></i>
                <img src="" id="post-image">
            </div>
        </div>
        <!-- GENERAL -->
        @yield('general')
        @stack('general')
        
        <div id="wrapper">
            <div class="content">
                @isset($onindex)
                    <div class="posts-container" id="posts-container">
                        @yield('post_container')
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
        
        <!-- SECCIÓN DE SCRIPTS -->
        <div id="scripts">
            <script type="text/javascript" src="{{ url('/resources/js/packed/basic.js') }}" defer></script>
            <!--<script type="module" type="text/javascript" src="../resources/js/recorder.js" defer></script>-->
            <script src="https://cdn.jsdelivr.net/npm/opus-media-recorder@latest/OpusMediaRecorder.umd.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/opus-media-recorder@latest/encoderWorker.umd.js"></script>
            <script src="{{ url('/resources/js/recorder.js') }}"  defer></script>
            <script>

var rchunks;

function requestToSv(_url,f,_nw = true,force = false)
{
    event.preventDefault();
    if(request) request.abort();
    
    let formData = new FormData(f);
    let files = document.getElementById("post-image-upload").files;
    
    if(files.length == 1)
    {
        formData.append("front",files[0]);
    }
    else if(files.length > 1)
    {
        alert("No se puede subir más de una imagen.");
        return;
    }
    
    let rurl = $("#record-audio").attr('src') || '';

    if(rurl !== '')
    {
        formData.append("audiodata",rchunks[0]);
        /*$.ajax({
             async: false,
             type: 'GET',
             url: rurl,
             success: function(data) {
                  formData.append("audiodata",data);
                  console.log(1);
             }
        });*/
    }

    request = $.ajax({
        url: _url,
        type: "post",
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    }).done(function(r,ts,oXHR){
        if(r["success"])
        {   
            resetForm();
            if(_nw)
            {
                open(r["url"]);
            }
            else
            {
                window.location.href = r["url"];
                if(force) location.reload();
            }
            return;
        }
        
        let sb = $(".post-submit");
        sb.val("Enviar");
        sb.attr("disabled",false);
        sb.css("background-color","#A74AC7");
        
        alert(r["description"]);
    }).fail(function(oXHR,ts,errThr){
        let sb = $(".post-submit");
        sb.val("Enviar");
        sb.attr("disabled",false);
        sb.css("background-color","#A74AC7");
        alert("¡Se produjo un error interno en el servidor! Intente contactar al administrador.");
    });
}

function resetForm()
{
    $(".post-textarea").text('');
    $(".post-title").val('');
    $("#post-image-upload").val('');
    $("#record-audio").attr('src','');
}

var request;

$("#post-form").submit(function(event) {
    let sb = $(".post-submit");
    sb.val("Enviando...");
    sb.attr("disabled",true);
    sb.css("background-color","gray");
    
    requestToSv("{{ isset($onindex) ? route('post_submit') : route('comment_submit') }}",document.getElementById("post-form"),false,{{ isset($onindex) ? "false" : "true" }});
});

$("#form-search").submit(function(e){
    if($("#form-search-words").val().length === 0)
    {
        e.preventDefault();
        alert("Se esperaba palabras de búsqueda.");
    }
});

function appendContent(txt)
{
    let txarea = $(".post-textarea");
    
    if(txarea.val().indexOf(txt) === (-1))
    {
        txarea.val(txarea.val() + txt + "\n");
    }
}


$("#post-image-close").on("click",function(){
    $(".ext1-image").hide();
    $("#post-image").attr("src","");
    $("#post-image-upload").val("");
});

$("#post-image-upload").on("change", function (e) {
    let files = e.currentTarget.files;
    let reader = new FileReader();
    
    reader.onload = function(e) {
      $("#post-image").attr("src", e.target.result);
    }
    
    reader.readAsDataURL(files[0]);
    
    $(".ext1-image").show();
});

window.addEventListener("paste",function(event){
    document.getElementById("post-image-upload").files = (event.clipboardData || event.originalEvent.clipboardData).files;
    $("#post-image-upload").trigger("change");
});
            </script>
        
            @stack('script')
        
            <script type="text/javascript" src="{{ url('/resources/js/packed/speech.js') }}" defer></script>
        </div>
    </body>
</html>