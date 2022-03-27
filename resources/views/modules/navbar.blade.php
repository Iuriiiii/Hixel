@php
    $notifications = $user->getNotifications();
@endphp

<nav>
    <div class="logo">
        <a href="{{ url('') }}">
            <i class="fa fa-black-tie"> {{ env('APP_NAME') }}</i>
        </a>
        <select id="themeselector"></select>
    </div>
    @if(Route::currentRouteName() === 'board')
    <i class="fa fa-paper-plane" id="button-publicate"></i>
    @endif
    @if(count($notifications))
    <i class="fa fa-bell" onclick="$('.notifications').toggle()">
        <span>{{ count($notifications) }}</span>
    </i>
    @endif
    <i class="fa fa-search" onclick="$('#modal-search').css('display','flex')"></i>
    @if(Route::currentRouteName() === 'board')
    <i class="fa fa-toggle-{{ $user->isCategoryHidden($category->id) ? 'off' : 'on' }}" id="hidecategory"></i>
    @endif
    <i class="fa fa-bars" id="reorder"></i>
    <select id="categoryselector"></select>
</nav>

@section('body')
    <div class="notifications">
        <ul>
        @foreach($notifications as $notification)
            @php
                $userpost = $notification->getPost();
            @endphp
            @if($userpost !== null)
                @if($notification->type == 1)
                    <a href="{{ $userpost->getUrl() }}" target="_self"><li>{{ $userpost->title }}</li></a>
                @elseif($notification->type == 2)
                    <a href="{{ $userpost->getUrl() }}" target="_self"><li>Tu respuesta fue comentada.</li></a>
                @endif
            @endif
        @endforeach
        </ul>
    </div>
@endsection