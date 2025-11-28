const canvas = document.getElementById('confettiCanvas');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

const confettiCount = 150;
const confetti = [];

function random(min, max){
    return Math.random() * (max - min) + min;
}

class Particle{
    constructor(){
        this.x = random(0, canvas.width);
        this.y = random(-canvas.height, 0);
        this.radius = random(2,5);
        this.color = `hsl(${random(0, 360)}, 100%, 50%)`;
        this.speed = random(1, 3);
        this.angle = random(0, 2 * Math.PI);
        this.tilt = random(-10,10);
    }
    update(){
        this.y += this.speed;
        this.tilt += 0.1;
        if(this.y > canvas.height) {
            this.y = random(-20,0);
            this.x = random(0,canvas.width);
        }
    }
    draw(){
        ctx.beginPath();
        ctx.moveTo(this.x + this.tilt, this.y);
        ctx.lineTo(this.x + this.tilt + this.radius, this.y + this.radius*2);
        ctx.strokeStyle = this.color;
        ctx.lineWidth = this.radius;
        ctx.stroke();
    }
}

for(let i=0; i<confettiCount; i++){
    confetti.push(new Particle());
}

function animate(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    confetti.forEach(p => {
        p.update();
        p.draw();
    });
    requestAnimationFrame(animate);
}

animate();
window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});
