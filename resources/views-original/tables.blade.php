@extends('basic')

@push('script')
<script>
    var rq;
    var page = 1;
    var working = true;
    var lp = null;
    
    // function getPosts()
    // {
        // if(!working) return;
        // if(rq) rq.abort();
        
        // rq = $.ajax({
            // url: "{{ route('get_posts') }}",
            // type: "post",
            // data: {page: window.page,category: {{ $category->id }},last_post: lp}
        // }).done(function(response, textStatus, jqXHR){
            // working = response.length !== 0;
            // $("#posts-container").append(response);
            // window.page++;
        // });
    // }
    var postscontainer = $("#posts-container");
    var body = document.body;
    
    Array.prototype.lastElement = function()
    {
        return this[this.length - 1];
    }
    
    function getPosts()
    {
        if(!working) return;
        if(rq) rq.abort();
        
        rq = $.ajax({
            url: "{{ route('get_posts') }}",
            type: "post",
            data: {page: window.page,category: {{ $category->id }},last_time: lp}
        }).done(function(response, textStatus, jqXHR){
            if(response["success"])
            {
                working = response[0].length !== 0;
                $.each(response[0],function(i,v){
                    let html = $(postToHtml(v,response.onindex));
                    if(window.innerWidth <= 768)
                        postscontainer.append(html);
                    else
                        postscontainer.append(html).masonry('appended', html);
                });
                console.log(response[0].lastElement());
                //lp = response[0].lastElement().last_update;
                window.page++;
            }
        });
    }
    
    window.addEventListener('load',getPosts);
    
    /* https://stackoverflow.com/questions/3962558/javascript-detect-scroll-end */
    $(window).scroll(function (e){
        let scrollTop = this.pageYOffset || body.scrollTop;
        if (body.scrollHeight - scrollTop === parseFloat(body.clientHeight))
            getPosts();
    });
   
</script>
@endpush