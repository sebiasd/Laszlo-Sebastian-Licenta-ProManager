<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$proiecte = [];
$taskuri = [];

$sql = "SELECT id, nume_proiect FROM proiecte WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $proiecte[] = $row;
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['proiect_id']) && !empty($_POST['proiect_id'])) {
        $proiect_id = $_POST['proiect_id'];

        $sql = "SELECT id, nume_task FROM taskuri WHERE proiect_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $proiect_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $taskuri[] = $row;
            }

            $stmt->close();
        }
    }

    if (isset($_POST['taskuri'])) {
        foreach ($_POST['taskuri'] as $task_id => $ore_lucrate) {
            $ore_lucrate = (int)$ore_lucrate;
            if ($ore_lucrate > 0) {
                $sql = "UPDATE taskuri SET ore_lucrate = ore_lucrate + ? WHERE id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ii", $ore_lucrate, $task_id);
                    if (!$stmt->execute()) {
                        echo "Eroare: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Eroare: " . $conn->error;
                }
            }
        }
        echo "Orele lucrate au fost actualizate.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Ore Lucrate</title>
    <link rel="stylesheet" href="../css/adauga_ore.css?v=1.0">
    <script>
        function submitForm() {
            document.getElementById('projectForm').submit();
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <h1>ProManager</h1>
                <ul>
                    <li><a href="dashboard.php">Proiectele Tale</a></li>
                    <li><a href="creare_proiect.php">Creare Proiect</a></li>
                    <li><a href="adauga_ore.php">Adaugă Ore</a></li>
                    <li><a href="../index.html">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1>Adaugă Ore Lucrate</h1>
        <form id="projectForm" action="adauga_ore.php" method="post">
            <label for="proiect_id">Proiect:</label>
            <select id="proiect_id" name="proiect_id" required onchange="submitForm()">
                <option value="">Selectează un proiect</option>
                <?php foreach ($proiecte as $proiect): ?>
                    <option value="<?= $proiect['id'] ?>" <?= (isset($_POST['proiect_id']) && $_POST['proiect_id'] == $proiect['id']) ? 'selected' : '' ?>><?= htmlspecialchars($proiect['nume_proiect']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!empty($taskuri)): ?>
            <form action="adauga_ore.php" method="post">
                <input type="hidden" name="proiect_id" value="<?= htmlspecialchars($proiect_id) ?>">
                <table>
                    <tr>
                        <th>Task</th>
                        <th>Ore Lucrate</th>
                    </tr>
                    <?php foreach ($taskuri as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['nume_task']) ?></td>
                            <td><input type="number" name="taskuri[<?= $task['id'] ?>]" value="0" min="0"></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <button type="submit">Adaugă Ore</button>
            </form>
        <?php endif; ?>

        <form action="dashboard.php" method="get">
            <button type="submit">Înapoi la Dashboard</button>
        </form>
    </div>
</body>
</html>