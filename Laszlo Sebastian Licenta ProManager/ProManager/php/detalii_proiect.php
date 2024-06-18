<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$proiect_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT nume_proiect, client, cod_proiect, data_inceput, data_sfarsit FROM proiecte WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $proiect_id, $user_id);
$stmt->execute();
$stmt->bind_result($nume_proiect, $client, $cod_proiect, $data_inceput, $data_sfarsit);
$stmt->fetch();
$stmt->close();

$sql = "SELECT id, nume_task, ore, ore_lucrate FROM taskuri WHERE proiect_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $proiect_id);
$stmt->execute();
$result = $stmt->get_result();

$taskuri = [];
$total_ore = 0;
$total_ore_lucrate = 0;

while ($row = $result->fetch_assoc()) {
    $taskuri[] = $row;
    $total_ore += $row['ore'];
    $total_ore_lucrate += $row['ore_lucrate'];
}

$stmt->close();
$conn->close();

$today = new DateTime();
$data_inceput_dt = new DateTime($data_inceput);
$data_sfarsit_dt = new DateTime($data_sfarsit);
$approaching_end_date = (clone $data_sfarsit_dt)->modify('-7 days');

$notifications = [];

if ($today > $data_sfarsit_dt && $total_ore_lucrate < $total_ore) {
    $notifications[] = "Proiectul a depășit data de sfârșit și nu este finalizat.";
}

if ($today >= $data_inceput_dt && $total_ore_lucrate == 0) {
    $notifications[] = "Proiectul a început, dar nu au fost înregistrate ore lucrate.";
}

if ($today > $data_inceput_dt && $today < $data_sfarsit_dt && ($total_ore_lucrate / $total_ore) < 0.5) {
    $notifications[] = "Proiectul este în desfășurare, dar mai puțin de 50% din orele planificate au fost lucrate.";
}

if ($today == $data_inceput_dt) {
    $notifications[] = "Proiectul începe astăzi.";
}

if ($today >= $approaching_end_date && $today < $data_sfarsit_dt) {
    $notifications[] = "Proiectul se apropie de data de finalizare. Mai sunt " . $today->diff($data_sfarsit_dt)->days . " zile rămase.";
}
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Proiect - <?= htmlspecialchars($nume_proiect) ?></title>
    <link rel="stylesheet" href="../css/detalii_proiect.css?v=1.0">
    <script src="../js/notifications.js" defer></script>
    <script src="../js/project_tooltip.js" defer></script>
    <style>
        .month.active-month {
            background-color: #7fffd4;
        }

        .progress-container {
            margin-top: 20px;
        }

        .progress-bar {
            display: flex;
            height: 30px;
            border: 1px solid #000;
            background-color: #e0e0e0;
        }

        .progress-done {
            background-color: #99ff99;
            height: 100%;
        }

        .progress-todo {
            background-color: #ff9999;
            height: 100%;
        }
    </style>
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
        <h1>Detalii Proiect: <?= htmlspecialchars($nume_proiect) ?></h1>
        <p>Client: <?= htmlspecialchars($client) ?></p>
        <p>Cod Proiect: <?= htmlspecialchars($cod_proiect) ?></p>
        <p>Data Început: <?= htmlspecialchars($data_inceput) ?></p>
        <p>Data Sfârșit: <?= htmlspecialchars($data_sfarsit) ?></p>

        <?php if (!empty($notifications)) : ?>
            <div class="notifications">
                <?php foreach ($notifications as $notification) : ?>
                    <div class="notification"><?= htmlspecialchars($notification) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="progress-container">
            <h2><?= htmlspecialchars($nume_proiect) ?></h2>
            <div class="progress-bar" style="width: 100%;" data-done="<?= $total_ore_lucrate ?>" data-total="<?= $total_ore ?>" data-proiect="<?= htmlspecialchars($nume_proiect) ?>">
                <div class="progress-done" style="width: <?= ($total_ore_lucrate / $total_ore) * 100 ?>%;"></div>
                <div class="progress-todo" style="width: <?= (1 - ($total_ore_lucrate / $total_ore)) * 100 ?>%;"></div>
            </div>
        </div>

        <div class="calendar-container">
            <table class="calendar">
                <tr>
                    <?php
                    $months = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'];
                    $start_month = (int)$data_inceput_dt->format('n');
                    $end_month = (int)$data_sfarsit_dt->format('n');

                    foreach ($months as $index => $month) {
                        $class = '';
                        if ($index + 1 >= $start_month && $index + 1 <= $end_month) {
                            $class = ' active-month';
                        }
                        echo '<td class="month' . $class . '">' . $month . '</td>';
                    }
                    ?>
                </tr>
            </table>
        </div>

        <div class="task-progress">
            <h2>Detalii Taskuri pentru Proiectul</h2>

            <?php foreach ($taskuri as $index => $task) : ?>
                <h3><?= htmlspecialchars($task['nume_task']) ?></h3>
                <div class="progress-bar">
                    <div class="progress-task" style="width: <?= ($task['ore_lucrate'] / $task['ore']) * 100 ?>%; background-color:
                     <?= getColor($index) ?>;" data-task="<?= htmlspecialchars($task['nume_task']) ?>" data-done="<?= $task['ore_lucrate'] ?>" data-total="<?= $task['ore'] ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="dashboard.php" class="button">Înapoi la Dashboard</a>
    </div>

    <div id="projectTooltip" class="project-tooltip"></div>
    <div id="taskTooltip" class="task-tooltip"></div>
</body>

</html>

<?php
function getColor($index)
{
    $colors = ['#ff9999', '#99ccff', '#99ff99', '#ffcc99', '#cc99ff'];
    return $colors[$index % count($colors)];
}
?>
