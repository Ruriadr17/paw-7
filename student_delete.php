<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Hubungkan ke database
include("connection.php");

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
        } else {
            $message = "Data mahasiswa tidak ditemukan.";
        }

    } else {
        $message = "Gagal menghapus data mahasiswa.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    // Kembali ke halaman tampil data
    header("Location: student_view.php?message=" . urlencode($message));
    exit;

} else {
    // Jika halaman dibuka tanpa menekan tombol Hapus
    header("Location: student_view.php");
    exit;
}
?>
```