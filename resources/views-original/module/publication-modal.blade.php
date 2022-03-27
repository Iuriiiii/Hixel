@push('general')
<div id="ex2" class="modal">
    <form id="post-action-form">
        <input id="post-action-hidden" type="hidden" name="action" value="">
        <input id="post-postid-hidden" type="hidden" name="postid" value="">
        <input name="code" type="text" placeholder="Código"><br>
        <button class="post-option-button-action" type="button" data-action="delete">Usar Código</button><br>
        @if($user->status === 1)
            <label>Sección: </label>
            <select name="category">
              @foreach($boards->boards() as $board)
              <option {{ $board->diminutive == $category ? 'selected ' : '' }}value="{{ $board->id }}">{{ $board->identifier }}</option>
              @endforeach
            </select><br>
            <button class="post-option-button-action" type="button" data-action="move">Mover</button>
            <button class="post-option-button-action" type="button" data-action="delete">Borrar</button>
            <button class="post-option-button-action" type="button" data-action="highlight">Destacar</button>
            <button class="post-option-button-action" type="button" data-action="makeofficial">Oficializar</button><br>
            <button class="post-option-button-action" type="button" data-action="ban">Banear por:</button>
            <select id="bantime" name="bantime">
                @for ($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}">{{ $i }} Día{{ ($i !== 1) ?'s':'' }}</option>
                @endfor
            </select><br>
        @endif
        <label>Confirmar: </label><input type="checkbox" name="confirm">
    </form>
</div>
@endpush

@push('script')
    <script>
    $(".post-option-button-action").on("click",function(){
        $("#post-action-hidden").val($(this).data("action"));
        $("#post-action-form").submit();
    });
    
    $("#post-action-form").submit(function(event) {
        event.preventDefault();
        if(request) request.abort();
        request = $.ajax({
            url: "{{ route('post_action') }}",
            type: "post",
            data: $(this).serialize()
        }).done(function(response, textStatus, jqXHR){
            if(response["success"])
            {   
                location.reload();
                return;
            }
            alert(response["description"]);
        });
    });
    
    function showOptions(elm)
    {
        $("#post-postid-hidden").val($(elm).data("postid"));
    }
    
    /*$(".post-options").on("click",function(){
        $("#post-postid-hidden").val($(this).data("postid"));
    });*/
    </script>
@endpush