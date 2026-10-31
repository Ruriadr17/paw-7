<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

include("connection.php");

$error_message = "";

// Mengambil NIM dari URL
$nim = $_GET["nim"] ?? "";

// Jika tombol Simpan Perubahan ditekan
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nim = trim($_POST["nim"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $birth_city = trim($_POST["birth_city"] ?? "");
    $birth_date = $_POST["birth_date"] ?? "";
    $faculty = $_POST["faculty"] ?? "";
    $department = trim($_POST["department"] ?? "");
    $gpa = $_POST["gpa"] ?? "";

    // Validasi data
    if (
        $nim === "" ||
        $name === "" ||
        $birth_city === "" ||
        $birth_date === "" ||
        $faculty === "" ||
        $department === "" ||
        !is_numeric($gpa) ||
        $gpa < 0 ||
        $gpa > 4
    ) {
        $error_message = "Semua data harus diisi dan IPK harus 0 sampai 4.";

    } else {

        // Query untuk mengubah data mahasiswa
        $query = "UPDATE student
                  SET name = ?,
                      birth_city = ?,
                      birth_date = ?,
                      faculty = ?,
                      department = ?,
                      gpa = ?
                  WHERE nim = ?";

        $stmt = mysqli_prepare($connection, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sssssds",
            $name,
            $birth_city,
            $birth_date,
            $faculty,
            $department,
            $gpa,
            $nim
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);
            mysqli_close($connection);

            header(
                "Location: student_view.php?message=" .
                urlencode("Data mahasiswa berhasil diedit.")
            );
            exit;

        } else {

            $error_message = "Gagal mengubah data mahasiswa.";
            mysqli_stmt_close($stmt);
        }
    }

} else {

    // Mengambil data mahasiswa berdasarkan NIM
    if ($nim === "") {
        mysqli_close($connection);
        header("Location: student_view.php");
        exit;
    }

    $query = "SELECT * FROM student WHERE nim = ?";
    $stmt = mysqli_prepare($connection, $query);

    mysqli_stmt_bind_param($stmt, "s", $nim);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    // Jika data tidak ditemukan
    if (!$student) {
        mysqli_close($connection);

        header(
            "Location: student_view.php?message=" .
            urlencode("Data mahasiswa tidak ditemukan.")
        );
        exit;
    }

    // Mengisi form dengan data lama
    $name = $student["name"];
    $birth_city = $student["birth_city"];
    $birth_date = $student["birth_date"];
    $faculty = $student["faculty"];
    $department = $student["department"];
    $gpa = $student["gpa"];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Mahasiswa</title>

    <link href="assets/style.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(120deg, #5B7CE8, #7952A8);
            color: #333;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
        }

        /* Header */
        #header {
            text-align: center;
            margin-bottom: 20px;
        }

        #logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
        }

        /* Garis pemisah */
        hr {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 20px;
        }

        /* Navigasi */
        nav {
            margin-bottom: 20px;
        }

        nav ul {
            display: flex;
            justify-content: center;
            gap: 12px;
            list-style: none;
        }

        nav ul li a {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 8px;
            transition: 0.3s;
        }

        nav ul li a:hover {
            background: white;
            color: #655DC0;
        }

        /* Form */
        form {
            background: white;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        fieldset {
            border: none;
        }

        legend {
            font-size: 22px;
            font-weight: bold;
            color: #625FC0;
            margin-bottom: 25px;
        }

        /* Input */
        form p {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #444;
        }

        input,
        select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 14px;
            background: white;
            outline: none;
            transition: 0.3s;
        }

        input:focus,
        select:focus {
            border-color: #6B70D9;
            box-shadow: 0 0 0 3px rgba(107, 112, 217, 0.12);
        }

        input[readonly] {
            background: #f1f2f7;
            color: #777;
            cursor: not-allowed;
        }

        /* Pesan error */
        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        /* Tombol */
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            margin-bottom: 0;
        }

        button {
            border: none;
            padding: 12px 22px;
            color: white;
            background: linear-gradient(120deg, #5B7CE8, #7952A8);
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }

        .btn-cancel {
            display: inline-block;
            padding: 12px 22px;
            background: #eeeeF5;
            color: #655DC0;
            text-decoration: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #deddf0;
        }

        /* Responsive */
        @media (max-width: 600px) {
            body {
                padding: 25px 12px;
            }

            form {
                padding: 22px;
            }

            #logo {
                font-size: 23px;
            }

            nav ul {
                gap: 7px;
            }

            nav ul li a {
                padding: 9px 12px;
                font-size: 13px;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <div id="header">
            <h1 id="logo">Edit Data Mahasiswa</h1>
        </div>

        <hr>

        <nav>
            <ul>
                <li>
                    <a href="student_view.php">Tampil</a>
                </li>

                <li>
                    <a href="student_add.php">Tambah</a>
                </li>

                <li>
                    <a href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <?php if ($error_message !== ""): ?>
            <p class="error-message">
                <?= htmlspecialchars($error_message) ?>
            </p>
        <?php endif; ?>

        <form action="student_edit.php" method="post">

            <fieldset>

                <legend>Data Mahasiswa</legend>

                <p>
                    <label>NIM:</label>

                    <input
                        type="text"
                        name="nim"
                        value="<?= htmlspecialchars($nim) ?>"
                        readonly
                    >
                </p>

                <p>
                    <label>Nama:</label>

                    <input
                        type="text"
                        name="name"
                        value="<?= htmlspecialchars($name) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Tempat Lahir:</label>

                    <input
                        type="text"
                        name="birth_city"
                        value="<?= htmlspecialchars($birth_city) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Tanggal Lahir:</label>

                    <input
                        type="date"
                        name="birth_date"
                        value="<?= htmlspecialchars($birth_date) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Fakultas:</label>

                    <select name="faculty" required>

                        <option value="FTIB"
                            <?= $faculty === "FTIB" ? "selected" : "" ?>>
                            FTIB
                        </option>

                        <option value="FTEIC"
                            <?= $faculty === "FTEIC" ? "selected" : "" ?>>
                            FTEIC
                        </option>

                    </select>
                </p>

                <p>
                    <label>Jurusan:</label>

                    <input
                        type="text"
                        name="department"
                        value="<?= htmlspecialchars($department) ?>"
                        required
                    >
                </p>

                <p>
                    <label>IPK:</label>

                    <input
                        type="number"
                        name="gpa"
                        min="0"
                        max="4"
                        step="0.01"
                        value="<?= htmlspecialchars((string)$gpa) ?>"
                        required
                    >
                </p>

            </fieldset>

            <p class="form-actions">

                <button type="submit">
                    Simpan Perubahan
                </button>

                <a href="student_view.php" class="btn-cancel">
                    Batal
                </a>

            </p>

        </form>

    </div>

</body>
</html>

<?php
mysqli_close($connection);
?>
