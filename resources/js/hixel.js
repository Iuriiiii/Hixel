import OpusMediaRecorder from 'opus-media-recorder';
import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

(function (root, factory) {
    if (typeof exports === "object") {
        module.exports = factory(root);
    } else if (typeof define === "function" && define.amd) {
        define([], factory);
    } else {
        root.LazyLoad = factory(root);
    }
}) (typeof global !== "undefined" ? global : this.window || this.global, function (root) {

    "use strict";

    if (typeof define === "function" && define.amd){
        root = window;
    }

    const defaults = {
        src: "data-src",
        srcset: "data-srcset",
        selector: ".lazyload",
        root: null,
        rootMargin: "0px",
        threshold: 0
    };

    /**
    * Merge two or more objects. Returns a new object.
    * @private
    * @param {Boolean}  deep     If true, do a deep (or recursive) merge [optional]
    * @param {Object}   objects  The objects to merge together
    * @returns {Object}          Merged values of defaults and options
    */
    const extend = function ()  {

        let extended = {};
        let deep = false;
        let i = 0;
        let length = arguments.length;

        /* Check if a deep merge */
        if (Object.prototype.toString.call(arguments[0]) === "[object Boolean]") {
            deep = arguments[0];
            i++;
        }

        /* Merge the object into the extended object */
        let merge = function (obj) {
            for (let prop in obj) {
                if (Object.prototype.hasOwnProperty.call(obj, prop)) {
                    /* If deep merge and property is an object, merge properties */
                    if (deep && Object.prototype.toString.call(obj[prop]) === "[object Object]") {
                        extended[prop] = extend(true, extended[prop], obj[prop]);
                    } else {
                        extended[prop] = obj[prop];
                    }
                }
            }
        };

        /* Loop through each object and conduct a merge */
        for (; i < length; i++) {
            let obj = arguments[i];
            merge(obj);
        }

        return extended;
    };

    function LazyLoad(images, options, cbp, cb) {
        this.settings = extend(defaults, options || {});
        this.images = images || document.querySelectorAll(this.settings.selector);
        this.observer = null;
        this.cbp = cbp;
        this.cb = cb;
        this.init();
    }

    LazyLoad.prototype = {
        init: function() {

            /* Without observers load everything and bail out early. */
            if (!root.IntersectionObserver) {
                this.loadImages();
                return;
            }

            let self = this;
            let observerConfig = {
                root: this.settings.root,
                rootMargin: this.settings.rootMargin,
                threshold: [this.settings.threshold]
            };

            this.observer = new IntersectionObserver(function(entries) {
                Array.prototype.forEach.call(entries, function (entry) {
                    if (entry.isIntersecting) {
                        self.observer.unobserve(entry.target);
                        self.workOn(entry.target);
                    }
                });
            }, observerConfig);

            Array.prototype.forEach.call(this.images, function (image) {
                self.observer.observe(image);
            });
        },

        workOn: function(entry)
        {
            let src = entry.getAttribute(this.settings.src);
            let srcset = entry.getAttribute(this.settings.srcset);

            if(this.cbp)
            {
                this.cbp(entry);
            }

            if ("img" === entry.tagName.toLowerCase()) {
                if (src) {
                    entry.src = src;
                }
                if (srcset) {
                    entry.srcset = srcset;
                }
            } else {
                entry.style.backgroundImage = "url(" + src + ")";
            }

            if(this.cb)
            {
                this.cb(entry);
            }
        },

        loadAndDestroy: function () {
            if (!this.settings) { return; }
            this.loadImages();
            this.destroy();
        },

        loadImages: function () {
            if (!this.settings) { return; }

            //let self = this;
            Array.prototype.forEach.call(this.images, function (image) {
                this.workOn(image);
            });
        },

        destroy: function () {
            if (!this.settings) { return; }
            this.observer.disconnect();
            this.settings = null;
        }
    };

    root.lazyload = function(images, options, cbp, cb) {
        return new LazyLoad(images, options, cbp, cb);
    };

    if (root.jQuery) {
        const $ = root.jQuery;
        $.fn.lazyload = function (options) {
            options = options || {};
            options.attribute = options.attribute || "data-src";
            new LazyLoad($.makeArray(this), options);
            return this;
        };
    }

    return LazyLoad;
});


(function($){
	
	$.fn.getStyles = function(only, except) {
		
		// the map to return with requested styles and values as KVP
		var product = {};
		
		// the style object from the DOM element we need to iterate through
		var style;
		
		// recycle the name of the style attribute
		var name;
		
		// if it's a limited list, no need to run through the entire style object
		if (only && only instanceof Array) {
			
			for (var i = 0, l = only.length; i < l; i++) {
				// since we have the name already, just return via built-in .css method
				name = only[i];
				product[name] = this.css(name);
			}
			
		} else {
		
			// prevent from empty selector
			if (this.length) {
				
				// otherwise, we need to get everything
				var dom = this.get(0);
				
				// standards
				if (window.getComputedStyle) {
					
					// convenience methods to turn css case ('background-image') to camel ('backgroundImage')
					// var pattern = /\-([a-z])/g;
					// var uc = function (a, b) {
					// 		return b.toUpperCase();
					// };			
					// var camelize = function(string){
					// 	return string.replace(pattern, uc);
                    // };

                    const camelize = (text, separator = '-') => text
                        .split(separator)
                        .reduce((acc, cur) => `${acc}${cur.charAt(0).toUpperCase() + cur.slice(1)}`, '');

                    // var capitalize = (word) => {
                    //     return `${word.slice(0, 1).toUpperCase()}${word.slice(1).toLowerCase()}`
                    // }

                    // var camelize = (text, separator = '-') => {
                    //     const words = text.split(separator)
                    //     const result = [words[0]]
                    //     words.slice(1).forEach((word) => result.push(capitalize(word)))
                    //     return result.join('')
                    //   }
					
					// make sure we're getting a good reference
					if (style = window.getComputedStyle(dom, null)) {
						var camel, value;
						// opera doesn't give back style.length - use truthy since a 0 length may as well be skipped anyways
						if (style.length) {
							for (var i = 0, l = style.length; i < l; i++) {
								name = style[i];
								camel = camelize(name);
								value = style.getPropertyValue(name);
								product[camel] = value;
							}
						} else {
							// opera
							for (name in style) {
								camel = camelize(name);
								value = style.getPropertyValue(name) || style[name];
								product[camel] = value;
							}
						}
					}
				}
				// IE - first try currentStyle, then normal style object - don't bother with runtimeStyle
				else if (style = dom.currentStyle) {
					for (name in style) {
						product[name] = style[name];
					}
				}
				else if (style = dom.style) {
					for (name in style) {
						if (typeof style[name] != 'function') {
							product[name] = style[name];
						}
					}
				}
			}
		}
		
		// remove any styles specified...
		// be careful on blacklist - sometimes vendor-specific values aren't obvious but will be visible...  e.g., excepting 'color' will still let '-webkit-text-fill-color' through, which will in fact color the text
		if (except && except instanceof Array) {
			for (var i = 0, l = except.length; i < l; i++) {
				name = except[i];
				delete product[name];
			}
		}
		
		// one way out so we can process blacklist in one spot
		return product;
	
	};
	
	// sugar - source is the selector, dom element or jQuery instance to copy from - only and except are optional
	$.fn.copyCSS = function(source, only, except) {
		var styles = $(source).getStyles(only, except);
		this.css(styles);
		
		return this;
	};
	
})(jQuery);

class HixelAPI {
    constructor()
    {
        this.apiUrl = '/api/';

        this.invoke = function(action,data,callback){
            $.ajax({url: this.apiUrl + action,method: 'post',data: data
            }).done(function(res){
                if(!res.success)
                {
                    error(res.description);
                    return;
                }
    
                if(callback)
                    callback();
            });
        };

        this.postAction = function(data,callback){
            this.invoke('postaction',data,callback);
        };
    }
}

// Autor: Iuri
class Temporizer {

    constructor()
    {
        this.time = 0;
        this.paused = false;
        this.interval = null;

        this.start = function()
        {
            if(this.interval) {return;}

            this.time = 0;

            this.interval = setInterval(function(self){
                if(self.paused) {return;}
                self.time += 100;
            },100,this);
        };

        this.restart = function()
        {
            this.time = 0;
        };

        this.pause = function()
        {
            this.paused = true;
        };

        this.stop = function(type = 'ms')
        {
            if(this.interval) {clearInterval(this.interval);}
            this.interval = null;

            return this.getTime(type);
        };

        this.resume = function()
        {
            this.paused = false;
        };

        this.getTime = function(type = 'ms')
        {
            switch(type)
            {
                case 'ms':
                case 'miliseconds':
                    return this.time;
                    break;
                case 'sec':
                case 'seconds':
                    return this.time / 1000;
                    break;
                case 'min':
                case 'minutes':
                    return this.time / 1000 / 60;
                    break;
            }
        };
    }
}

(function(a,b){"function"==typeof define&&define.amd?define([],b):"object"==typeof exports?module.exports=b():window.Notifier=b()})(this,function(){var a={autopush:!0,zindex:9999,default_time:4500,vanish_time:300,fps:30,position:"bottom-right",direction:"bottom",success:{classes:"notifyjs-success",textColor:"#155724",borderColor:"#c3e6cb",backgroundColor:"#d4edda",progressColor:"#155724",iconColor:"#155724",icon:"<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"8\" height=\"8\" viewBox=\"0 0 8 8\"><path d=\"M6.41 0l-.69.72-2.78 2.78-.81-.78-.72-.72-1.41 1.41.72.72 1.5 1.5.69.72.72-.72 3.5-3.5.72-.72-1.44-1.41z\" transform=\"translate(0 1)\" /></svg>"},error:{classes:"notifyjs-danger",textColor:"#721c24",borderColor:"#f5c6cb",backgroundColor:"#f8d7da",progressColor:"#721c24",iconColor:"#721c24",icon:"<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"8\" height=\"8\" viewBox=\"0 0 8 8\"><path d=\"M1.41 0l-1.41 1.41.72.72 1.78 1.81-1.78 1.78-.72.69 1.41 1.44.72-.72 1.81-1.81 1.78 1.81.69.72 1.44-1.44-.72-.69-1.81-1.78 1.81-1.81.72-.72-1.44-1.41-.69.72-1.78 1.78-1.81-1.78-.72-.72z\" /></svg>"},warning:{classes:"notifyjs-warning",textColor:"#856404",borderColor:"#fff3cd",backgroundColor:"#ffeeba",progressColor:"#856404",iconColor:"#856404",icon:"<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"8\" height=\"8\" viewBox=\"0 0 8 8\"><path d=\"M3.09 0c-.06 0-.1.04-.13.09l-2.94 6.81c-.02.05-.03.13-.03.19v.81c0 .05.04.09.09.09h6.81c.05 0 .09-.04.09-.09v-.81c0-.05-.01-.14-.03-.19l-2.94-6.81c-.02-.05-.07-.09-.13-.09h-.81zm-.09 3h1v2h-1v-2zm0 3h1v1h-1v-1z\" /></svg>"},info:{classes:"notifyjs-info",textColor:"#0c5460",borderColor:"#d1ecf1",backgroundColor:"#bee5eb",progressColor:"#0c5460",iconColor:"#0c5460",icon:"<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"8\" height=\"8\" viewBox=\"0 0 8 8\"><path d=\"M3 0c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1zm-1.5 2.5c-.83 0-1.5.67-1.5 1.5h1c0-.28.22-.5.5-.5s.5.22.5.5-1 1.64-1 2.5c0 .86.67 1.5 1.5 1.5s1.5-.67 1.5-1.5h-1c0 .28-.22.5-.5.5s-.5-.22-.5-.5c0-.36 1-1.84 1-2.5 0-.81-.67-1.5-1.5-1.5z\" transform=\"translate(2)\"/></svg>"}},b=function(a,b,c,d,e,f,g){switch(this.pushed=!1,this.element=document.createElement("div"),this.element.className=c.classes||"",this.element.style.display="none",this.element.style.position="relative",this.element.style.padding="1em 2em 1em 2.5em",a.options.direction){case"top":this.element.style.marginTop="0.5em";break;case"bottom":default:this.element.style.marginBottom="0.5em";}if(this.element.style.width="100%",this.element.style.borderWidth="1px",this.element.style.borderStyle="solid",this.element.style.borderColor=c.borderColor,this.element.style.boxSizing="border-box",this.element.style.backgroundColor=c.backgroundColor,"undefined"!=typeof c.icon){var h=document.createElement("div");h.style.position="absolute",h.style.top="50%",h.style.left="10px",h.style.transform="translateY(-50%)",h.innerHTML=c.icon,-1==c.icon.indexOf("<img")?-1!=c.icon.indexOf("<svg ")&&(h.childNodes[0].style.width="16px",h.childNodes[0].style.height="16px","undefined"!=typeof c.iconColor&&(h.childNodes[0].style.fill=c.iconColor)):(h.childNodes[0].style.width="16px",h.childNodes[0].style.height="16px"),"undefined"!=typeof c.iconClasses&&(h.childNodes[0].className+=c.iconClasses),this.element.appendChild(h)}var i=document.createElement("span");i.style.color=c.textColor,i.innerHTML=b,this.element.appendChild(i);var j=document.createElement("p");switch(j.className="progress",j.style.position="absolute",j.style.bottom=0,j.style.left=0,j.style.right="100%",j.style.height="2px",j.style.content=" ",j.style.backgroundColor=c.progressColor,j.style.marginBottom=0,this.element.appendChild(j),a.options.direction){case"top":a.container.insertBefore(this.element,a.container.childNodes[0]);break;case"bottom":default:a.container.appendChild(this.element);}this.callback=g;var k=this;this.push=function(){if(!k.pushed){k.pushed=!0;var a=0,b=1e3/f;k.element.style.display="block",k.interval=setInterval(function(){a++;var c=100*(1-b*a/d);j.style.right=c+"%",0>=c&&("function"==typeof g&&g(),k.clear())},b)}},this.clear=function(){if(k.pushed){var a=1e3/f,b=1;"undefined"!=typeof k.interval&&clearInterval(k.interval),k.interval=setInterval(function(){b-=1/(e/a),k.element.style.opacity=b,0>=b&&(clearInterval(k.interval),k.destroy())},a)}},this.destroy=function(){k.pushed&&(k.pushed=!1,"undefined"!=typeof k.interval&&clearInterval(k.interval),a.container.removeChild(k.element))}};return function(c){if(this.options=Object.assign({},a),this.options=Object.assign(this.options,c),this.container=document.getElementById("notifyjs-container-"+this.options.position),null===this.container){switch(this.container=document.createElement("div"),this.container.id="notifyjs-container-"+this.options.position,this.container.style.zIndex=this.options.zindex,this.container.style.position="fixed",this.container.style.maxWidth="304px",this.container.style.width="100%",this.options.position){case"top-left":this.container.style.top=0,this.container.style.left="0.5em";break;case"top-right":this.container.style.top=0,this.container.style.right="0.5em";break;case"bottom-left":this.container.style.bottom=0,this.container.style.left="0.5em";break;case"bottom-right":default:this.container.style.bottom=0,this.container.style.right="0.5em";}document.getElementsByTagName("body")[0].appendChild(this.container)}this.notify=function(a,c,d,e){if("undefined"==typeof this.options[a])return void console.error("Notify.js: Error, undefined '"+a+"' notification type");"undefined"==typeof d&&(d=this.options.default_time);var f=new b(this,c,this.options[a],d,this.options.vanish_time,this.options.fps,e);return this.options.autopush&&f.push(),f}}});

Array.prototype.lastElement = function()
{
    return this[this.length - 1];
}

Array.prototype.randomElement = function()
{
    return this[Math.floor(Math.random() * this.length)];
}

Array.prototype.clear = function()
{
    "strict mode";
    this.length = 0;
}

if(!"speechSynthesis" in window)
    $(".post-speech-buttons").hide();

function getRandomEspVoice()
{
    let voices = speechSynthesis.getVoices();
    let ret = [];

    for(let i = 0;i < voices.length;i++)
    {
        if(voices[i].lang === 'es-ES')
            ret.push(voices[i]);
    }

    return ret.randomElement();
}
    
window.playspeech = function(elm) {
    let playbtn = $(elm);
    let sid = playbtn.data("sid");
    let stopbtn = $("#stop-" + sid);
    let pausebtn = $("#pause-" + sid);
    let resumebtn = $("#resume-" + sid);
    let contentid = $("#p-content-" + sid);

    speechSynthesis.cancel();

    let msg = new SpeechSynthesisUtterance();
    //msg.voiceURI = 'native';
    msg.voice = getRandomEspVoice();
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

////////////////////////////////////////////////////////////////////////////////////////////////////////
window.onload = function()
{
    let rrecbtn = $('.record-record')
    let rstpbtn = $('.record-stop')
    let raudio = $('#record-audio');
    let rrembtn = $('.record-remove');
    let rchunks = [];
    let c = '<i class="fa fa-microphone" data-s="1"></i>';
    let p = '<i class="fa fa-circle"></i>';
    let r = '<i class="fa fa-microphone"></i>';
    let s = '<i class="fa fa-spinner">';
    let o = '<i class="fa fa-paper-plane-o">';
    let recorder = null;
    let req = null;
    let getposts = true;
    let lastposttime = null;
    let npage = 1;
    let ocontent = $('#content');
    let txarea = $('#form-comment textarea');
    let onboard = urlocation === 'board';
    let defaultimg = '/public/img/l/default.webp';
    var notifier = new Notifier();
    let gettingposts = false;
    let iplus18 = $('i[name="imgstatus"]');
    let iplusUrl = $('i[name="imgurlstatus"]');
    let cs = $('#categoryselector');
    let ts = $('#themeselector');
    let hixel = new HixelAPI();
    let uploadlabel = $('.upload-label');
    let fpip = $(".form-post-image-preview");
    let previewc = $('.form-post-image-preview i');
    let urlData = {verified: false,type: 'i',url: ''};
    let fpipi = $('.form-post-image-preview img');
    let imgmodal = $('#imgmodal');
    let modalimg = $('#fullsizeimg');

    window.pid = 0;

    function getClickedPublication()
    {
        return ( onboard ? `.preview[data-pid="${pid}"]` : `.post-container[data-pid="${pid}"]` );
    }

    function loadContextMenu()
    {
        let selector = onboard ? '.preview' : '.post-container';
        let options;
        let getCategories = function(){
            let ret = {};
            
            categories.forEach(function(v,i,a){
                ret[v.id] = v.identifier;
            });

            return ret;
        };

        let getStatus = function(){
            let ret = {
                0: 'Estandar',
                3: 'Resuelto',
                4: 'Verificado',
                5: 'Subnormal',
                6: 'Genio',
                7: 'This',
                8: 'Confirmado',
                9: 'Fake',
                10: 'Bait',
                11: 'Ultrajado',
                12: 'Importante'
            };

            if(urstatus === 1)
            {
                ret[1] = 'Destacado';
                ret[2] = 'Oficial';
            }

            return ret;
        };

        switch(urstatus)
        {
            case 0:
                return;
            case 1:
                $.contextMenu({
                    selector: selector,
                    callback: function(key,opt){
                        switch(key)
                        {
                            case 'banapply':
                                options = opt.inputs['bantime'].$input[0].options;
                                if(opt.inputs['bandelyesno'].$input[0].checked)
                                {
                                    hixel.postAction({action: 'bananddel',postid: pid, bantime: options[options.selectedIndex].value, deleteall: opt.inputs['bandelallyesno'].$input[0].checked},function(){
                                        opt.$trigger.hide();
                                        success('El usuario fue baneado y la publicación eliminada.');
                                    });
                                }
                                else
                                {
                                    hixel.postAction({action: 'ban',postid: pid, bantime: options[options.selectedIndex].value},function(){
                                        success('El usuario fue baneado.');
                                    });
                                }
                                break;
                            case 'delpost':
                                hixel.postAction({action: 'delete',postid: pid},function(){
                                    opt.$trigger.hide();
                                    success('La publicación fue eliminada.');
                                    //if(!onboard)
                                        //location.href = '/';
                                });
                                break;
                            case 'apppost':
                                options = opt.inputs['movpost'].$input[0].options;
                                let ncategory = options[options.selectedIndex].value;
                                options = opt.inputs['stapost'].$input[0].options;
                                let nstatus =  options[options.selectedIndex].value - 0;
                                hixel.postAction({action: 'modifypost',postid: pid,category: ncategory, status: nstatus},function(){
                                    if(onboard)
                                    {
                                        $(`${getClickedPublication()} .p-category`).text(categories[ncategory - 1].diminutive);
                                        $(getClickedPublication()).data('type',nstatus);
                                        $(`${getClickedPublication()} .p-status`).text(getStatus()[nstatus]);
                                    }
                                    else
                                    {
                                        let pub = $(`${getClickedPublication()} .post-status`);
                                        pub.data('type',nstatus);
                                        pub.text(getStatus()[nstatus]);
                                    }

                                    success('Publicación modificada con éxito.');
                                });
                                break;
                            case 'banimage':
                            case 'delimage':
                                hixel.postAction({action: key,postid: pid},function(){
                                    $(`${getClickedPublication()} img`).attr('src',defaultimg);
                                    success('Imagen modificada con éxito.');
                                });
                                break;
                            case 'cenimg':
                                hixel.postAction({action: 'censoreimg',postid: pid},function(){
                                    $(`${getClickedPublication()} img`).attr('type','censored');
                                    success('Imagen modificada con éxito.');
                                });
                                break;
                            case 'uncenimg':
                                hixel.postAction({action: 'uncensoreimg',postid: pid},function(){
                                    $(`${getClickedPublication()} img`).removeAttr('type');
                                    success('Imagen modificada con éxito.');
                                });
                                break;
                            case 'delallpost':
                                hixel.postAction({action: 'delallpost',postid: pid},function(){
                                    let usid = $(`${getClickedPublication()}`).data('usid');
                                    $(`${getClickedPublication()}[data-usid="${usid}"]`).hide();
                                    success('Las publicaciones fueron borradas con éxito.');
                                });
                                break;
                        }
                    },
                    items: {
                        ban: {
                            name: 'Banear...',
                            items: {
                                bandelyesno: {name: 'Borrar',type: 'checkbox',selected: false},
                                bandelallyesno: {name: 'Borrar Todo',type: 'checkbox',selected: false},
                                bantime: {name: 'Tiempo:', type: 'select',options: {
                                    1: '1 Hora',
                                    12: '12 Hora',
                                    24: '24 Hora',
                                    48: '2 Días',
                                    168: '7 Días',
                                    336: '14 Días',
                                    720: '30 Días',
                                    2399976: 'Permanentemente'
                                },selected: 1},
                                banapply: {name: 'Aplicar'}
                            }
                        },
                        post: {
                            name: 'Publicación...',
                            items: {
                                delpost: {name: 'Borrar'},
                                delallpost: {name: 'Borrar todas del usuario'},
                                movpost: {name: 'Mover a...', type: 'select',options: getCategories(), selected: cid},
                                stapost: {name: 'Estado...',type: 'select',options: getStatus(), selected: 0},
                                apppost: {name: 'Aplicar'}
                            }
                        },
                        img: {
                            name: 'Imagen...',
                            items: {
                                delimage: {name: 'Borrar'},
                                banimage: {name: 'Banear'},
                                cenimg: {name: 'Censurar'},
                                uncenimg: {name: 'Descensurar'},
                            }
                        }
                    }
                });
                break;
        }
    }

    

    function fadeIn(element, duration = 1000) {
        element.style.display = 'inline-block';
        element.style.opacity = 0;
        var last = +new Date();
        var tick = function() {
          element.style.opacity = +element.style.opacity + (new Date() - last) / duration;
          last = +new Date();
          if (+element.style.opacity < 1) {
            (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
          }
        };
        tick();
      }

    let lazyimgs = [];

    function mylazyload(){
        lazyload(undefined,undefined,undefined,function(entry) {
            if(lazyimgs.includes(entry))
                return;
            
            fadeIn(entry);
            lazyimgs.push(entry);
        });
    }

    function loadThemeList()
    {
        for(let i = 0;i < themes.length;i++)
        {
            let theme = themes[i];

            ts.append(`<option ${theme.id === urtheme ? 'selected ' : '' }value="${theme.id}" data-path="${theme.path}">${theme.name}</option>`);
        }
    }

    function loadCategoryList()
    {
        for(let i = 0;i < categories.length;i++)
        {
            let category = categories[i];

            if(urstatus === 0 & category.access === 1)
                continue;

            cs.append(`<option ${category.id === cid ? 'selected ' : '' }value="${category.diminutive}">${ category.identifier }</option>`);
        }
    }

    function toggleChecked(elm = null)
    {
        let obj = $(this || elm);

        if(obj.attr('value') == 1)
        {
            obj.removeAttr('value');
            obj.attr('class','fa fa-square');
            return false;
        }
        else
        {
            obj.attr('value',1);
            obj.attr('class','fa fa-check-square');
            return true;
        }
    }

    iplusUrl.on('click',function()
    {
        if(toggleChecked(this))
        {
            let obj = $('<input type="text" placeholder="Enlace" name="fronturl">');

            uploadlabel.removeAttr('for');
            uploadlabel.html(obj);

            obj.on('input',function(){
                let url = $(this).val();

                let verifyImg = function(data,obj)
                {
                    if(obj.url.match('ytimg') !== (-1))
                    {
                        if(obj.img.naturalWidth === 120 && obj.img.naturalHeight === 90)
                        {
                            urlData.verified = false;
                            error('Parece ser que el vídeo no existe.');
                            previewc.trigger('click');
                            return;
                        }
                    }

                    success('Imagen obtenida con éxito.');
                    fpipi.attr('src',obj.url);
                    fpip.show();
                    urlData.verified = true;
                    urlData.url = data.url;
                    urlData.type = data.type;
                };

                let verifyImgError = function()
                {
                    error('No se pudo descargar la imagen.');
                    previewc.trigger('click');
                    urlData.verified = false;
                };

                if(isValidURL(url))
                {
                    switch(true)
                    {
                        case isDirectImageUrl(url):
                            downloadImage(url).then(verifyImg.bind(obj,{url: url,type: 'i'}),verifyImgError);
                            break;
                        case isYoutubeUrl(url):
                            let v;

                            if((v = getV(url)) === false)
                            {
                                error('Enlace de youtube inválido.');
                                return;
                            }

                            downloadImage(`https://i.ytimg.com/vi/${v}/hqdefault.jpg`).then(verifyImg.bind(obj,{url: v,type: 'ytb'}),verifyImgError);
                            break;
                        case isDaylimotionUrl(url):
                            break;
                    }
                }
            });
        }
        else
        {
            uploadlabel.attr('for','form-post-image-file');
            uploadlabel.html('Subir archivo <i class="fa fa-upload" aria-hidden="true"></i>');
        }
    });

    iplus18.on('click',toggleChecked);

    $('#reorder').on('click',function()
    {
        ts.toggle();
        cs.toggle();
    });

    $('#search-button').on('click',function()
    {
        let url = $('#search-text').val();

        if(url.length === 0)
        {
            alert('Se esperaban palabras a buscar.');
            return;
        }

        location.href = '/search?words=' + encodeURI(url);
    });

    /* https://stackoverflow.com/questions/14313183/javascript-regex-how-do-i-check-if-the-string-is-ascii-only */
    function isASCII(str, extended = true)
    {
        return (extended ? /^[\x00-\xFF]*$/ : /^[\x00-\x7F]*$/).test(str);
    }

    cs.on('change',function()
    {
        location.href = '/' + $(this).val();
    });

    ts.on('change',function()
    {
        let csstheme = $('#theme');
        let obj = $(this);
        let tid = obj.val();
        let path = obj.find(':selected').data('path');

        $.ajax({
            url: '/api/changetheme',
            method: 'post',
            data: {themeid: tid}
        }).done(function(r,t,h){
            csstheme.attr('href',path);
            success('Tema modificado con éxito.');
        });
    });
    
    $('#hidecategory').on('click',function()
    {
        $(this).attr('class','fa fa-spinner');
        
        $.ajax({
            url: '/api/hidecategory',
            method: 'post',
            data: {categoryid: cid}
        }).done(function(r,t,h)
        {
            if(!r['success'])
            {
                error(r['description']);
                return;
            }
            
            $('#hidecategory').attr('class', r['hidden'] ? 'fa fa-toggle-off' : 'fa fa-toggle-on' );
            
            if(r['hidden'])
                success('Esta categoría fue escondida del indice general.');
            else
                success('Esta categoría ahora es visible en el indice general.');
        });
    });
    
    function statusToString(n)
    {
        let status = [
            '',
            'Destacado',
            'Oficial',
            'Resuelto',
            'Verificado',
            'Subnormal',
            'Genio',
            'This',
            'Confirmado',
            'Fake',
            'Bait',
            'Ultrajado',
            'Importante'
        ];

        return status[n];
    }

    window.reportPost = function(pid)
    {
        if(confirm('¿Reportar?\nUn mal uso de esta herramienta puede ser motivo de baneo permanente. ¿Continuar?'))
        {
            markPostFromIndex(null,pid,3);
        }
    };

    window.markPostFromIndex = function(elm,pid,type)
    {
        if(elm)
        {
            elm = $(elm);
            var par = elm.parent().parent().parent();
        }
        let msg;

        $.ajax({
            url: '/api/markpost',
            method: 'post',
            data: {postid: pid,type: type}
        }).done(function(r,t,h)
        {
            if(!r['success'])
            {
                alert(r['description']);
                return;
            }

            switch(type)
            {
                case 1:
                    if(r['newtype'] === 0)
                        msg = 'Publicación removida de escondidos.';
                    else
                        msg = 'Publicación escondida.';
                    par.hide();
                    break;
                case 2:
                    if(r['newtype'] === 0)
                    {
                        msg = 'Publicación removida de favoritos.';
                        elm.attr('class','fa fa-star-o');
                        elm.css('color','white');
                    }
                    else
                    {
                        msg = 'Publicación añadida a favoritos.';
                        elm.attr('class','fa fa-star');
                        elm.css('color','yellow');
                    }
                    break;
                case 3:
                    msg = 'Publicación reportada al STAFF exitosamente.';
                    break;
            }

            success(msg);
        });
        
    };
    
    function renderPostsToIndex(posts)
    {
        for(let i=0;i < posts.length;i++)
        {
            let elm = renderPost(posts[i]);
            ocontent.append(elm);
        }
    }

    function getPostsForIndex()
    {
        if(!getposts)
        {
            alert("No hay más publicaciones.");
            return;
        }

        if(gettingposts)
            return;
        
        $.ajax({
            url: '/api/getposts',
            method: 'post',
            data: {page: npage, category: cid, last_time: lastposttime},
        }).done(function(r,t,h){
            if(!r['success'])
            {
                error(r['description']);
                getposts = false;
                return;
            }
            
            renderPostsToIndex(r[0]);

            npage++;
            gettingposts = false;

            mylazyload();
        }).fail(function(r,t,h){
            error('Se produjo un error interno en el servidor, contacte con el administrador.');
            gettingposts = false;
        });

        gettingposts = true;
    }

    window.addTag = function(tag)
    {
        if(tag.indexOf('>') === (-1))
            tag = '>' + tag;

        if(txarea.val().indexOf(tag) !== (-1))
            return;
        
        let char = txarea.val().substr(txarea.val().length -1);

        if(char !== "\n" & char !== '')
            txarea.val(txarea.val() + "\r\n");
        
        txarea.val(txarea.val() + tag + "\r\n");
    };

    window.playVideo = function(t)
    {
        let p = $(t);
        let urltype = p.data("urltype");
        let v = p.data("v");

        switch(urltype)
        {
            case 'ytb':
                p.parent().replaceWith(`<iframe width="100%" height="60%" src="https://www.youtube.com/embed/${v}?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`);
                break;
            case 'dlm':
                p.parent().replaceWith(`<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"> <iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" type="text/html" src="https://www.dailymotion.com/embed/video/${v}?autoplay=1" width="100%" height="100%" allowfullscreen allow="autoplay"></iframe></div>`);
                break;
        }

        p.trigger("click");
    };

    // $(document).on('mousewheel',function(){

    // });

    window.onOverShowComment = function(elme,sid)
    {
        let cmtpp = $('#comment-popup');

        function clearcmtpp()
        {
            cmtpp.hide();
            cmtpp.html('');
            //cmtpp.removeAttribute('style');
            cmtpp.css('display','');
        }

        $(elme).on('mousedown',clearcmtpp);
        
        $(elme).on('mouseleave',clearcmtpp); 

        $(elme).on('mousewheel',clearcmtpp)

        let rc = elme.getBoundingClientRect();
        let cmt = $('#' + sid);

        elme = $(elme);
        
        let left = rc.left + elme.width() + 10;
        let top = rc.top;
        let width = document.body.clientWidth * 0.4;
        let x2 = left + width;
        let x2minusbw = x2 - document.body.clientWidth;
        cmtpp.css(cmt.getStyles());

        //console.log(left,width,x2,x2minusbw);

        if((x2minusbw > 0) && (x2minusbw <= 200))
            width -= (x2minusbw + 25);
        else if(x2 > document.body.clientWidth)
            left = rc.left - width - 20;
        
        cmtpp.html(cmt.html());

        let y2 = top + cmtpp.outerHeight();

        if(y2 > document.body.clientHeight)
            top -= (y2 - document.body.clientHeight);

        cmtpp.css({position:'fixed',display:'block', width: width,zIndex:2,left:left,top:top});
    };

    window.setImgUrl = function(obj,url)
    {
        let jqobj = $(obj);

        if(jqobj.attr('src') !== url)
        {
            jqobj.removeAttr('type'); // Si existe, lo elimina
            jqobj.attr('style','cursor:initial');
            jqobj.attr('src',url);
            fadeIn(obj);
        }
    };

    function renderPost(json)
    {
        let frontimage = json.preview || '/public/img/default.webp';
        let status = statusToString(json.status);
        let html =
        `<div class="preview" data-type="${json.status}" data-pid="${json.id}" onmousedown="pid = ${json.id}">
            <a href="${json.url}"><img ${ json.censoredf ? 'type="censored" ' : '' }class="lazyload" data-src="${frontimage}"></a>
            <div class="p-left">
                <div class="p-category">${json.category}</div>
                <div class="p-hide"><i class="fa fa-eye" onclick="markPostFromIndex(this,${json.id},1)"></i></div>
                <div class="p-hide"><i class="fa fa-star${json.fav ? '' : '-o'}" onclick="markPostFromIndex(this,${json.id},2)" ${json.fav ? 'style="color:yellow;"' : ''}></i></div>
                <div class="p-report"><i class="fa fa-exclamation" onclick="reportPost(${json.id})"></i></div>
            </div>
            <div class="p-c-counter"><i class="fa fa-comments">${json.cc}</i></div>
            <div class="p-status">${status}</div>
            <a href="${json.url}"><div class="p-title">${json.title}</div></a>
        </div>`;
        
        return $(html);
    }

    window.bbParse = function(code)
    {
        let bbcodes = [
            /\[b\](.*?)\[\/b\]/i,
        ];

        let bbreplaces = [
            '<b>$1</b>'
        ];

        bbcodes.forEach(function(elm,idx){
            console.log(elm);
            code = code.replace(elm,bbreplaces[idx]);
        });

        return code;
    };

    window.showImageOnModal = function(url)
    {
        modalimg.attr('src',url);
        imgmodal.show();
    };

    function writePost(post)
    {
        let comments = '';
        let responses = '';
        
        let status = statusToString(post.status);
        let writeReferences = function(arr,iclass)
        {
            let tmp = '';

            for(let i = 0;i < arr.length;i++)
            {
                if(i !== 0 & i !== arr.length)
                    tmp += ' ';
                tmp += `<a href="${arr[i].url}"><i class="${iclass}" onmouseover="onOverShowComment(this,'${arr[i].sid}')">${arr[i].sid}</i></a>`;
            }

            return tmp;
        }

        comments = writeReferences(post.comments,'fa fa-caret-right');
        responses = writeReferences(post.responses,'fa fa-caret-left');
        //class="lazyload" data-
        let censored = post.front.censored;
        let img = '';

        // Youtube video
        switch(post.front.type)
        {
            case 1: // Imagen
                img =
                `<div class="front-image"><img ${ censored ? 'type="censored" ' : '' }${ post.front.animated | censored ? `style="cursor:pointer;" onclick="setImgUrl(this,'${post.front.hq}')" ` : '' }src="${post.front.sq}">
                    <div class="img-bar">
                        ${ post.front.animated ? '<i class="fa fa-film"></i>&nbsp;' : '' }<a href="${post.front.hq}" target="_blank"><i class="fa fa-expand"></i></a>${ censored ? '<i class="fa fa-exclamation-triangle"></i>' : '' }
                    </div>
                </div>`;
                break;
            case 2: // Youtube
                img =
                `<div class="video-bb">
                    <img ${ censored ? 'type="censored"' : '' }src="https://img.youtube.com/vi/${post.front.extra}/mqdefault.jpg">
                    <div class="play-button" data-urltype="ytb" data-v="${post.front.extra}" onclick="playVideo(this)">
                        <i class="fa fa-play-circle"></i>
                    </div>
                    <div class="img-bar">
                        <a href="https://www.youtube.com/watch?v=${post.front.extra}" target="_blank"><i class="fa fa-expand"></i></a>${ censored ? '<i class="fa fa-exclamation-triangle"></i>' : '' }
                    </div>
                </div>`;
                break;
        }

        let front =
        `<div class="post-front">
            ${img}
        </div>`;

        if(post.only === 1)
        {
            post.htmlcontent = front;
        }

        return $(`
        <div class="post-container" id="${post.sid}" ${ post.iscomment ? `data-type="${post.extra}"` : '' } data-usid="${post.userid}" data-pid="${post.id}"  onmousedown="pid = ${post.id}">
            ${ post.only !== 1 ? `${ post.front.exists ? front : '' }` :  ''}
            <div class="post-status" data-type="${post.status}">${status}</div>
            <a href="${post.url}"><h3 class="post-title">${ post.iscomment ? post.sid : post.title }</h3></a>${ post.iscomment ? `&nbsp;<i class="fa fa-tag" onclick="addTag('${post.sid}');"></i>` : '' }
            <p class="p-responses">${responses}</p>
            <p class="p-comments">${comments}</p>
            <div class="post-content" id="p-content-${post.sid}">${post.htmlcontent}</div>
            ${ post.audio.exists > 0 ? `<div class="post-audio"><audio src="${post.audio.src}" controls>Tu navegador no soporta controles de audio</audio></div>` : '' }
            <div class="post-footer">
                <div class="post-speech-buttons">
                    <i id="play-${post.sid}" class="fa fa-play" onclick="playspeech(this)" data-sid="${post.sid}"></i>
                    <i id="pause-${post.sid}" class="fa fa-pause" onclick="speechSynthesis.pause()" style="display:none;"></i>
                    <i id="resume-${post.sid}" class="fa fa-play" onclick="speechSynthesis.resume()" style="display:none;"></i>
                    <i id="stop-${post.sid}" class="fa fa-stop" onclick="speechSynthesis.cancel()" style="display:none;"></i>
                </div>
                <div class="post-information">
                    <i class="fa fa-clock-o">&nbsp;${post.timediff}</i>
                    <i class="fa fa-user-circle-o">&nbsp;${post.usernickname}</i>
                </div>
            </div>
        </div>`);
    }

    function writePublicationAndComments(post,comments)
    {
        let pc = $('#pc');
        let cc = $('#cc');

        writePost(post).appendTo(pc);

        for(let i = 0;i < comments.length;i++)
        {
            let comment = comments[i];
            writePost(comment).appendTo(cc);
        }

        loadHighlightCode();
    }
    
    function loadHighlightCode()
    {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    }

    function loadBroadcast()
    {
        if(urlocation === 'board')
        {
            window.Echo.private('created.post').listen('PostCreated', (e) => {
                if(urstatus === 1)
                    renderPostsToIndex(e);
            });
        }else if(urlocation === 'search')
        {}
        else
        {
            window.Echo.private(`created.comment.${puid}`).listen('PostCreated', (e) => {
                writePost(e).prependTo(cc);
            });
        }
    }

    function main()
    {
        loadCategoryList();
        loadThemeList();
        loadContextMenu();
        loadBroadcast();

        if(urlocation === 'board')
        {
            getPostsForIndex();

            /* https://stackoverflow.com/questions/3962558/javascript-detect-scroll-end */
            $(window).scroll(function (e)
            {
                let body = document.body;
                let bodyw = parseFloat(body.clientHeight) + 60;

                if (body.scrollHeight - (this.pageYOffset || body.scrollTop) <= bodyw)
                    getPostsForIndex();
            });
        }
        else if(urlocation === 'search')
            renderPostsToIndex(publications);
        else
            writePublicationAndComments(publication,comments);
        
        if(urstatus === 1)
            information('Administrador');
        else if(urstatus === 3)
            information('Moderador');

        mylazyload();
    }

    // function addLastComment()
    // {
    //     let cc = $('#cc');
    //     $.ajax({
    //         url: '/api/getlastcomment',
    //         method: 'post'
    //     }).done(function(r,t,h){
    //         cc.prepend(writePost(r));
    //         mylazyload();
    //     });
    // }

    function isValidURL(str)
    {
        let pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return !!pattern.test(str);
    }

    function isImageUrl(url) {
        return(url.match(/\.(jpeg|jpg|gif|png|webp|bmp)$/) != null);
    }

    function isDirectImageUrl(str)
    {
        return isValidURL(str) && isImageUrl(str);
    }

    function downloadImage(url,timeoutT) {
        return new Promise(function(resolve, reject) {
            let timeout = timeoutT || 5000;
            let timer, img = new Image();
            let obj = {url: url,img: img,status: ''};
            img.onerror = img.onabort = function() {
                clearTimeout(timer);
                obj.status = 'error';
                reject(obj);
            };
            img.onload = function() {
                clearTimeout(timer);
                obj.status = 'success';
                resolve(obj);
            };
            timer = setTimeout(function() {
                obj.status = 'timeout';
                reject(obj);
            }, timeout); 
            img.src = url;
        });
    }

    function isYoutubeUrl(url)
    {
        return url.indexOf('youtu') !== (-1);
    }

    function isDaylimotionUrl(url)
    {
        return url.indexOf('daylimotion') !== (-1);
    }

    function isVideoUrl(url)
    {
        if(isYoutubeUrl(url))
            return 1;
        else if(isDaylimotionUrl(url))
            return 2;
        return false;
    }

    function ytbUrlExists(url)
    {
        let http = new XMLHttpRequest();
        http.open('GET',`https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=${url}&format=json`,true);
        http.send();
        
        if(XMLHttpRequest.response === 'Bad Request')
            return false;

        return true;
    }

    function downImgf(purl,result)
    {
        switch(result)
        {
            case 'success':
                //console.log('success');
                $('.form-post-image-preview img').attr('src',purl);
                fpip.show();
                break;
            case 'error':
                alert('No se pudo descargar la imagen.');
                break;
            case 'timeout':
                alert('No se pudo descargar la imagen (tiempo fuera).');

                break;
        }
    }

    function getV(url)
    {
        let r;
        
        if(r = url.match(/(?:https:\/\/)?www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)(?:.*)?/m))
            return r[1];
        else if(r = url.match(/(?:https?:\/\/)?[www\.]?(?:youtu|y2u)\.be\/([a-zA-Z0-9_-]+)(?:.*)?/m))
            return r[1];
        else
            return false;
    }

    function submit(e)
    {
        let img = document.getElementById('form-post-image-file');
        //let jqimg = $('#form-post-image-file');
        let audio = $('#record-audio');
        let title = $('#form-post .title');
        let category = $('#form-post select');
        let fc = false;

        if(e.target.id.toString() === 'form-comment')
            fc = true;

        let sbtn = $(`#form-${ fc ? 'comment' : 'post' } button[type="submit"]`);
        let pid = $(`#form-comment > input[type="hidden"]`);
        let tarea = $(`#form-${ fc ? 'comment' : 'post' } textarea`);
        let url = $('input[name="fronturl"]').val();

        e.preventDefault();

        if(!fc)
        {
            if(title.val() === '')
            {
                alert('Se requiere un título.');
                return;
            }

            if(title.val().length > 100)
            {
                alert('El título de la publicación no puede contener más de 100 carácteres.');
                return;
            }
        }
        else
        {
            if(parseInt(pid.val(),10) === 0)
            {
                alert('PostID Inválido.');
                return;
            }
        }

        if(url)
        {
            if(url.length > 0 && !isValidURL(url))
            {
                alert('URL de la imagen/video inválida.');
                return;
            }
            else if(url.length === 0)
            {
                alert('Se esperaba URL de la imagen o vídeo.');
                return;
            }
            else
            {
                if(!urlData.verified)
                {
                    error('La URL aún no fue verificada o es inválida.');
                    return;
                }
            }
        }

        if(tarea.val() === '' & img.files.length === 0 & audio.attr('src') === undefined & ((url || '') === ''))
        {
            alert('Se espera algún tipo de contenido.');
            return;
        }

        if(tarea.val() !== '' & !isASCII(tarea.val()))
        {
            alert('El contenido de la publicación no puede contener carácteres raros.');
            return;
        }

        if(req)
            req.abort();
        
        let fd = new FormData();

        if(!fc)
        {
            fd.append('title',title.val());
            fd.append('category',category.val());

            if(typeof grecaptcha !== 'undefined')
                fd.append('g-recaptcha-response',grecaptcha.getResponse());
        }
        else
        {
            if(parseInt(pid.val(),10) > 0)
                fd.append('postid',pid.val());
        }
        
        if(tarea.val() !== '')
            fd.append('content',tarea.val());

        if(audio.attr('src') !== undefined)
            fd.append('audiodata',rchunks[0]);
        
        if(iplus18.attr('value') == 1)
            fd.append('censorefront',true);
        
        if(iplusUrl.attr('value') == 1)
        {
            fd.append('fronturl',urlData.url);
            fd.append('fronttype',urlData.type);
        }
        else
            if(img.files.length > 0)
                fd.append('front',img.files[0]);
        
        sbtn.attr('disabled',true);
        sbtn.html(s);
        
        req = $.ajax({
            url: `/api/${ fc ? 'commentsubmit' : 'postsubmit' }`,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            progress: function(e)
                {
                    if(e.lengthComputable)
                        showProgressBar(e.loaded,e.total);
                }
        }).done(function(r)
        {
            sbtn.attr('disabled',false);
            sbtn.html(o);
            
            hideProgressBar();
            
            if(!r['success'])
            {
                alert(r['description']);
                return;
            }
            
            tarea.val('');
            audio.removeAttr('src');
            previewc.trigger('click');
            rrembtn.trigger('click');

            if(!fc)
            {
                window.open(r['url'],'_blank');
                title.val('');
            }

            success('¡Publicación enviada!');

            //addLastComment();
            //mylazyload();
        }).fail(function(r,t,h)
        {
            sbtn.attr('disabled',false);
            sbtn.html(o);
            
            hideProgressBar();
            
            if(r.status === 429)
                error('Espere un minuto para volver a publicar.');
            else
                error('Se produjo un error inesperado.');
        });
    }

    $('#form-comment').on('submit',submit);

    $('#form-post').on('submit',submit);
    
    const workerOptions = {encoderWorkerFactory: function ()
        {
            return new Worker('/public/js/encoderWorker.umd.js')
        },
        OggOpusEncoderWasmPath:  '/public/js/wasm/OggOpusEncoder.wasm',
        WebMOpusEncoderWasmPath: '/public/js/wasm/WebMOpusEncoder.wasm'
    };
    
    window.MediaRecorder = OpusMediaRecorder;

    let timerr = new Temporizer();

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

                if(timerr.stop() > 30000)
                {
                    error('No se pueden enviar audios de más de 30 segundos de duración.');
                    rchunks.clear();
                    rrembtn.trigger('click');
                    return;
                }

                let blob = new Blob(rchunks, { 'type' : 'audio/ogg' });
                let objUrl = URL.createObjectURL(blob);

                raudio.attr('src',objUrl);
                raudio.show();
            };
            
            recorder.onpause = function(){
                rrecbtn.html(c);
                timerr.pause();
            };
            
            recorder.onstart = function(){
                rchunks.clear();
                raudio.attr('src','');
                rrembtn.hide();
                raudio.hide();
                rrecbtn.html(p);
                timerr.start();
            };
            
             recorder.onresume = function(){
                rrecbtn.html(p);
                timerr.resume();
            };
            
            if(f) f();
        });
    }

    rrecbtn.on('click', function(){
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

    rrembtn.on('click',function(){
        raudio.attr('src','');
        rrembtn.hide();
        raudio.hide();
        rrecbtn.html(r);
        rchunks.clear();
        if(recorder)
            recorder.stream.getTracks().forEach(i => i.stop());
    });

    rstpbtn.on('click', function(){
        recorder.stop();
    })
    
    
    $('#button-publicate').on('click',function()
    {
        showModal('#modal-publication')
    });
    
    $('#form-post-image-file').on('change',function(e)
    {
        let files = e.currentTarget.files;
        let reader = new FileReader();
        let img = $(`#form-${urlocation === 'board' ? 'post' : 'comment'} img`);
    
        reader.onload = function(e)
        {
          img.attr("src", e.target.result);
        }
        
        reader.readAsDataURL(files[0]);
        
        fpip.show();
    });
    
    $(window).on('paste',function(e)
    {
        let fpif = document.getElementById('form-post-image-file');
        let tmpfiles = (e.clipboardData || e.originalEvent.clipboardData).files;
        if(tmpfiles.length > 0)
        {
            fpif.files = tmpfiles;
            $(fpif).trigger("change");
        }
    });
    
    function formRemoveImage(f)
    {
        let img = $(f + ' img');
        $(f + ' input[type="file"]').val('');
        img.attr('src','');
        img.parent().hide();
    }
    
    function formChangeImage(f,i)
    {
        $(f + ' img').attr('src',i);
    }
    
    document.onkeydown = function(e) {
        let modal = $('.modal');
        let code = (e.key | e.keyIdentifier | e.keyCode | e.which | e.charCode);

        if((code === 27) & modal.is(':visible'))
            modal.hide();
    };
    
    function showModal(id)
    {
        $(id).css('display','flex');
    }
    
    function hideModal(id)
    {
        $(id).css('display','none');
    }
    
    function showMessage(id,txt)
    {
        let container = $(id);
        $(id + ' div').text(txt);
        container.show();
        setTimeout(function(){
            container.hide();
        },5000)
    }
    
    window.showProgressBar = function(value = 0,max = 100)
    {
        let w = $('#progressbar');
        let pb = $('#progressbar progress');
        
        if(w.is(':visible'))
            progressBarUpdate(value);
        else
        {
            w.show();
            pb.attr('value',value);
            pb.attr('max',max);
        }
    }
    
    window.progressBarUpdate = function(value)
    {
        let pb = $('#progressbar progress');
        pb.attr('value',value);
    }
    
    window.hideProgressBar = function()
    {
        $('#progressbar').hide();
    };
    
    window.alert = function(txt)
    {
        // showMessage('#alert',txt);
        notifier.notify('warning',txt);
    };
    
    window.error = function(txt)
    {
        notifier.notify('error',txt);
        // showMessage('#error',txt);
    };
    
    window.information = function(txt)
    {
        // showMessage('#information',txt);
        notifier.notify('info',txt);
    };
    
    window.success = function(txt)
    {
        // showMessage('#success',txt);
        notifier.notify('success',txt);
    };

    main();
}