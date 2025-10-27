const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

canvas.width = 400;
canvas.height = 600;

const paddle = {
  x: canvas.width / 2 - 50,
  y: canvas.height - 30,
  width: 100,
  height: 20,
  speed: 8
};

const ball = {
  x: canvas.width / 2,
  y: canvas.height / 2,
  size: 40,
  dx: 4,
  dy: 4,
  img: new Image()
};

ball.img.src = "Screenshot_20251027_130208_WhatsApp.jpg"; // ← foto kamu di folder yang sama

let rightPressed = false;
let leftPressed = false;

document.addEventListener("keydown", (e) => {
  if (e.key === "ArrowRight") rightPressed = true;
  if (e.key === "ArrowLeft") leftPressed = true;
});

document.addEventListener("keyup", (e) => {
  if (e.key === "ArrowRight") rightPressed = false;
  if (e.key === "ArrowLeft") leftPressed = false;
});

function drawPaddle() {
  ctx.fillStyle = "#fff";
  ctx.fillRect(paddle.x, paddle.y, paddle.width, paddle.height);
}

function drawBall() {
  ctx.drawImage(ball.img, ball.x - ball.size / 2, ball.y - ball.size / 2, ball.size, ball.size);
}

function update() {
  // Gerak paddle
  if (rightPressed && paddle.x + paddle.width < canvas.width) paddle.x += paddle.speed;
  if (leftPressed && paddle.x > 0) paddle.x -= paddle.speed;

  // Gerak bola
  ball.x += ball.dx;
  ball.y += ball.dy;

  // Pantulan sisi kiri dan kanan
  if (ball.x - ball.size / 2 < 0 || ball.x + ball.size / 2 > canvas.width) {
    ball.dx *= -1;
  }

  // Pantulan atas
  if (ball.y - ball.size / 2 < 0) {
    ball.dy *= -1;
  }

  // Pantulan bawah (kena paddle)
  if (
    ball.x > paddle.x &&
    ball.x < paddle.x + paddle.width &&
    ball.y + ball.size / 2 > paddle.y
  ) {
    ball.dy *= -1;
  }

  // Game over (bola jatuh)
  if (ball.y - ball.size / 2 > canvas.height) {
    ball.x = canvas.width / 2;
    ball.y = canvas.height / 2;
    ball.dy = 4;
  }

  // Gambar ulang
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawPaddle();
  drawBall();

  requestAnimationFrame(update);
}

ball.img.onload = update;
