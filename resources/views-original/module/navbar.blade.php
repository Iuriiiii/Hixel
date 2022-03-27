@push('js')
    
@endpush

@push('css')
    <!--<link href="{{ url('/resources/css/navbar.css') }}" rel="stylesheet" type="text/css">-->
@endpush

@push('script')
    <script>
    $("#bell-button").on("click",function(){
        $(".notify-modal").toggle();
    });
    
    function delNotification(elm)
    {
        let nid = $(elm).data('nid');
        let nurl = $(elm).data('url');
        
        $.ajax({
            url: "{{ route('remove_notification') }}",
            type: "post",
            data: {notifyid: nid}
        }).done(function(response, textStatus, jqXHR){
            if(response["success"])
            {   
                window.location.href = nurl;
                return;
            }
            alert(response["description"]);
        });
    }
    </script>
@endpush

@section('general')
    @php
        $notifications = $user->getNotifications();
    @endphp
    
    <!-- NOTIFICATIONS: {{ count($notifications) }} -->
    
    @if(count($notifications))
        <div class="notify-modal-counter">
                <p>{{ count($notifications) }}</p>
        </div>
    @endif
    
    <div class="notify-modal">
        @foreach($notifications as $notification)
        @php
            $userpost = $notification->getPost();
        @endphp
        @if($userpost !== null)
        <a href="#">
        <div onclick="delNotification(this)" data-nid="{{ $notification->id }}" data-url="{{ $userpost->getUrl() }}">
            @if($notification->type == 1)
                <div class="notify-info">
                <div class="title">{{ $userpost->title }}</div>
                <pre>Tu publicación fue comentada</pre>
                </div>
                <img class="notify-thumbnail" src="{{ $userpost->getThumbnailUrl() }}"/>
            @elseif($notification->type == 2)
                <pre>Tu respuesta fue replicada</pre>
            @endif
        </div>
        </a>
        @endif
        @endforeach
    </div>
@endsection

@section('body_header')
<div id="navbar-body">
    <a href="/">
        <h1><i style="margin-left:15px;margin-right:10px;" class="fab fa-black-tie"></i>Hixel</h1>
        <h4>{{ $category->identifier }}</h4>
    </a>
    <nav>
        <ul class="links">
            {{--  <input type="checkbox" id="btn-menu">
            <label for="btn-menu"><li class="menu-bar"><i class="fas fa-bars"></i></li></label>
            <ul class="boards">
              &#10092;
              @foreach($boards->boards() as $board)
                @if($board->diminutive == $category->diminutive)
                    <li>{{ $board->diminutive }}</li>
					<div class="hr"></div>
                @else
                    <li><a href="/{{ $board->diminutive }}" data-text="{{ $board->identifier }}">{{ $board->diminutive }}</a></li>
					<div class="hr"></div>
                @endif
                @if(!$loop->last)
                    &#10072;
                @endif
              @endforeach
              &#10093;
            </ul>
			<!--<select id="choose-themes" name="themes">
				<option value="">Hixel
				<option value="white" onchange="colorChanger()">Hixel W
				<option value="vwhite">Vox Day
				<option value="vnight">Voxed N
			</select> -->
            <!--<input type="text" id="search-words"></input>-->
            --}}
            <select id="navbar-menu">
              @foreach($boards->boards() as $board)
              <option  data-dim="{{ $board->diminutive }}" {{ $board->diminutive == $category->diminutive ? 'selected ' : '' }}value="{{ $board->id }}"><a href="/{{ $board->diminutive }}">{{ $board->identifier }}</a></option>
              @endforeach
            </select>
            @if($user->isCategoryHidden($category->id))
            <li class="fa fa-toggle-off" id="hidecategory"></li>
            @else
            <li class="fa fa-toggle-on" id="hidecategory"></li>
            @endif
            <li onclick="$('#msearch').modal({clickClose: false});" class="fa fa-search" id="search-button"></li>
            <li class="far fa-bell" id="bell-button"></li>
            <a onclick="$('#ex1').modal({clickClose: false});">
                @if(isset($onindex))
                    <li class="fas fa-pen" id="nav-post"></li>
                @else
                    <li class="fa fa-reply" id="nav-post"></li>
                @endif
            </a>
        </ul>
    </nav>
</div>
@endsection

@push('script')
<script>
    $("#navbar-menu").on("change",function(){
        location.href = '{{ url('') }}/' + $(this).find(":selected").data('dim');
    });
</script>

<script>
    var toggleHidenbtn = $("#hidecategory");
    var lastclass = toggleHidenbtn.attr("class");
    
    toggleHidenbtn.on("click",function(){
        $.ajax({
            url: "{{ route('hide.category') }}",
            type: "post",
            data: {categoryid: {{ $category->id }}},
            cache: false,
            beforeSend:function(){
                toggleHidenbtn.attr("class","fa fa-cog");
            }
        }).done(function(r,ts,oXHR){
            if(!r["success"])
            {
                toggleHidenbtn.attr("class",lastclass);
                alert(r["description"]);
                return;
            }
            if(r["hidden"])
            {
                toggleHidenbtn.attr("class","fa fa-toggle-off");
            }
            else
            {
                toggleHidenbtn.attr("class","fa fa-toggle-on");
            }
        });
    })
</script>
@endpush