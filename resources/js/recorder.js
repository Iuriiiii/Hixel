const workerOptions = {
	OggOpusEncoderWasmPath:  '/public/js/wasm/OggOpusEncoder.wasm',
	WebMOpusEncoderWasmPath: '/public/js/wasm/WebMOpusEncoder.wasm'
};


window.MediaRecorder = OpusMediaRecorder;
let rrecbtn = $(".record-record")
let rstpbtn = $(".record-stop")
let raudio = $("#record-audio");
let rrembtn = $(".record-remove");

let c = '<i class="fa fa-microphone" data-s="1"></i>';
let p = '<i class="fa fa-circle"></i>';
let r = '<i class="fa fa-microphone"></i>';

function startRecoder(f)
{
    navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
        recorder = new MediaRecorder(stream, {audioBitsPerSecond : 32000, mimeType: 'audio/ogg' }, workerOptions);
        
        recorder.ondataavailable = function(e) {
            rchunks.push(e.data);
        };
        
        recorder.onstop = function(){
            rrecbtn.html(r);
            rstpbtn.hide();
            rrembtn.show();
            let blob = new Blob(rchunks, { 'type' : 'audio/ogg' });
            let objUrl = URL.createObjectURL(blob);

            raudio.attr('src',objUrl);
            raudio.show();
        };
        
        recorder.onpause = function(){
            rrecbtn.html(c);
        };
        
        recorder.onstart = function(){
            rchunks = [];
            raudio.attr('src','');
            rrembtn.hide();
            raudio.hide();
            rrecbtn.html(p);
        };
        
         recorder.onresume = function(){
            rrecbtn.html(p);
        };
        
        if(f) f();
    });
}

rrecbtn.on("click", function(){
    if($(this).html() === p)
    {
        recorder.pause();
    }
    else if($(this).html() === c)
    {
        recorder.resume();
    }
    else
    {
        startRecoder(function(){
            recorder.start();
            rstpbtn.show();
        });
    }
});

rrembtn.on("click",function(){
    raudio.attr('src','');
    rrembtn.hide();
    raudio.hide();
    rrecbtn.html(r);
    rchunks = [];
    recorder.stream.getTracks().forEach(i => i.stop());
});

rstpbtn.on("click", function(){
    recorder.stop();
})