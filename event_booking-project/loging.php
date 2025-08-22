<?php 
session_start();
include "connect.php";

// Redirect if already logged in
if (isset($_SESSION["user_id"])) {
    // Check if a redirect parameter exists
    if (isset($_GET['redirect'])) {
        header("Location: " . $_GET['redirect']);
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM user_data WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            // Redirect to the intended page after login
            if (isset($_GET['redirect'])) {
                header("Location: " . $_GET['redirect']);
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background-color: #f0f8ff; }
    .login-container {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      background: white;
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
  </style>
</head>
<body>

<div class="login-container">
  <h3 class="text-center text-primary mb-4">User Login</h3>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <div class="mb-3">
      <label for="email" class="form-label text-primary">Email address</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required />
    </div>
    <div class="mb-3">
      <label for="password" class="form-label text-primary">Password</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required />
    </div>
    <div class="d-grid mb-2">
      <button type="submit" name="submit" class="btn btn-blue">Login</button>
    </div>
    <div class="d-grid">
      <a href="register.php" class="btn btn-outline-primary">New Register</a>
    </div>
  </form>
</div>

</body>
</html>
