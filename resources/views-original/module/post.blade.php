<div class="post-container"{{ $item->isComment() ? " id=color-{$item->extra}" : '' }}{{ ($item->userid === $user->id) && $item->isComment() ? ' style=background-color:#212121;color:white' : ''}}>
    @if($item->front)
    <div class="post-left">
        @isset($onindex)
            <a href="{{ $item->getUrl() }}">
        @else
            <a href="{{ $item->getImageUrl(false) }}" target="_blank">
        @endisset
        <img class="post-front" src="{{ $item->getImageUrl() }}">
        </a>
    </div>
    @endif
    <div>
    <div class="post-options" onclick="showOptions(this);" data-postid="{{ $item->id }}"><a href="#ex2" rel="modal:open">&there4;</a></div>
    <div class="post-content-container">
        @if($item->isComment())
            @if($item->userid === $post->userid)
                <div class="tag-op">OP</div>
            @endif
        @endif
        <span class="post-user-nickname"><li class="fas fa-user"></li> <b>{{ $item->getUser()->status == 1 ? $item->getUserNickname() : 'Anónimo'}}</b></span>
        <a href="{{ $item->getUrl() }}" id="{{ $item->sid }}">
        @if($item->isComment())
            <span class="post-title">{{ $item->sid }}</span>
        @else
            <span class="post-title">{{ $item->title }}</span>
        @endif
        </a>
        @if(!isset($onindex))
        <i class="fa fa-clock-o" aria-hidden="true"{{ $item->isComment() ? ' style=float:right;' : ''}}> {{ $item->getTimeDiff() }}</i>
        @endif
        @if($item->isComment())
            <i class="fa fa-tag" onclick="appendContent('>{{ $item->sid }}');"></i>
        @endif
        @if($item->status == 2)
            <div class="tag-official">Oficial</div>
        @elseif($item->status == 1)
            <div class="tag-highlight">Destacado</div>
        @endif
        <div class="post-content" id="post-content-{{ $item->sid }}">
            @if($item->isComment())
                @foreach($item->getComments() as $response)
                    <a style="color:blue;" href="{{ $response->getUrl() }}" target="_self">&gt;&gt;{{ $response->sid }}</a> 
                @endforeach
            @endif
            <p>
            @isset($onindex)
                {!! $item->getPreviewOfContentAsHTML() !!}...
            @else
                {!! $item->getContentAsHTML() !!}
            @endisset
            </p>
        </div>
        @if($item->audioid)
        <div class="post-audio">
            <audio src="{{ DB::table('audios')->where('id',$item->audioid)->first()->file }}" controls>
        </div>
        @endif
    </div>
    <br>
    <table style="color:white;width:100%;">
        <tr>
            <td style="text-align:left;width:33;">
                <div class="play-speech" onclick="playspeech(this)" id="play-{{ $item->sid }}" data-contentid="{{ $item->sid }}"><li class="fa fa-play"></li></div>
                <div class="pause-speech" onclick="speechSynthesis.pause()" id="pause-{{ $item->sid }}" data-contentid="{{ $item->sid }}"><li class="fa fa-pause"></li></div>
                <div class="resume-speech" onclick="speechSynthesis.resume()" id="resume-{{ $item->sid }}" data-contentid="{{ $item->sid }}"><li class="fa fa-play"></li></div>
                <div class="stop-speech" onclick="speechSynthesis.cancel()" id="stop-{{ $item->sid }}" data-contentid="{{ $item->sid }}"><li class="fa fa-stop"></li></div>
                </td>
            <td style="text-align:center;width:33;">
                @if(!$item->isComment())
                    @if(!isset($onindex))
                    <div class="comments-comment" onclick="$('#ex1').modal({clickClose: false})">Comentar</div>  
                    @endif                
            </td>
            <td style="text-align:right;width:33;">
                <div class="comments-counter"><li class="fas fa-comments"><p> {{ $item->getCommentsCounter() }}</p></li></div>
                @endif
            </td>
        </tr>
    </table>
    </div>
</div>