
<?php
  if (isset($_POST["submit"])) {
    $username = htmlentities(strip_tags(trim($_POST["username"])));
    $password = htmlentities(strip_tags(trim($_POST["password"])));

    $error_message = "";

    if (empty($username)) {
      $error_message .= "- Username belum diisi <br>";
    }

    if (empty($password)) {
      $error_message .= "- Password belum diisi <br>";
    }

    if ($error_message === "") {
      include("connection.php");

      $username = mysqli_real_escape_string($connection, $username);
      $password = mysqli_real_escape_string($connection, $password);

      $password_sha1 = sha1($password);

      $query = "
        SELECT * FROM admin
        WHERE username = '$username'
        AND password = '$password_sha1'
      ";

      $result = mysqli_query($connection, $query);

      if (mysqli_num_rows($result) == 0) {
        $error_message .= "- Username dan/atau Password tidak sesuai";
      }

      mysqli_free_result($result);
      mysqli_close($connection);

      if ($error_message === "") {
        session_start();
        $_SESSION["username"] = $username;
        header("Location: student_view.php");
        exit;
      }
    }
  } else {
    $error_message = "";
    $username = "";
    $password = "";
  }
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Data Mahasiswa</title>

  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Background */
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #667eea, #764ba2);
      padding: 20px;
    }

    /* Container */
    .container {
      width: 100%;
      max-width: 420px;
      background: white;
      padding: 40px;
      border-radius: 18px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    /* Heading */
    h1 {
      text-align: center;
      color: #252b42;
      font-size: 28px;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .subtitle {
      text-align: center;
      color: #999;
      font-size: 13px;
      margin-bottom: 30px;
    }

    /* Legend */
    legend {
      font-size: 18px;
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
    }

    fieldset {
      border: none;
    }

    /* Form */
    form p {
      margin-bottom: 20px;
    }

    label {
      display: block;
      font-size: 14px;
      color: #444;
      margin-bottom: 8px;
      font-weight: 600;
    }

    /* Input */
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 13px 15px;
      border: 1px solid #ddd;
      border-radius: 10px;
      font-size: 14px;
      outline: none;
      transition: 0.3s;
      background: #fafafa;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #667eea;
      background: white;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.12);
    }

    /* Button */
    input[type="submit"] {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    input[type="submit"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 7px 18px rgba(102, 126, 234, 0.3);
    }

    input[type="submit"]:active {
      transform: scale(0.98);
    }

    /* Error */
    .error {
      background: #fff0f0;
      color: #dc3545;
      border: 1px solid #ffcaca;
      padding: 12px 15px;
      border-radius: 9px;
      font-size: 13px;
      margin-bottom: 20px;
      line-height: 1.6;
    }

    /* Footer */
    .footer {
      text-align: center;
      margin-top: 25px;
      color: #aaa;
      font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 480px) {
      .container {
        padding: 30px 22px;
      }

      h1 {
        font-size: 24px;
      }
    }
  </style>
</head>

<body>

  <div class="container">

    <h1>Data Mahasiswa</h1>
    <p class="subtitle">Silakan login untuk melanjutkan</p>

    <?php
      if ($error_message !== "") {
        echo "<div class='error'>$error_message</div>";
      }
    ?>

    <form action="login.php" method="post">

      <fieldset>
        <legend>Login</legend>

        <p>
          <label for="username">Username</label>
          <input
            type="text"
            name="username"
            id="username"
            placeholder="Masukkan username"
            value="<?php echo htmlspecialchars($username); ?>"
          >
        </p>

        <p>
          <label for="password">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Masukkan password"
          >
        </p>

        <p>
          <input type="submit" name="submit" value="Log In">
        </p>

      </fieldset>

    </form>

    <div class="footer">
      &copy; 2026 Data Mahasiswa. All Rights Reserved.
    </div>

  </div>

</body>
</html>
