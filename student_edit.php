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
    <title>Edit Mahasiswa</title>
    <link href="assets/style.css" rel="stylesheet">
</head>

<body>
<div class="container">

    <div id="header">
        <h1 id="logo">Edit Data Mahasiswa</h1>
    </div>

    <hr>

    <nav>
        <ul>
            <li><a href="student_view.php">Tampil</a></li>
            <li><a href="student_add.php">Tambah</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <?php if ($error_message !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error_message) ?>
        </p>
    <?php endif; ?>

    <form action="student_edit.php" method="post">

        <fieldset>
            <legend>Data Mahasiswa</legend>

            <p>
                <label>NIM:</label><br>
                <input
                    type="text"
                    name="nim"
                    value="<?= htmlspecialchars($nim) ?>"
                    readonly
                >
            </p>

            <p>
                <label>Nama:</label><br>
                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($name) ?>"
                    required
                >
            </p>

            <p>
                <label>Tempat Lahir:</label><br>
                <input
                    type="text"
                    name="birth_city"
                    value="<?= htmlspecialchars($birth_city) ?>"
                    required
                >
            </p>

            <p>
                <label>Tanggal Lahir:</label><br>
                <input
                    type="date"
                    name="birth_date"
                    value="<?= htmlspecialchars($birth_date) ?>"
                    required
                >
            </p>

            <p>
                <label>Fakultas:</label><br>
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
                <label>Jurusan:</label><br>
                <input
                    type="text"
                    name="department"
                    value="<?= htmlspecialchars($department) ?>"
                    required
                >
            </p>

            <p>
                <label>IPK:</label><br>
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

        <p>
            <button type="submit">Simpan Perubahan</button>
            <a href="student_view.php">Batal</a>
        </p>

    </form>

</div>
</body>
</html>

<?php
mysqli_close($connection);
?>
