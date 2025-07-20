<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Under Construction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('/portal/uploads/logo3kecil.png') no-repeat center center fixed;
      background-size: cover;
      color: white;
      text-align: center;
    }

    .overlay {
      background: rgba(0, 0, 0, 0.6);
      height: 100%;
      width: 100%;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 1;
    }

    .content {
      position: relative;
      z-index: 2;
      top: 50%;
      transform: translateY(-50%);
    }

    .countdown-box {
      background: orange;
      color: black;
      padding: 20px;
      border-radius: 8px;
      margin: 10px;
      min-width: 80px;
    }

    .subscribe input {
      max-width: 300px;
      display: inline-block;
      margin-right: 5px;
    }

    .social-icons i {
      font-size: 1.5rem;
      margin: 0 10px;
      color: white;
    }

    .social-icons i:hover {
      color: orange;
    }

  </style>
</head>
<body>
  <div class="overlay"></div>
  <div class="content container">
    <h1 class="fw-bold mb-3">Our Website is Under Construction</h1>
    <p class="mb-4">We’re working hard to finish the development of this site. Stay tuned!</p>

    <div class="d-flex justify-content-center flex-wrap mb-4" id="countdown">
      <div class="countdown-box">
        <div id="days">00</div><small>DAYS</small>
      </div>
      <div class="countdown-box">
        <div id="hours">00</div><small>HOURS</small>
      </div>
      <div class="countdown-box">
        <div id="minutes">00</div><small>MINUTES</small>
      </div>
      <div class="countdown-box">
        <div id="seconds">00</div><small>SECONDS</small>
      </div>
    </div>

    <div class="subscribe mb-4">
      <input type="text" class="form-control d-inline-block" placeholder="Enter your name">
      <button class="btn btn-warning">SUBMIT</button>
    </div>

    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
  </div>

  <script>
    // Countdown logic
    var countDownDate = new Date("Aug 04, 2025 08:05:16").getTime();

    var x = setInterval(function () {
      var now = new Date().getTime();
      var distance = countDownDate - now;

      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      document.getElementById("days").innerText = days;
      document.getElementById("hours").innerText = hours;
      document.getElementById("minutes").innerText = minutes;
      document.getElementById("seconds").innerText = seconds;

      if (distance < 0) {
        clearInterval(x);
        document.getElementById("countdown").innerHTML = "<p class='text-warning fw-bold'>We're Live!</p>";
      }
    }, 1000);
  </script>
</body>
</html>
