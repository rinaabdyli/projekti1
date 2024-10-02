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
    $emri = $_POST['emri'];
    $fjalkalimi = password_hash($_POST['fjalkalimi'], PASSWORD_DEFAULT); // Hash fjalëkalimi

    // Shto përdoruesin në databazë
    $sql = "INSERT INTO perdoruesi (emri, fjalekalimi) VALUES ('$emri', '$fjalkalimi')";

    if ($conn->query($sql) === TRUE) {
        echo "Regjistrimi ishte i suksesshëm!";
    } else {
        echo "Gabim: " . $sql . "<br>" . $conn->error;
    }
}

// Kyçja
if (isset($_POST['login'])) {
    $emri = $_POST['emri']; // Merr emrin nga forma
    $fjalkalimi = $_POST['fjalkalimi'];

    // Merr përdoruesin nga databaza
    $sql = "SELECT * FROM perdoruesi WHERE emri='$emri'"; // Përdor emrin e saktë
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Kontrollo fjalëkalimin
        if (password_verify($fjalkalimi, $row['fjalkalimi'])) {
            $_SESSION['emri'] = $row['emri'];
            echo "Kyqja ishte e suksesshme! Mirë se erdhët, " . $row['emri'];
        } else {
            echo "Fjalëkalimi i gabuar!";
        }
    } else {
        echo "Përdoruesi nuk u gjet!";
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
        Emri: <input type="text" name="emri" required><br>
        Fjalëkalimi: <input type="password" name="fjalkalimi" required><br>
        <input type="submit" name="login" value="Kyçu">
    </form>
</body>
</html>
