@push('css')
    <!--<link href="../resources/css/navbar.css" rel="stylesheet" type="text/css">-->
    <style>

.rightside-buttons-container {
    display:none;
    position:fixed;
    z-index:1;
    right:5px;
    bottom:5px;
    background-color:gray;
}

.rightside-buttons-container button {
    width:40px;
    height:40px;
	color: #Fff;
	background: transparent;
	border: none;
}

.rightside-buttons-container button:hover {
	cursor: pointer;
}

#gotop {
	font-size:20px;
}

.rightside-buttons-container .fa-pen {
	font-size: 15px;
}

    </style>
@endpush

@push('js')
    <script type="text/javascript" src="../resources/js/jquery.appear.js"></script>
@endpush

@push('general')
    <div class="rightside-buttons-container">
        <button class="fa fa-angle-double-up" type="button" id="gotop"></button>
        <a href="#ex1" rel="modal:open"><button type="button">
        @if(isset($onindex))
            <li class="fas fa-pen" id="nav-post"></li>
        @else
            <li class="fas fa-reply" id="nav-post"></li>
        @endif</button></a>
    </div>
@endpush

@push('script')
    <script>
        function isOnWindow(element) {
            var onWindow = element.get(0).getBoundingClientRect();

            if (onWindow.top >= 0 && onWindow.left >= 0 && onWindow.bottom <= (window.innerHeight || document.documentElement.clientHeight) && onWindow.right <= (window.innerWidth || document.documentElement.clientWidth)) {
                return true;
            }

            return false;
        }

        function IsVisible(element) {
            if (element.is(':visible') && element.css('visibility') != 'hidden' && element.css('opacity') > 0) {
                return true;
            }

            return false;
        }

        function IsVisibleOnWindow(element) {
            return IsVisible(element) && isOnWindow(element);
        }
                
        window.addEventListener('load',function(){
            $(window).scroll(function() {
                if(IsVisibleOnWindow($('#navbar-body')))
                {
                    $('.rightside-buttons-container').hide();
                }
                else
                {
                    $('.rightside-buttons-container').show();
                }
            });
            
            $('#gotop').on('click',function(){
                $(window).scrollTop(0);
            });
        })
    </script>
@endpush