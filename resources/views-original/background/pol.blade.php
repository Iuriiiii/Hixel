
{{-- La variable $category se utiliza para saber en cuál tablón estamos en el momento --}}

@push('css')
    {{--
        En esta sección van los "link" a los .CSS
        También puede ir una un <style></style> sin problema.
    --}}
    <style>
body{background:#111;touch-action:none}#view{position:fixed;top:0;left:0;visibility:hidden;z-index:-1}
    </style>
@endpush

@push('script')
    {{--
        En esta sección van los "script" a los .JS
        También puede ir una un <script></script> sin problema.
    --}}
    <!-- https://codepen.io/osublake/details/RLOzxo -->
    <script>
"use strict";console.clear();var log=console.log.bind(console),TAU=2*Math.PI,Particle=function(){function t(t,i){this.texture=t,this.frame=i,this.alive=!1,this.width=i.width,this.height=i.height,this.originX=i.width/2,this.originY=i.height/2}return t.prototype.init=function(t,i){void 0===t&&(t=0),void 0===i&&(i=0);var e=random(TAU),r=random(2,6);return this.x=t,this.y=i,this.alpha=1,this.alive=!0,this.theta=e,this.vx=Math.sin(e)*r,this.vy=Math.cos(e)*r,this.rotation=Math.atan2(this.vy,this.vx),this.drag=random(.82,.97),this.scale=random(.1,1),this.wander=random(.5,1),this.matrix={a:1,b:0,c:0,d:1,tx:0,ty:0},this},t.prototype.update=function(){var t=this.matrix;this.x+=this.vx,this.y+=this.vy,this.vx*=this.drag,this.vy*=this.drag,this.theta+=random(-.5,.5)*this.wander,this.vx+=.1*Math.sin(this.theta),this.vy+=.1*Math.cos(this.theta),this.rotation=Math.atan2(this.vy,this.vx),this.alpha*=.98,this.scale*=.985,this.alive=.06<this.scale&&.06<this.alpha;var i=Math.cos(this.rotation)*this.scale,e=Math.sin(this.rotation)*this.scale;return t.a=i,t.b=e,t.c=-e,t.d=i,t.tx=this.x-(this.originX*t.a+this.originY*t.c),t.ty=this.y-(this.originX*t.b+this.originY*t.d),this},t.prototype.draw=function(t){var i=this.matrix,e=this.frame;return t.globalAlpha=this.alpha,t.setTransform(i.a,i.b,i.c,i.d,i.tx,i.ty),t.drawImage(this.texture,e.x,e.y,e.width,e.height,0,0,this.width,this.height),this},t}(),App=function(){function t(t){var o=this;this.pool=[],this.particles=[],this.pointer={x:-9999,y:-9999},this.buffer=document.createElement("canvas"),this.bufferContext=this.buffer.getContext("2d"),this.supportsFilters=void 0!==this.bufferContext.filter,this.pointerMove=function(t){t.preventDefault();var i=t.targetTouches?t.targetTouches[0]:t;o.pointer.x=i.clientX,o.pointer.y=i.clientY;for(var e=0;e<random(2,7);e++)o.spawn(o.pointer.x,o.pointer.y)},this.resize=function(t){o.width=o.buffer.width=o.view.width=window.innerWidth,o.height=o.buffer.height=o.view.height=window.innerHeight},this.render=function(t){var i=o.context,e=o.particles,r=o.bufferContext;i.fillStyle=o.backgroundColor,i.fillRect(0,0,o.width,o.height),r.globalAlpha=1,r.setTransform(1,0,0,1,0,0),r.clearRect(0,0,o.width,o.height),r.globalCompositeOperation=o.blendMode;for(var s=0;s<e.length;s++){(h=e[s]).alive?h.update():(o.pool.push(h),removeItems(e,s,1))}for(var h,n=0,a=e;n<a.length;n++){(h=a[n]).draw(r)}o.supportsFilters&&(o.useBlurFilter&&(i.filter="blur("+o.filterBlur+"px)"),i.drawImage(o.buffer,0,0),o.useContrastFilter&&(i.filter="drop-shadow(4px 4px 4px rgba(0,0,0,1)) contrast("+o.filterContrast+"%)")),i.drawImage(o.buffer,0,0),i.filter="none",requestAnimationFrame(o.render)},Object.assign(this,t),this.context=this.view.getContext("2d",{alpha:!1})}return t.prototype.spawn=function(t,i){var e=this.particles.length>this.maxParticles?this.particles.shift():this.pool.length?this.pool.pop():new Particle(this.texture,sample(this.frames));return e.init(t,i),this.particles.push(e),this},t.prototype.start=function(){return this.resize(),this.render(),this.view.style.visibility="visible",window.PointerEvent?window.addEventListener("pointermove",this.pointerMove):(window.addEventListener("mousemove",this.pointerMove),window.addEventListener("touchmove",this.pointerMove)),window.addEventListener("resize",this.resize),requestAnimationFrame(this.render),this},t}();function createFrames(t,i,e){for(var r=[],s=0;s<t;s++)r.push({x:i*s,y:0,width:i,height:e});return r}function removeItems(t,i,e){var r=t.length;if(!(r<=i||0===e)){for(var s=r-(e=r<i+e?r-i:e),h=i;h<s;++h)t[h]=t[h+e];t.length=s}}function random(t,i){var e;return null==i&&(i=t,t=0),i<t&&(e=t,t=i,i=e),t+(i-t)*Math.random()}function sample(t){return t[Math.random()*t.length|0]}var app=new App({view:document.querySelector("#view"),texture:document.querySelector("#star-texture"),frames:createFrames(5,80,80),maxParticles:2e3,backgroundColor:"#111111",blendMode:"lighter",filterBlur:50,filterContrast:300,useBlurFilter:!0,useContrastFilter:!0});window.addEventListener("load",app.start()),window.focus(),log("APP",app);
    </script>
@endpush

@section('background')
    {{--
        En esta sección van el código dentro del body, si se require
    --}}
<canvas id="view"></canvas>

<aside style="display:none">
  <img id="star-texture" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/106114/stars-02.png?v=1">
</aside>
@endsection