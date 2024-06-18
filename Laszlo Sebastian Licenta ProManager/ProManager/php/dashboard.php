<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, nume_proiect, data_inceput, data_sfarsit FROM proiecte WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $proiecte = [];
    while ($row = $result->fetch_assoc()) {
        $proiecte[] = $row;
    }
    $stmt->close();
} else {
    echo "Eroare: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablou de Bord ProManager</title>
    <link rel="stylesheet" href="../css/dashboard.css?v=2.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <h1>ProManager</h1>
                <ul>
                    <li><a href="creare_proiect.php">Creează Proiect</a></li>
                    <li><a href="adauga_ore.php">Adaugă Ore</a></li>
                    <li><a href="../index.html">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="introducere">
        <div class="introducere-content">
            <h1>ProManager</h1>
            <p>Navighează spre succesul proiectelor tale cu ProManager</p>
            <a href="creare_proiect.php" class="button">Creează Proiect Nou</a>
        </div>
    </section>

    <main class="container">
        <section class="proiecte">
            <h2>Proiectele Tale</h2>
            <ul>
                <?php if (!empty($proiecte)): ?>
                    <?php foreach ($proiecte as $proiect): ?>
                        <li>
                            <a href="detalii_proiect.php?id=<?= $proiect['id'] ?>">
                                <?= htmlspecialchars($proiect['nume_proiect']) ?>
                            </a>
                            : <?= $proiect['data_inceput'] ?> / <?= $proiect['data_sfarsit'] ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Niciun proiect găsit.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section class="actiuni">
            <a href="creare_proiect.php" class="button">Creează Proiect Nou</a>
            <a href="adauga_ore.php" class="button">Adaugă Ore Lucrate</a>
        </section>
    </main>
</body>
</html>
