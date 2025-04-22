<link rel="stylesheet" href="main.css"; ?>
<link href="bootstrap-5.3.3-dist/css/bootstrap.css" rel="stylesheet">
<script src="bootstrap-5.3.3-dist/js/bootstrap.js"></script>


<div class="row justify-content-center mt-7">
  <div class="col-lg-5 text-center">
    <a href="index.html">
      <img src="assets/img/svg/logo.svg" alt="">
    </a>
    <div class="card mt-5">
      <div class="card-body py-5 px-lg-5">
        <div class="svg-icon svg-icon-xl text-purple">
          <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 512 512"><title>ionicons-v5-g</title><path d="M336,208V113a80,80,0,0,0-160,0v95" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"></path><rect x="96" y="208" width="320" height="272" rx="48" ry="48" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"></rect></svg>
        </div>
        <h3 class="fw-normal text-dark mt-4">
          2-step verification
        </h3>
        <p class="mt-4 mb-1">
          We sent a verification code to your phone number.
        </p>
        <p>
          Please enter the code in the field below.
        </p>

        <div class="row mt-4 pt-2">
          <!-- Create 6 input fields -->
          <div class="col">
          <form method ="POST">
            <input type="text" class="form-control form-control-lg text-center py-4" id ="2fcode1" maxlength="1" required autofocus="" oninput="moveFocus(this, 1)">
          </div>
          <div class="col">
          <form method ="POST">
            <input type="text" class="form-control form-control-lg text-center py-4" id ="2fcode2" maxlength="1" required oninput="moveFocus(this, 2)">
          </div>
          <div class="col">
          <form method ="POST">
            <input type="text" class="form-control form-control-lg text-center py-4" id ="2fcode3" maxlength="1" required oninput="moveFocus(this, 3)">
          </div>
          <div class="col">
          <form method ="POST">
            <input type="text" class="form-control form-control-lg text-center py-4" id ="2fcode4" maxlength="1" required oninput="moveFocus(this, 4)">
          </div>
          <div class="col">
          <form method ="POST">
            <input type="text" class="form-control form-control-lg text-center py-4" id ="2fcode5" maxlength="1" reqired oninput="moveFocus(this, 5)">
          </div>
          <div class="col">
          <form method ="POST">
            <input type="text" class="form-control form-control-lg text-center py-4" id ="2fcode6" maxlength="1" required oninput="moveFocus(this, 6)">
          </div>
        </div>
        <input type="submit" value="Verify my account" class="button">
        </a>
      </div>
    </div>

  </div>
</div>

<script>
  // JavaScript to automatically focus on the next input field
  function moveFocus(currentField, nextFieldNumber) {
    if (currentField.value.length == currentField.maxLength) {
      document.getElementsByClassName('form-control')[nextFieldNumber]?.focus();
    }
  }
</script>

<?php

$codeArray = array($_POST["2fcode1"],$_POST["2fcode2"],$_POST["2fcode3"],$_POST["2fcode4"],$_POST["2fcode5"],$_POST["2fcode6"]);
$code = implode("", $codeArray);

?>