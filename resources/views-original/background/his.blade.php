
{{-- La variable $category se utiliza para saber en cuál tablón estamos en el momento --}}

@push('css')
    {{--
        En esta sección van los "link" a los .CSS
        También puede ir una un <style></style> sin problema.
    --}}
    <!-- https://codepen.io/jackrugile/details/AokpF -->
    <style>
html,body{height:100%}body{background:#111}.scene{background:#000;box-shadow:0 0 0 10px #222,0 30px 30px -20px #000;height:80vmin;left:calc(50% - 200px);top:calc(50% - 200px);max-height:400px;max-width:400px;margin:auto;position:fixed;width:80vmin}canvas{height:100%;left:0;position:absolute;top:0;width:100%}
    </style>
@endpush

@push('script')
    {{--
        En esta sección van los "script" a los .JS
        También puede ir una un <script></script> sin problema.
    --}}
    <script>
var c=document.createElement("canvas"),ctx=c.getContext("2d"),dpr=window.devicePixelRatio,w=400,h=400,particles=[],particleCount=1e3,particlePath=4,pillars=[],pillarCount=60,hue=0,hueRange=60,hueChange=1,gravity=.1,lineWidth=1.1,lineCap="butt",PI=Math.PI,TWO_PI=2*PI;function rand(t,i){return Math.random()*(i-t)+t}function distance(t,i){var e=t.x-i.x,r=t.y-i.y;return Math.sqrt(e*e+r*r)}function Particle(t){this.path=[],this.reset()}function Pillar(){this.reset()}function init(){ctx.lineWidth=lineWidth,ctx.lineCap=lineCap;for(var t=pillarCount;t--;)pillars.push(new Pillar);document.querySelector(".scene").appendChild(c),loop()}function step(){hue+=hueChange,particles.length<particleCount&&particles.push(new Particle);for(var t=particles.length;t--;)particles[t].step();for(t=pillarCount;t--;)pillars[t].step()}function draw(){ctx.fillStyle="hsla(0, 0%, 0%, 0.3)",ctx.fillRect(0,0,w,h),ctx.globalCompositeOperation="lighter";for(var t=particles.length;t--;)particles[t].draw();for(ctx.globalCompositeOperation="source-over",t=pillarCount,ctx.fillStyle="rgba(20, 20, 20, 0.3)";t--;)pillars[t].draw()}function loop(){requestAnimationFrame(loop),step(),draw()}c.width=w*dpr,c.height=h*dpr,ctx.scale(dpr,dpr),Particle.prototype.reset=function(){this.radius=1,this.x=rand(0,w),this.y=0,this.vx=0,this.vy=0,this.hit=0,this.path.length=0},Particle.prototype.step=function(){this.hit=0,this.path.unshift([this.x,this.y]),this.path.length>particlePath&&this.path.pop(),this.vy+=gravity,this.x+=this.vx,this.y+=this.vy,this.y>h+10&&this.reset();for(var t=pillarCount;t--;){var i=pillars[t];distance(this,i)<this.radius+i.renderRadius&&(this.vx=-(i.x-this.x)*rand(.01,.03),this.vy=-(i.y-this.y)*rand(.01,.03),i.radius-=.1,this.hit=1)}},Particle.prototype.draw=function(){ctx.beginPath(),ctx.moveTo(this.x,~~this.y);for(var t=0,i=this.path.length;t<i;t++){var e=this.path[t];ctx.lineTo(e[0],~~e[1])}ctx.strokeStyle="hsla("+rand(hue+this.x/3,hue+this.x/3+hueRange)+", 50%, 30%, 0.6)",ctx.stroke(),this.hit&&(ctx.beginPath(),ctx.arc(this.x,this.y,rand(1,25),0,TWO_PI),ctx.fillStyle="hsla("+rand(hue+this.x/3,hue+this.x/3+hueRange)+", 80%, 15%, 0.1)",ctx.fill())},Pillar.prototype.reset=function(){this.radius=rand(50,100),this.renderRadius=0,this.x=rand(0,w),this.y=rand(h/2-h/4,h),this.active=0},Pillar.prototype.step=function(){this.active?this.radius<=1?this.reset():this.renderRadius=this.radius:this.renderRadius<this.radius?this.renderRadius+=.5:this.active=1},Pillar.prototype.draw=function(){ctx.beginPath(),ctx.arc(this.x,this.y,this.renderRadius,0,TWO_PI,!1),ctx.fill()},init();
    </script>
@endpush

@section('background')
    {{--
        En esta sección van el código dentro del body, si se require
    --}}
    <div class="scene"></div>
@endsection