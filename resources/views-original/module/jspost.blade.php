    let html = '<div class="post-container"' + ( item.iscomment ? 'id=color-' + $item->extra : '' ) + '>\n';
    if(item.hasfront)
    {
        html += '<div class="post-left">\n';
        if(onindex)
        {
            html += '<a href="' + item.url + '">\n';
        }
        else
        {
            html += '<a href="' + item.front + '" target="_blank">\n';
            
        }
        html += '<img class="post-front" src="' + item.thumbnail + '">\n';
        
        html += '</a>\n';
        html += '</div>\n';
    }
    html += '<div>\n';
    html += '<div class="post-options" onclick="showOptions(this);" data-postid="' + item.id + '"><a href="#ex2" rel="modal:open">&there4;</a></div>\n';
    html += '<div class="post-content-container">\n'
    html += '<span class="post-user-nickname"><li class="fas fa-user"></li> <b>' + item.usernickname + '</b></span>\n';
    html += '<a href="' + item.url '" id="' + item.sid + '">\n';
    if(item.iscomment)
    {
        html += '<h3 class="post-title">( ' + item.sid + ' )</h3>\n';
        if(item.userid == postid)
        {
            html += '<div class="tag-op">OP</div>\n';
        }
    }
    else
    {
        html += '<h3 class="post-title">' + item.title + '</h3>\n';
    }
    html += '</a>\n';
    html += '<i class="fa fa-clock-o"> Hace ' + item.timediff + '</i>\n';
    if(item.iscomment)
    {
        html += '<i class="fa fa-tag" onclick="appendContent(\'>' + item.sid + '\');"></i>\n';
    }
    
    if($item->status == 2)
    {
        html += '<div class="tag-official">Oficial</div>\n'
    }
    else if(item->status == 1)
    {
        html += '<div class="tag-highlight">Destacado</div>\n'
    }
        html += '<div class="post-content" id="post-content-' + item.sid + '"><p>\n';
        if(onindex)
        {
            html += item.content + '...\n';
        }
        else
        {
            html += item.content + '...\n';
        }
        
        html += '</p></div>\n';
        
        if($item->audioid)
        {
            html += '<div class="post-audio"><audio src="{{ DB::table('audios')->where('id',$item->audioid)->first()->file }}" controls></div>\n';
        }
    html += '</div>\n';
    html += '<br>\n';
    html += '<table style="color:white;width:100%;">\n';
    html += '<tr>\n';
    html += '<td style="text-align:left;width:33;">\n';
    html += '<div class="play-speech" onclick="playspeech(this)" id="play-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-play"></li></div>\n';
    html += '<div class="pause-speech" onclick="speechSynthesis.pause()" id="pause-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-pause"></li></div>\n';
    html += '<div class="resume-speech" onclick="speechSynthesis.resume()" id="resume-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-play"></li></div>\n';
    html += '<div class="stop-speech" onclick="speechSynthesis.cancel()" id="stop-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-stop"></li></div>\n';
    html += '</td>\n';
    html += '<td style="text-align:center;width:33;">\n';
                if(!item.iscomment)
                {
                    if(onindex)
                    {
                        html += '<div class="comments-comment" onclick="$(\'#ex1\').modal({clickClose: false})">Comentar</div>\n';
                    }           
                    html += '</td>\n';
                    html += '<td style="text-align:right;width:33;">\n';
                    html += '<div class="comments-counter"><li class="fas fa-comments"><p> ' + item.commentcounter + '</p></li></div>\n';
                }
    html += '</td>\n';
    html += '</tr>\n';
    html += '</table>\n';
    html += '</div>\n';
    html += '</div>\n';