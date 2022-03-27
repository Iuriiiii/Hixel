@extends('basic')
@inject('boards','App\Http\Controllers\IndexController')

@section('title',env('APP_NAME') . ' - ' . $post['title'])

@push('js')
var publication = @json($post);
var comments = @json($comments);
var opid = {{ $post['userid'] }};
var puid = {{ $post['id'] }};
@endpush

@push('script')
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.4.1/highlight.min.js"></script>
@endpush

@section('body')
@parent
<div class="modal" id="imgmodal">
    <div>
            <i class="fa fa-window-close" onclick="$(this).parent().parent().css('display','none')"></i>
            <form>
                <img id="fullsizeimg" style="width:90%;height:90%">
            </form>
    </div>
</div>
@endsection

@section('content')
<div class="rsbuttons">
    <button type="button" onclick="$('html, body').animate({ scrollTop: 0 }, 1000);"><i class="fa fa-level-up"></i></button>
    <button type="button" onclick="$('html, body').animate({ scrollTop: 99999999 }, 1000);"><i class="fa fa-level-down"></i></button>
</div>
<div class="pc" id="pc"></div>
<div class="section-comment">
    <form id="form-comment">
        <input type="hidden" value="{{ $post['id'] }}" id="postid"/>
        <h3>Comentar</h3>
        <textarea placeholder="Mensaje"></textarea><br>
        <div>
            <label class="upload-label" for="form-post-image-file">
            <!--<input type="text" placeholder="Enlace">-->
            Subir archivo <i class="fa fa-upload" aria-hidden="true"></i>
            </label>
            <input type="file" name="front" id="form-post-image-file" accept="image/gif, image/png, image/bmp, image/jpeg, image/webp">
            <div style="float:right">
                <i class="fa fa-square" type="checkbox" name="imgstatus" style="user-select:none;"> +18</i>&nbsp;
                <i class="fa fa-square" type="checkbox" name="imgurlstatus" style="user-select:none;"> URL</i>
            </div>
        </div>
        <div class="record">
            <button type="button" class="record-record"><i class="fa fa-microphone"></i></button>
            <button type="button" class="record-stop"><i class="fa fa-stop" ></i></button>
            <button type="button" class="record-remove"><i class="fa fa-microphone-slash"></i></button>
        </div>
        <div class="form-post-rightside-buttons">
            <button type="submit"><i class="fa fa-paper-plane-o"></i></button>
        </div>
        <audio id="record-audio" controls>
            Tu navegador no soporta controles de audio
        </audio>
        <div class="form-post-image-preview">
            <i class="fa fa-window-close" onclick="$('#form-comment input[type=file]').val('');$(this).parent().hide()"></i>
            <img>
        </div>
    </form>
</div>
<div class="cc" id="cc"><div class="post-container" id="comment-popup"></div></div>
@endsection