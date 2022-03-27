function download(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

$(".video-bb").on("click",function(){
    var urltype = $(this).data("urltype");
    var v = $(this).data("v");
    
    if(urltype === "ytb")
    {
        $(this).replaceWith('<iframe width="560" height="315" src="https://www.youtube.com/embed/' + v + '?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
    }
    else if(urltype === "dlm")
    {
        $(this).replaceWith('<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"> <iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" type="text/html" src="https://www.dailymotion.com/embed/video/' + v + '?autoplay=1" width="100%" height="100%" allowfullscreen allow="autoplay"> </iframe> </div>');
    }
    $(this).trigger("click");
});

$(".tweet").each(function(i,tweet){
    let id = $(tweet).data('id');
    
    twttr.widgets.createTweet(
    id, tweet, 
    {
        conversation : 'none',    // or all
        cards        : 'hidden',  // or visible 
        linkColor    : '#cc0000', // default is blue
        theme        : 'dark',    // or dark
        lang         : 'es'
    });
});