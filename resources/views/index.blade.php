@extends('basic')
@inject('boards','App\Http\Controllers\IndexController')
@inject('recaptcha','App\Models\ReCaptcha')

@push('js')
@if($recaptcha->hasReCaptcha())
<script src='https://www.google.com/recaptcha/api.js'></script>
@endif
@endpush

@section('content')
<div class="modal" id="modal-publication">
    <div>
        <i class="fa fa-window-close" onclick="$(this).parent().parent().css('display','none')"></i>
        <form id="form-post">
            <h3>Publicar</h3>
            @if(Route::currentRouteName() === 'board')
            <select>
                @foreach($boards->boards() as $board)
                <option {{ $board->diminutive == $category->diminutive ? 'selected ' : '' }}value="{{ $board->id }}">{{ $board->identifier }}</option>
                @endforeach
            </select>
            @endif
            <input class="title" type="text" placeholder="Título"></input>
            <textarea placeholder="Mensaje"></textarea><br>
            <div>
                <label class="upload-label" for="form-post-image-file">
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
            @if($recaptcha->hasReCaptcha())
            <div class="g-recaptcha" data-sitekey="6LdhYuIZAAAAAE29l4UUVcz5MvnyntHviotuvlwa"></div>
            @endif
            <audio id="record-audio" controls>
                Tu navegador no soporta controles de audio
            </audio>
            <div class="form-post-image-preview">
                <i class="fa fa-window-close" onclick="$('#form-post input[type=file]').val('');$(this).parent().hide()"></i>
                <img>
            </div>
        </form>
    </div>
</div>
<div class="content" id="content"></div>
@endsection