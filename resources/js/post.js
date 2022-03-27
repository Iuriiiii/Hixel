function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function postToHtml(item,onindex = false,opid = 0,adm = false)
{
    let html = '<div class="post-container" data-sid="'+ item.sid +'" ' + ( item.iscomment ? 'id="color-' + item.extra + '"' : '' ) + ((item.userid == opid) & item.iscomment ? ' style="background-color:#212121;color:white"' : '') +'>\n';
    
        if(!onindex & !item.iscomment & !item.status === 2)
            html += '<div class="hidebtn" data-sid="' + item.sid + '" data-postid="' + item.id + '"><i class="fa fa-window-close"></i></div>';
        
        html += '<div class="post-left">\n';
        
        if(onindex)
        {
            html += '<a href="' + item.url + '">\n';
        }
        else
        {
            html += '<a href="' + item.front + '" target="_blank">\n';
        }
        
        if(onindex)
        {
            html += '<img class="post-front" src="' + (item.hasfront ? item.thumbnail : '/public/img/default.webp' ) + '">\n';
        }
        else
        {
            if(item.hasfront)
                html += '<img class="post-front" src="' + item.thumbnail + '">\n';
        }
        html += '</a>\n';
        html += '</div>\n';
    
    html += '<div>\n';
    
    if(adm)
    {
        html += '<div class="post-options" onclick="showOptions(this);" data-postid="' + item.id + '"><a href="#ex2" rel="modal:open">&there4;</a></div>\n';
    }
    
    html += '<div class="post-content-container">\n'
    html += '<span class="post-user-nickname"><li class="fas fa-user"></li> <b>' + item.usernickname + '</b></span>\n';
    html += '<a href="' + item.url + '" id="' + item.sid + '">\n';
    
    if(item.iscomment)
    {
        html += '<h3 class="post-title">( ' + item.sid + ' )</h3>\n';
        if(item.userid == opid)
        {
            html += '<div class="tag-op">OP</div>\n';
        }
    }
    else
    {
        html += '<h3 class="post-title"><p>' + escapeHtml(item.title) + '</p></h3>\n';
    }
    
    html += '</a>\n';
    if(!onindex)
    {
        html += '<i class="fa fa-clock-o"' + ( item.iscomment ? ' style="float:right;"' : '' ) + '> ' + item.timediff + '</i>\n';
    }
    
    if(item.iscomment)
    {
        html += '<i class="fa fa-tag" onclick="appendContent(\'>' + item.sid + '\');"></i>\n';
    }
    
    if(item.status == 2)
    {
        html += '<div class="tag-official">Oficial</div>\n'
    }
    else if(item.status == 1)
    {
        html += '<div class="tag-highlight">Destacado</div>\n'
    }
    
    if(!onindex)
    {
        if(item.iscomment)
        {
            $.each(item.comments,function(idx,elm){
                html += '<br><a class="comments" data-sid="' + elm.sid + '" href="' + elm.url + '" target="_self">&gt;&gt;' + elm.sid + '</a>';
            });
            $.each(item.responses,function(idx,elm){
                html += '<br><a class="responses" data-sid="' + elm.sid + '" href="' + elm.url + '" target="_self">&gt;' + elm.sid + '</a>';
            });
        }
        
        html += '<div class="post-content" id="post-content-' + item.sid + '"><p>\n';
        
        html += item.htmlcontent;
        
        html += '</p></div>\n';
    }
    
    if(item.audiosrc !== '')
    {
        html += '<div class="post-audio"><audio src="' + item.audiosrc + '" controls></div>\n';
    }
    
    html += '</div>\n';
    html += '<br>\n';
    html += '<table class="post-buttons-table" style="color:white;width:100%;">\n';
    html += '<tr>\n';
    html += '<td style="text-align:left;width:33;">\n';
    html += '<div class="play-speech" onclick="playspeech(this)" id="play-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-play"></li></div>\n';
    html += '<div class="pause-speech" onclick="speechSynthesis.pause()" id="pause-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-pause"></li></div>\n';
    html += '<div class="resume-speech" onclick="speechSynthesis.resume()" id="resume-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-play"></li></div>\n';
    html += '<div class="stop-speech" onclick="speechSynthesis.cancel()" id="stop-' + item.sid + '" data-contentid="' + item.sid + '"><li class="fa fa-stop"></li></div>\n';
    html += '</td>\n';
    html += '<td style="text-align:center;width:33;">\n';
    
    if(!onindex && !item.iscomment)
    {
        html += '<div class="comments-comment" onclick="$(\'#ex1\').modal({clickClose: false})">Comentar</div>';
    }
    
    if(!item.iscomment)
    {
        if(onindex)
        {
            //html += '<div class="comments-comment" onclick="$(\'#ex1\').modal({clickClose: false})">Comentar</div>\n';
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
    
    return html;
}