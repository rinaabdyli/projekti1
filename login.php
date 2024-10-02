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

// Kontrollo nëse forma është dërguar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emri = $_POST['emri']; // Ndryshoni këtu për të marrë emrin e përdoruesit
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
            echo "Fjalëkalimi i suksesshem! mire se erdhet!!";
        }
    } else {
        echo "Përdoruesi nuk u gjet!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyçu</title>
</head>
<body>
    <h2>Kyçu</h2>
    <form method="post" action="">
        Emri: <input type="text" name="emri" required><br> <!-- Ndryshimi nga Email në Emër -->
        Fjalëkalimi: <input type="password" name="fjalkalimi" required><br>
        <input type="submit" value="Kyçu">
    </form>
</body>
</html>
