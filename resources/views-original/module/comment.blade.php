<div class="comment-container" id="color-{{ $comment->extra }}">
    <span class="comment-user-nickname">Anon · {{ $comment->getUserNickname() }}</span>
	<h5 id="{{ $comment->sid }}" class="comment-title">{{ $comment->sid }}</h5>
    @if($comment->userid === $post->userid)
        <div class="tag-op">OP</div>
    @endif
    @if($comment->status == 2)
        <div class="tag-official">Oficial</div>
    @elseif($comment->status == 1)
        <div class="tag-highlight">Destacado</div>
    @endif
    <div class="post-options" data-postid="{{ $comment->id }}"><a href="#ex2" rel="modal:open">&there4;</a></div>
    <div class="comment-content">
        {!! $comment->content !!}
    </div>
</div>