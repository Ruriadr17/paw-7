<?php
  session_start();

  if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
  }

  include("connection.php");

  if (isset($_POST["submit"])) {
    $nim = htmlentities(strip_tags(trim($_POST["nim"])));
    $name = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year = htmlentities(strip_tags(trim($_POST["birth_year"])));

    $error_message = "";

    if (empty($nim)) {
      $error_message .= "- NIM belum diisi <br>";
    } else if (!preg_match("/^[0-9]{8}$/", $nim)) {
      $error_message .= "- NIM harus berupa 8 digit angka <br>";
    }

    $nim = mysqli_real_escape_string($connection, $nim);
    $query = "SELECT * FROM student WHERE nim='$nim'";
    $query_result = mysqli_query($connection, $query);

    $data_amount = mysqli_num_rows($query_result);

    if ($data_amount >= 1) {
      $error_message .= "- NIM yang sama sudah digunakan <br>";
    }

    mysqli_free_result($query_result);

    if (empty($name)) {
      $error_message .= "- Nama belum diisi <br>";
    }

    if (empty($birth_city)) {
      $error_message .= "- Tempat lahir belum diisi <br>";
    }

    if (empty($department)) {
      $error_message .= "- Jurusan belum diisi <br>";
    }

    $select_ftib = "";
    $select_fteic = "";

    switch ($faculty) {
      case 'FTIB':
        $select_ftib = "selected";
        break;
      case 'FTEIC':
        $select_fteic = "selected";
        break;
    }

    if (!is_numeric($gpa) || $gpa <= 0 || $gpa > 4) {
      $error_message .= "- IPK harus diisi dengan angka 0 sampai 4 <br>";
    }

    if ($error_message === "") {
      $name = mysqli_real_escape_string($connection, $name);
      $birth_city = mysqli_real_escape_string($connection, $birth_city);
      $faculty = mysqli_real_escape_string($connection, $faculty);
      $department = mysqli_real_escape_string($connection, $department);
      $birth_date = mysqli_real_escape_string($connection, $birth_date);
      $birth_month = mysqli_real_escape_string($connection, $birth_month);
      $birth_year = mysqli_real_escape_string($connection, $birth_year);
      $gpa = (float) $gpa;

      $birth_date_full = $birth_year . "-" . $birth_month . "-" . $birth_date;

      $query = "INSERT INTO student VALUES ";
      $query .= "('$nim', '$name', '$birth_city', ";
      $query .= "'$birth_date_full','$faculty','$department',$gpa)";

      $result = mysqli_query($connection, $query);

      if ($result) {
        $message = "Mahasiswa dengan nama = \"<b>$name</b>\" sudah berhasil di tambah";
        $message = urlencode($message);
        header("Location: student_view.php?message={$message}");
        exit;
      } else {
        die("Query gagal dijalankan: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
      }
    }
  } else {
    $error_message = "";
    $nim = "";
    $name = "";
    $birth_city = "";
    $select_ftib = "selected";
    $select_fteic = "";
    $department = "";
    $gpa = "";
    $birth_date = 1;
    $birth_month = "1";
    $birth_year = 1996;
    $faculty = "FTIB";
  }

  $arr_month = [
    "1" => "Januari",
    "2" => "Februari",
    "3" => "Maret",
    "4" => "April",
    "5" => "Mei",
    "6" => "Juni",
    "7" => "Juli",
    "8" => "Agustus",
    "9" => "September",
    "10" => "Oktober",
    "11" => "November",
    "12" => "Desember"
  ];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Mahasiswa - Data Mahasiswa</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea, #764ba2);
      padding: 40px 20px;
    }

    .container {
      width: 100%;
      max-width: 850px;
      margin: auto;
      background: white;
      padding: 35px 40px;
      border-radius: 18px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    #header {
      text-align: center;
      margin-bottom: 25px;
    }

    #logo {
      color: #252b42;
      font-size: 28px;
      font-weight: 700;
    }

    nav {
      margin: 20px 0 30px;
      border-bottom: 1px solid #eee;
      padding-bottom: 20px;
    }

    nav ul {
      display: flex;
      justify-content: center;
      gap: 12px;
      list-style: none;
    }

    nav a {
      display: inline-block;
      text-decoration: none;
      color: #667eea;
      font-size: 14px;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 8px;
      transition: 0.3s;
    }

    nav a:hover {
      background: #667eea;
      color: white;
    }

    h2 {
      color: #252b42;
      font-size: 23px;
      margin-bottom: 25px;
    }

    fieldset {
      border: none;
    }

    legend {
      font-size: 18px;
      font-weight: 600;
      color: #333;
      margin-bottom: 22px;
    }

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

    input[type="text"],
    select {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #ddd;
      border-radius: 9px;
      font-size: 14px;
      outline: none;
      background: #fafafa;
      transition: 0.3s;
      color: #333;
    }

    input[type="text"]:focus,
    select:focus {
      border-color: #667eea;
      background: white;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.12);
    }

    #birth_date,
    select[name="birth_month"],
    select[name="birth_year"] {
      width: auto;
      min-width: 100px;
      margin-right: 8px;
    }

    .hint {
      display: block;
      color: #999;
      font-size: 12px;
      margin-top: 7px;
    }

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

    .error {
      background: #fff0f0;
      color: #dc3545;
      border: 1px solid #ffcaca;
      padding: 14px 16px;
      border-radius: 9px;
      font-size: 13px;
      margin-bottom: 22px;
      line-height: 1.7;
    }

    @media (max-width: 600px) {
      body {
        padding: 20px 12px;
      }

      .container {
        padding: 28px 20px;
      }

      #logo {
        font-size: 24px;
      }

      nav ul {
        gap: 3px;
      }

      nav a {
        padding: 9px 12px;
        font-size: 13px;
      }

      #birth_date,
      select[name="birth_month"],
      select[name="birth_year"] {
        min-width: 0;
        margin-right: 3px;
      }
    }
  </style>
</head>

<body>
  <div class="container">

    <div id="header">
      <h1 id="logo">Data Mahasiswa</h1>
    </div>

    <nav>
      <ul>
        <li><a href="student_view.php">Tampil</a></li>
        <li><a href="student_add.php">Tambah</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>

    <h2>Tambah Data Mahasiswa</h2>

    <?php
      if ($error_message !== "") {
        echo "<div class='error'>$error_message</div>";
      }
    ?>

    <form id="form_mahasiswa" action="student_add.php" method="post">

      <fieldset>
        <legend>Mahasiswa Baru</legend>

        <p>
          <label for="nim">NIM</label>
          <input
            type="text"
            name="nim"
            id="nim"
            value="<?php echo htmlspecialchars($nim); ?>"
            placeholder="Contoh: 12345678"
          >
          <span class="hint">NIM harus terdiri dari 8 digit angka.</span>
        </p>

        <p>
          <label for="name">Nama</label>
          <input
            type="text"
            name="name"
            id="name"
            value="<?php echo htmlspecialchars($name); ?>"
            placeholder="Masukkan nama mahasiswa"
          >
        </p>

        <p>
          <label for="birth_city">Tempat Lahir</label>
          <input
            type="text"
            name="birth_city"
            id="birth_city"
            value="<?php echo htmlspecialchars($birth_city); ?>"
            placeholder="Masukkan tempat lahir"
          >
        </p>

        <p>
          <label for="birth_date">Tanggal Lahir</label>

          <select name="birth_date" id="birth_date">
            <?php
              for ($i = 1; $i <= 31; $i++) {
                $selected = ($i == $birth_date) ? "selected" : "";
                echo "<option value='$i' $selected>";
                echo str_pad($i, 2, "0", STR_PAD_LEFT);
                echo "</option>";
              }
            ?>
          </select>

          <select name="birth_month">
            <?php
              foreach ($arr_month as $key => $value) {
                $selected = ($key == $birth_month) ? "selected" : "";
                echo "<option value='$key' $selected>$value</option>";
              }
            ?>
          </select>

          <select name="birth_year">
            <?php
              for ($i = 1990; $i <= 2005; $i++) {
                $selected = ($i == $birth_year) ? "selected" : "";
                echo "<option value='$i' $selected>$i</option>";
              }
            ?>
          </select>
        </p>

        <p>
          <label for="faculty">Fakultas</label>
          <select name="faculty" id="faculty">
            <option value="FTIB" <?php echo $select_ftib; ?>>FTIB</option>
            <option value="FTEIC" <?php echo $select_fteic; ?>>FTEIC</option>
          </select>
        </p>

        <p>
          <label for="department">Jurusan</label>
          <input
            type="text"
            name="department"
            id="department"
            value="<?php echo htmlspecialchars($department); ?>"
            placeholder="Masukkan jurusan"
          >
        </p>

        <p>
          <label for="gpa">IPK</label>
          <input
            type="text"
            name="gpa"
            id="gpa"
            value="<?php echo htmlspecialchars($gpa); ?>"
            placeholder="Contoh: 2.75"
          >
          <span class="hint">Gunakan tanda titik (.) untuk angka desimal.</span>
        </p>

      </fieldset>

      <p>
        <input type="submit" name="submit" value="Tambah Data">
      </p>

    </form>
  </div>
</body>
</html>

<?php
  mysqli_close($connection);
?>