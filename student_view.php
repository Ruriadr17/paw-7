<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Hubungkan ke database
include("connection.php");

// Mengambil pesan dari URL
$message = $_GET["message"] ?? "";

// Query untuk mengambil data mahasiswa
$query = "SELECT * FROM student ORDER BY nim ASC";
$result = mysqli_query($connection, $query);

// Cek kesalahan query
if (!$result) {
    die(
        "Query Error: " .
        mysqli_errno($connection) . " - " .
        mysqli_error($connection)
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Mahasiswa</title>

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
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        #header {
            text-align: center;
            margin-bottom: 20px;
        }

        #logo {
            color: white;
            font-size: 30px;
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
            margin-bottom: 25px;
        }

        nav ul {
            display: flex;
            justify-content: center;
            gap: 12px;
            list-style: none;
        }

        nav ul li a {
            display: inline-block;
            padding: 11px 22px;
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

        /* Notifikasi */
        .pesan {
            background: #dcfce7;
            color: #166534;
            border-left: 5px solid #22c55e;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Tabel */
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        table th {
            background: linear-gradient(120deg, #5B7CE8, #7952A8);
            color: white;
            padding: 15px 12px;
            text-align: center;
            font-size: 14px;
            white-space: nowrap;
        }

        table th:first-child {
            border-radius: 7px 0 0 0;
        }

        table th:last-child {
            border-radius: 0 7px 0 0;
        }

        table td {
            padding: 13px 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            color: #444;
        }

        table tbody tr:hover {
            background-color: #f5f4ff;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Kolom aksi */
        .aksi {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
        }

        .aksi form {
            display: inline;
            margin: 0;
        }

        /* Tombol Edit */
        .btn-edit {
            display: inline-block;
            padding: 8px 14px;
            background: linear-gradient(120deg, #5B7CE8, #7952A8);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-edit:hover {
            opacity: 0.85;
            transform: translateY(-2px);
        }

        /* Tombol Hapus */
        .btn-delete {
            padding: 8px 14px;
            background: linear-gradient(120deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-delete:hover {
            opacity: 0.85;
            transform: translateY(-2px);
        }

        /* Jika data kosong */
        .empty-data {
            text-align: center;
            padding: 25px;
            color: #888;
        }

        /* Responsive */
        @media (max-width: 600px) {
            body {
                padding: 25px 12px;
            }

            #logo {
                font-size: 24px;
            }

            nav ul {
                gap: 7px;
            }

            nav ul li a {
                padding: 9px 13px;
                font-size: 13px;
            }

            .table-container {
                padding: 15px;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <!-- Header -->
        <div id="header">
            <h1 id="logo">Data Mahasiswa</h1>
        </div>

        <hr>

        <!-- Navigasi -->
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

        <!-- Pesan Notifikasi -->
        <?php if ($message !== ""): ?>
            <div class="pesan">
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <!-- Tabel Data Mahasiswa -->
        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Fakultas</th>
                        <th>Jurusan</th>
                        <th>IPK</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php while ($data = mysqli_fetch_assoc($result)): ?>

                            <?php
                            $birth_date = strtotime($data["birth_date"]);
                            $formatted_date = date("d-m-Y", $birth_date);
                            ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($data["nim"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data["name"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data["birth_city"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($formatted_date) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data["faculty"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data["department"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data["gpa"]) ?>
                                </td>

                                <td>

                                    <div class="aksi">

                                        <!-- Tombol Edit -->
                                        <a
                                            href="student_edit.php?nim=<?= urlencode($data["nim"]) ?>"
                                            class="btn-edit">
                                            Edit
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form
                                            action="student_delete.php"
                                            method="post"
                                            onsubmit="return confirm('Yakin ingin menghapus data mahasiswa ini?');">

                                            <input
                                                type="hidden"
                                                name="nim"
                                                value="<?= htmlspecialchars($data["nim"], ENT_QUOTES, "UTF-8") ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="hapus"
                                                class="btn-delete">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="empty-data">
                                Belum ada data mahasiswa.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>

<?php
mysqli_free_result($result);
mysqli_close($connection);
?>