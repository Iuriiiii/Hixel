
{{-- La variable $category se utiliza para saber en cuál tablón estamos en el momento --}}

@push('css')
    {{--
        En esta sección van los "link" a los .CSS
        También puede ir una un <style></style> sin problema.
    --}}
    <style>
body {
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAI0lEQVQIW2NkwAT/GdHE/gP5jMiCYAGQIpggXAAmiCIAEgQAAE4FBbECyZcAAAAASUVORK5CYII=) #222;
}</style>
@endpush

@push('script')
    {{--
        En esta sección van los "script" a los .JS
        También puede ir una un <script></script> sin problema.
    --}}
    <script>
    
function Particles(){
  //particle colors
  this.colors = [
    '255, 255, 255',
    '255, 99, 71',
    '19, 19, 19'
  ]
  //adds gradient to particles on true
  this.blurry = true;
  //adds white border
  this.border = false;
  //particle radius min/max
  this.minRadius = 10; 
  this.maxRadius = 35;
  //particle opacity min/max
  this.minOpacity = .005;
  this.maxOpacity = .5;
  //particle speed min/max
  this.minSpeed = .05;
  this.maxSpeed = .5;
  //frames per second 
  this.fps = 60;
  //number of particles
  this.numParticles = 75;
  //required canvas variables
  this.canvas = document.getElementById('canvas');
  this.ctx = this.canvas.getContext('2d');
}

Particles.prototype.init = function(){this.render();this.createCircle();}
Particles.prototype._rand = function(min, max){return Math.random() * (max - min) + min;}
Particles.prototype.render = function(){var self = this,wHeight = $(window).height(),wWidth = $(window).width();self.canvas.width = wWidth;self.canvas.height = wHeight;$(window).on('resize', self.render);}
Particles.prototype.createCircle = function(){var particle = [];for (var i = 0; i < this.numParticles; i++) {var self = this,color = self.colors[~~(self._rand(0, self.colors.length))];particle[i] = {radius: self._rand(self.minRadius, self.maxRadius),xPos: self._rand(0, canvas.width),yPos: self._rand(0, canvas.height),xVelocity: self._rand(self.minSpeed, self.maxSpeed),yVelocity: self._rand(self.minSpeed, self.maxSpeed),color: 'rgba(' + color + ',' + self._rand(self.minOpacity, self.maxOpacity) + ')'};self.draw(particle, i);}self.animate(particle);}
Particles.prototype.draw = function(particle, i){var self = this,ctx = self.ctx;if (self.blurry === true ) {var grd = ctx.createRadialGradient(particle[i].xPos, particle[i].yPos, particle[i].radius, particle[i].xPos, particle[i].yPos, particle[i].radius/1.25);grd.addColorStop(1.000, particle[i].color);grd.addColorStop(0.000, 'rgba(34, 34, 34, 0)');ctx.fillStyle = grd;}else{ctx.fillStyle = particle[i].color;}if (self.border === true) {ctx.strokeStyle = '#fff';ctx.stroke();}ctx.beginPath();ctx.arc(particle[i].xPos, particle[i].yPos, particle[i].radius, 0, 2 * Math.PI, false);ctx.fill();}
Particles.prototype.animate = function(particle){var self = this,ctx = self.ctx;setInterval(function(){self.clearCanvas();for (var i = 0; i < self.numParticles; i++) {particle[i].xPos += particle[i].xVelocity;particle[i].yPos -= particle[i].yVelocity;if (particle[i].xPos > self.canvas.width + particle[i].radius || particle[i].yPos > self.canvas.height + particle[i].radius) {self.resetParticle(particle, i);}else{self.draw(particle, i);}}}, 1000/self.fps); }
Particles.prototype.resetParticle = function(particle, i){var self = this;var random = self._rand(0, 1);if (random > .5){particle[i].xPos = -particle[i].radius;particle[i].yPos = self._rand(0, canvas.height);}else{particle[i].xPos = self._rand(0, canvas.width);particle[i].yPos = canvas.height + particle[i].radius;}self.draw(particle, i);}
Particles.prototype.clearCanvas = function(){this.ctx.clearRect(0, 0, canvas.width, canvas.height);}   

var particle = new Particles().init();</script>
@endpush

@section('background')
    {{--
        En esta sección van el código dentro del body, si se require
    --}}
<canvas id="canvas"></canvas>
@endsection