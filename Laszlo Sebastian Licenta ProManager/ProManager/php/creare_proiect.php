<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $client = $_POST['client'];
    $nume_proiect = $_POST['nume_proiect'];
    $cod_proiect = $_POST['cod_proiect'];
    $data_inceput = $_POST['data_inceput'];
    $data_sfarsit = $_POST['data_sfarsit'];

   
    $predefined_tasks = [
        'Planificare & Analiză' => $_POST['ore_planificare_analiza'],
        'Stabilire Cerințe' => $_POST['ore_stabilire_cerinte'],
        'Developement' => $_POST['ore_developement'],
        'Design' => $_POST['ore_design'],
        'Testare' => $_POST['ore_testare']
    ];


    $tasks_personalizate = isset($_POST['tasks_personalizate']) ? $_POST['tasks_personalizate'] : [];
    $ore_personalizate = isset($_POST['ore_personalizate']) ? $_POST['ore_personalizate'] : [];

    $sql_proiect = "INSERT INTO proiecte (user_id, client, nume_proiect, cod_proiect, data_inceput, data_sfarsit) VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt_proiect = $conn->prepare($sql_proiect)) {
        $stmt_proiect->bind_param("isssss", $user_id, $client, $nume_proiect, $cod_proiect, $data_inceput, $data_sfarsit);
        
        if ($stmt_proiect->execute()) {
            $proiect_id = $stmt_proiect->insert_id;

          
            $sql_task = "INSERT INTO taskuri (proiect_id, nume_task, ore) VALUES (?, ?, ?)";
            $stmt_task = $conn->prepare($sql_task);
            
            foreach ($predefined_tasks as $task => $ore) {
                if (!empty($ore)) {
                    $stmt_task->bind_param("isi", $proiect_id, $task, $ore);
                    $stmt_task->execute();
                }
            }

        
            foreach ($tasks_personalizate as $index => $task_personalizat) {
                if (!empty($task_personalizat) && !empty($ore_personalizate[$index])) {
                    $stmt_task->bind_param("isi", $proiect_id, $task_personalizat, $ore_personalizate[$index]);
                    $stmt_task->execute();
                }
            }

            header("Location: dashboard.php");
            exit();
        } else {
            echo "Eroare: " . $stmt_proiect->error;
        }
        $stmt_proiect->close();
    } else {
        echo "Eroare: " . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crează Proiect Nou</title>
    <!-- Folosim stilurile din dashboard.css -->
    <link rel="stylesheet" href="../css/creare_proiect.css?v=1.0">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <h1>ProManager</h1>
                <ul>
                    <li><a href="dashboard.php">Proiectele Tale</a></li> <!-- Link către dashboard.php -->
                    <li><a href="adauga_ore.php">Adaugă Ore</a></li>
                    <li><a href="../index.html">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>Crează Proiect Nou</h1>
        <form action="creare_proiect.php" method="post">
            <label for="client">Client:</label>
            <input type="text" id="client" name="client" required>
            <label for="nume_proiect">Nume Proiect:</label>
            <input type="text" id="nume_proiect" name="nume_proiect" required>
            <label for="cod_proiect">Cod Proiect:</label>
            <input type="text" id="cod_proiect" name="cod_proiect">
            <label for="data_inceput">Data Început:</label>
            <input type="date" id="data_inceput" name="data_inceput" required>
            <label for="data_sfarsit">Data Sfârșit:</label>
            <input type="date" id="data_sfarsit" name="data_sfarsit" required>

            <h2>Sarcini Predefinite:</h2>
            <div>
                <input type="checkbox" id="planificare_analiza" name="task_predefinite[]" value="Planificare & Analiză">
                <label for="planificare_analiza">Planificare & Analiză</label>
                <input type="number" id="ore_planificare_analiza" name="ore_planificare_analiza" placeholder="Ore">
            </div>
            <div>
                <input type="checkbox" id="stabilire_cerinte" name="task_predefinite[]" value="Stabilire Cerințe">
                <label for="stabilire_cerinte">Stabilire Cerințe</label>
                <input type="number" id="ore_stabilire_cerinte" name="ore_stabilire_cerinte" placeholder="Ore">
            </div>
            <div>
                <input type="checkbox" id="developement" name="task_predefinite[]" value="Developement">
                <label for="developement">Developement</label>
                <input type="number" id="ore_developement" name="ore_developement" placeholder="Ore">
            </div>
            <div>
                <input type="checkbox" id="design" name="task_predefinite[]" value="Design">
                <label for="design">Design</label>
                <input type="number" id="ore_design" name="ore_design" placeholder="Ore">
            </div>
            <div>
                <input type="checkbox" id="testare" name="task_predefinite[]" value="Testare">
                <label for="testare">Testare</label>
                <input type="number" id="ore_testare" name="ore_testare" placeholder="Ore">
            </div>

            <h2>Sarcini Personalizate:</h2>
            <div id="taskuri_personalizate">
                <div class="task_personalizat">
                    <label for="tasks_personalizate[]">Task:</label>
                    <input type="text" name="tasks_personalizate[]" placeholder="Nume task">
                    <label for="ore_personalizate[]">Ore:</label>
                    <input type="number" name="ore_personalizate[]" placeholder="Ore">
                </div>
            </div>
            <button type="button" id="adauga_sarcina">+ Adaugă Sarcină</button>
            
            <button type="submit">Salvează Proiect</button>
            <button type="button" id="inapoi_dashboard" class="button">Înapoi la Dashboard</button>
        </form>
    </div>
    
    <script>
        document.getElementById("adauga_sarcina").addEventListener("click", function() {
            var newTask = document.createElement("div");
            newTask.classList.add("task_personalizat");
            newTask.innerHTML = `
                <label for="tasks_personalizate[]">Task:</label>
                <input type="text" name="tasks_personalizate[]" placeholder="Nume task">
                <label for="ore_personalizate[]">Ore:</label>
                <input type="number" name="ore_personalizate[]" placeholder="Ore">
            `;
            document.getElementById("taskuri_personalizate").appendChild(newTask);
        });

        document.getElementById("inapoi_dashboard").addEventListener("click", function() {
            window.location.href = "dashboard.php";
        });
    </script>
</body>
</html>
