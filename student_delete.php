
<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Hubungkan ke database
include("connection.php");

// Variabel pesan
$message = "";
$status = "";

// Cek apakah tombol Hapus ditekan
if (isset($_POST["hapus"])) {

    // Ambil NIM dari form
    $nim = $_POST["nim"];

    // Gunakan prepared statement untuk menghapus data
    $query = "DELETE FROM student WHERE nim = ?";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        die("Query Error: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "s", $nim);

    // Jalankan perintah hapus
    if (mysqli_stmt_execute($stmt)) {

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $message = "Data mahasiswa berhasil dihapus!";
            $status = "success";
        } else {
            $message = "Data mahasiswa tidak ditemukan.";
            $status = "error";
        }

    } else {
        $message = "Gagal menghapus data mahasiswa.";
        $status = "error";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

} else {
    header("Location: student_view.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notifikasi Hapus Mahasiswa</title>

    <style>

        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        /* BACKGROUND */
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;

            background: linear-gradient(
                135deg,
                #667eea,
                #764ba2,
                #a18cd1
            );
        }

        /* CONTAINER */
        .container {
            background: #ffffff;
            width: 90%;
            max-width: 500px;
            padding: 45px 35px;
            text-align: center;
            border-radius: 22px;

            box-shadow: 0 15px 40px
                rgba(40, 30, 100, 0.25);

            animation: fadeIn 0.5s ease;
        }

        /* ICON */
        .icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 20px;

            display: flex;
            justify-content: center;
            align-items: center;

            border-radius: 50%;
            font-size: 38px;
            color: white;

            background: linear-gradient(
                135deg,
                #667eea,
                #764ba2
            );
        }

        /* ICON ERROR */
        .icon.error {
            background: linear-gradient(
                135deg,
                #ef6b85,
                #9b4dca
            );
        }

        /* TITLE */
        h1 {
            color: #4c3b91;
            font-size: 27px;
            margin-bottom: 15px;
        }

        /* MESSAGE */
        .message {
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        /* NOTIFICATION */
        .notification {
            padding: 17px;
            margin-bottom: 25px;
            border-radius: 12px;

            background: linear-gradient(
                135deg,
                #667eea,
                #764ba2
            );

            color: white;
            font-size: 14px;
            font-weight: 600;

            box-shadow: 0 5px 15px
                rgba(102, 126, 234, 0.25);
        }

        /* NOTIFICATION ERROR */
        .notification.error {
            background: linear-gradient(
                135deg,
                #ef6b85,
                #9b4dca
            );
        }

        /* BUTTON */
        .btn {
            display: inline-block;
            padding: 13px 28px;

            background: linear-gradient(
                135deg,
                #667eea,
                #764ba2
            );

            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;

            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-3px);

            box-shadow: 0 7px 18px
                rgba(102, 126, 234, 0.35);
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;

            color: #94a3b8;
            font-size: 13px;
        }

        /* ANIMATION */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 500px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 23px;
            }
        }

    </style>
</head>

<body>

    <div class="container">

        <div class="icon <?= $status == 'error' ? 'error' : ''; ?>">
            <?= $status == "success" ? "✓" : "!"; ?>
        </div>

        <h1>
            <?= $status == "success"
                ? "Berhasil!"
                : "Penghapusan Gagal"; ?>
        </h1>

        <p class="message">
            <?= $status == "success"
                ? "Data mahasiswa berhasil diproses dan dihapus dari database."
                : "Maaf, data mahasiswa tidak berhasil dihapus."; ?>
        </p>

        <div class="notification <?= $status == 'error' ? 'error' : ''; ?>">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <a href="student_view.php" class="btn">
            Kembali ke Data Mahasiswa
        </a>

        <div class="footer">
            &copy; 2026 Sistem Informasi Mahasiswa
        </div>

    </div>

</body>
</html>
