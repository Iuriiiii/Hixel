@extends('basic')

@push('script')
<script>
    var foundposts = @json($posts);
   
 
    $.each(foundposts,function(i,v){
        let html = $(postToHtml(v,true));
        let container = $("#posts-container");
        if(window.innerWidth <= 768)
        {
            container.append(html);
        }
        else
        {
            container.append(html).masonry('appended', html);
        }
    });
</script>
@endpush