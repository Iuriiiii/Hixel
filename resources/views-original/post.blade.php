@extends('basic')

@section('tag-title','Hixel - ' . $post->title)

@push('js')
<script sync src="https://platform.twitter.com/widgets.js"></script>
@endpush

@push('css')
<style>

</style>
@endpush

@section('content')
<div id="comment-popup"></div>
<div class="publication-container"></div>
<div class="comments-container">
    <div class="comment-post-container">
        <!--<div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">×</span><strong>Error: </strong><span class="message"></span>
        </div><br>-->
        <form id="comment-post-form" enctype="multipart/form-data">
            @if($user->status === 1)
                <select name="status">
                    <option selected value="0">Estandar</option>
                    <option value="2">Oficial</option>
                    <option value="1">Destacado</option>
                </select><br>
            @endif
            <br>
            <textarea name="content" class="post-textarea" placeholder="Mensaje de la publicación"></textarea>
            <input type="hidden" name="postid" value="{{ $post->id }}">
            <input type="submit" class="post-submit" value="Enviar">
                        
        </form>
    </div>
    <br>
</div>
@endsection

@push('script')
<script>
$("#comment-post-form").submit(function(event) {
    let sb = $(".post-submit");
    sb.val("Enviando...");
    sb.attr("disabled",true);
    sb.css("background-color","gray");
    
    requestToSv("{{ route('comment_submit') }}",document.getElementById("comment-post-form"),false,true);
});

    let allposts = @json($arrayposts);
    let cc = $('.comments-container');
    let uid = {{ $post->userid }};
    
    $('.publication-container').append(postToHtml(allposts[0],false,0,{{ $user->status === 1 ? 'true' : 'false' }}));
    
    $.each(allposts[1],function(idx,elm){
        cc.append(postToHtml(elm,false,uid,{{ $user->status === 1 ? 'true' : 'false' }}));
    });
    
    $.each(allposts[2],function(idx,elm){
        cc.append(postToHtml(elm,false,uid,{{ $user->status === 1 ? 'true' : 'false' }}));
    });
    
    $.each(allposts[3],function(idx,elm){
        cc.append(postToHtml(elm,false,uid,{{ $user->status === 1 ? 'true' : 'false' }}));
    });
    
    // $(window).on("mousemove",function(e){
        // if(popup === null) return;
        // $(popup).css({position:"absolute",display:"block", left:e.pageX,top:e.pageY});
    // });
    
    $.each([".responses",".comments"],function(idx,elme){
        $(elme).on("mouseover",function(obj){
            let pos = $(obj.target).offset();
            let elm = $('.post-container[data-sid="' +  $(obj.target).data('sid') + '"]').clone()[0];
            $("#comment-popup").html(elm);
            $("#comment-popup").css({position:"relative",display:"block",zIndex:2, left:pos.left + 60,top:pos.top,width:"30%"});
        });
        
        $(elme).on("click",function(){
            $("#comment-popup").hide();
            $("#comment-popup").html('');
        });
        
        $(elme).on("mouseleave",function(){
            $("#comment-popup").hide();
            $("#comment-popup").html('');
        }); 
    });

$(".hidebtn").on("click",function(){
    //let sid = $(this).data('sid');
    //let elm = $('.post-container[data-sid="' + sid + '"]');
    
    $.ajax({
        url: "{{ route('hide.post') }}",
        type: "post",
        data: {postid: {{ $post->id }}}
    }).done(function(r,t,e){
        location.href = "{{ url('') }}";
    });
    
});

</script>
@endpush