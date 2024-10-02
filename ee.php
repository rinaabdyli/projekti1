<?php
session_start();

$servername = "localhost"; // ose IP e serverit tuaj
$username = "root"; // emri i përdoruesit të MySQL
$password = ""; // fjalëkalimi për MySQL
$dbname = "konsumatori"; // emri i databazës

// Krijimi i lidhjes me databazën
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrollo lidhjen
if ($conn->connect_error) {
    die("Lidhja ka dështuar: " . $conn->connect_error);
}

// Regjistrimi
if (isset($_POST['register'])) {
    $emri = trim($_POST['emri']);
    $fjalkalimi = $_POST['fjalkalimi'] ?? '';

    if (empty($emri) || empty($fjalkalimi)) {
        echo "Të gjitha fushat janë të detyrueshme!";
    } else {
        // Kontrollo nëse emri përmbush kriteret
        if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $emri)) {
            echo "Emri duhet të jetë mes 3 dhe 20 karaktereve dhe të përmbajë vetëm letra dhe numra.";
        } else {
            // Kontrollo nëse përdoruesi ekziston
            $stmt = $conn->prepare("SELECT * FROM perdoruesi WHERE emri = ?");
            if ($stmt === false) {
                die("Gabim në përgatitjen e deklaratës: " . $conn->error);
            }
            $stmt->bind_param("s", $emri);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "Përdoruesi tashmë ekziston!";
            } else {
                // Regjistro përdoruesin
                $fjalkalimiHash = password_hash($fjalkalimi, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO perdoruesi (emri, fjalkalimi) VALUES (?, ?)");
                if ($stmt === false) {
                    die("Gabim në përgatitjen e deklaratës: " . $conn->error);
                }
                $stmt->bind_param("ss", $emri, $fjalkalimiHash);
                if ($stmt->execute()) {
                    echo "Regjistrimi ishte i suksesshëm!";
                } else {
                    echo "Gabim gjatë regjistrimit: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}

// Kyçja
if (isset($_POST['login'])) {
    $emri = trim($_POST['emri_login']);
    $fjalkalimi = $_POST['fjalkalimi_login'] ?? '';

    if (empty($emri) || empty($fjalkalimi)) {
        echo "Të gjitha fushat janë të detyrueshme!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM perdoruesi WHERE emri = ?");
        if ($stmt === false) {
            die("Gabim në përgatitjen e deklaratës: " . $conn->error);
        }
        $stmt->bind_param("s", $emri);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($fjalkalimi, $row['fjalkalimi'])) {
                $_SESSION['emri'] = $row['emri'];
                echo "Kyqja ishte e suksesshme! Mirë se erdhët, " . htmlspecialchars($row['emri']);
            } else {
                echo "Fjalëkalimi i gabuar!";
            }
        } else {
            echo "Përdoruesi nuk u gjet!";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regjistrimi dhe Kyçja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Regjistrohu</h2>
    <form method="post" action="">
        Emri: <input type="text" name="emri" required><br>
        Fjalëkalimi: <input type="password" name="fjalkalimi" required><br>
        <input type="submit" name="register" value="Regjistrohu">
    </form>

    <h2>Kyçu</h2>
    <form method="post" action="">
        Emri: <input type="text" name="emri_login" required><br>
        Fjalëkalimi: <input type="password" name="fjalkalimi_login" required><br>
        <input type="submit" name="login" value="Kyçu">
    </form>
</body>
</html>
