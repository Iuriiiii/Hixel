var $grid;

window.addEventListener("load", function(event){
    if(window.innerWidth <= 768) return;
    
    $grid = $('.posts-container').masonry({
        itemSelector: '.post-container',
        columnWidth: 100,
		fitWidth: true,
		horizontalOrder: true,
		resize: false
    });
    
    let imglist = [];
    
    setInterval(function(){
        $("img").one("load",function(){
            $grid.masonry('layout');
        }).each(function(){
            if(this.complete && imglist.indexOf(this) === (-1)) {
                imglist.push(this);
                $(this).trigger('load'); // For jQuery >= 3.0 
            }
        });
    },1000);
});