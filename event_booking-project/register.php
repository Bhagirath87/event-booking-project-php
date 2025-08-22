<?php
include "connect.php";

if (isset($_POST['submit'])) {
    // Escape user input to prevent SQL injection
    $name = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $number = mysqli_real_escape_string($con, $_POST['number']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $city = mysqli_real_escape_string($con, $_POST['city']);

    // Validate mobile number format (exactly 10 digits)
    if (!preg_match('/^\d{10}$/', $number)) {
        echo "<script>alert('Mobile number must be exactly 10 digits.');</script>";
        exit();
    }

    // Server-side password match check
    if ($pass !== $confirm_pass) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Check for existing email or number
        $check = "SELECT * FROM user_data WHERE email = '$email' OR number = '$number'";
        $c_res = mysqli_query($con, $check);

        if (mysqli_num_rows($c_res) > 0) {
            $row = mysqli_fetch_assoc($c_res);
            if ($row['email'] == $email) {
                echo "<script>alert('Email is already registered!');</script>";
            } else if ($row['number'] == $number) {
                echo "<script>alert('Number is already registered!');</script>";
            }
        } else {
            // Hash the password before saving
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $isql = "INSERT INTO user_data (name, email, number, password, city_id) 
                     VALUES ('$name', '$email', '$number', '$hashed_pass', '$city')";
            $in = mysqli_query($con, $isql);

            if ($in) {
                header("Location: loging.php");
                exit(); // prevent further execution
            } else {
                echo "Error: " . mysqli_error($con);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f8ff;
    }
    .register-container {
      max-width: 500px;
      margin: 2rem auto;
      padding: 30px;
      background-color: white;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,255,0.1);
    }
    .btn-blue {
      background-color: #007BFF;
      color: white;
    }
    .btn-blue:hover {
      background-color: #0056b3;
    }
    .text-danger, .text-success {
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
<div class="register-container">
  <h3 class="text-center text-primary mb-4">New User Registration</h3>
  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label text-primary">Full Name</label>
      <input type="text" class="form-control" name="fullname" required>
    </div>
    <div class="mb-3">
      <label class="form-label text-primary">Email</label>
      <input type="email" class="form-control" name="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label text-primary">Mobile Number</label>
      <input type="tel" class="form-control" name="number" pattern="\d{10}" maxlength="10" title="Please enter a 10-digit mobile number" required>
    </div>
    <div class="mb-3">
      <label class="form-label text-primary">Password</label>
      <input type="password" class="form-control" name="password" id="password" required>
    </div>
    <div class="mb-3">
      <label class="form-label text-primary">Confirm Password</label>
      <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
      <small id="matchMessage" class="text-danger"></small>
    </div>
    <div class="mb-3">
      <label class="form-label text-primary">City</label>
      <select name="city" class="form-control" id="city">
        <?php
        $sql = "SELECT * FROM city";
        $res = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
          echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['city_name']) . "</option>";
        }
        ?>
      </select>
    </div>
    <div class="d-grid">
      <button type="submit" name="submit" class="btn btn-blue">Register</button>
    </div>
    <div class="d-grid mt-2">
      <a href="loging.php" class="btn btn-outline-primary">Login</a>
    </div>
  </form>
</div>

<script>
  const password = document.querySelector("#password");
  const confirm = document.querySelector("#confirm_password");
  const message = document.querySelector("#matchMessage");

  confirm.addEventListener("keyup", () => {
    if (confirm.value === password.value) {
      message.textContent = "✅ Passwords match";
      message.classList.remove("text-danger");
      message.classList.add("text-success");
    } else {
      message.textContent = "❌ Passwords do not match";
      message.classList.remove("text-success");
      message.classList.add("text-danger");
    }
  });

  // Optional: Prevent submission if JS catches mismatch
  document.querySelector("form").addEventListener("submit", (e) => {
    if (password.value !== confirm.value) {
      e.preventDefault();
      alert("Please ensure passwords match before submitting.");
    }
  });
</script>
</body>
</html>
