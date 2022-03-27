var obj = $(".post-textarea");

function doBB(event)
{
    var selection = obj.getSelection();
    var tag1 = "["+event.data.code + (event.data.param ? "=" + event.data.param : "" ) + "]"
    var tag2 = "[/"+event.data.code+"]"
    var bblen1 = tag1.length;
    var bblen2 = tag2.length;
    
    if(selection.text != "")
    {
        if(event.data.do == "fill")
        {
            obj.surroundSelectedText(tag1,tag2);
            obj.setSelection(selection.start,selection.start + selection.length + bblen2 + bblen1);
        }
        else if(event.data.do == "remove")
        {
            selection.text = selection.text.replace(tag1,"");
            selection.text = selection.text.replace(tag2,"");
            obj.replaceSelectedText(selection.text);
            obj.setSelection(selection.start,selection.start + selection.text.length);
        }
        else if(event.data.do == "apply")
        {
            obj.collapseSelection(false);
            obj.focus();
        }
    }
    else
    {
        if(event.data.do == "apply")
        {
            obj.val(obj.val() + tag1 + tag2);
            obj.focus();
            obj.setSelection(obj.val().length - bblen2);
        }
    }
}

function makeEvent(elmt,bb,param = "")
{
    $(elmt).on("mouseenter",{code: bb,do: "fill",param: param},doBB);
    $(elmt).on("mouseleave",{code: bb,do: "remove",param: param},doBB);
    $(elmt).on("click",{code: bb,do: "apply",param: param},doBB);
}

var hexDigits = new Array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function hex(x) {
    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
}

makeEvent("#bbimg","img");
makeEvent("#bbvid","video");
makeEvent("#bbspo","spoiler");
makeEvent("#bburl","url");
makeEvent("#bbb","b");
makeEvent("#bbcol","color");
makeEvent("#bbund","u");
makeEvent("#bbita","i");
makeEvent("#bbquo","quote");
makeEvent("#bbtch","t");
makeEvent("#bbtwt","tweet");
makeEvent("#bbcnt","center");
makeEvent("#bbrgt","right");

for(let i = 0;i < 7;i++)
{
    let id = "#bbcol" + i.toString();
    makeEvent(id,"color",rgb2hex($(id).css('color')));
}