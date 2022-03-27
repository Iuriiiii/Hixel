if (!"speechSynthesis" in window) {
    $(".post-textspeech-buttons").hide()
}

function playspeech(elm){
    var playbtn = $(elm);
    let sid = playbtn.data("sid");
    var stopbtn = $("#stop-" + sid);
    var pausebtn = $("#pause-" + sid);
    var resumebtn = $("#resume-" + sid);
    var contentid = $("#post-content-" + sid);
    
    speechSynthesis.cancel();
    
    let msg = new SpeechSynthesisUtterance();
    msg.voiceURI = 'native';
    msg.rate = msg.volume = msg.pitch = 1;
    msg.text = contentid.text().trim();
    msg.lang = 'es-ES';
    
    msg.onstart = function()
    {
        playbtn.hide();
        resumebtn.hide();
        pausebtn.css("display","inline-block");
        stopbtn.css("display","inline-block");
    }
    
    msg.onend = function()
    {
        stopbtn.hide();
        pausebtn.hide();
        resumebtn.hide();
        playbtn.css("display","inline-block");
    }
    
    msg.onpause = function()
    {
        playbtn.hide();
        pausebtn.hide();
        resumebtn.css("display","inline-block");
    }
    
    msg.onresume = function()
    {
        playbtn.hide();
        resumebtn.hide();
        pausebtn.css("display","inline-block");
    }
    
    speechSynthesis.speak(msg);
}
