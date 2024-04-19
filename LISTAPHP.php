<?php
// Función para obtener las tareas desde un archivo de texto
function getTasksFromFile() {
    $tasks = [];
    $file = fopen("tasks.txt", "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $tasks[] = trim($line);
        }
        fclose($file);
    }
    return $tasks;
}

// Función para guardar las tareas en un archivo de texto
function saveTasksToFile($tasks) {
    $file = fopen("tasks.txt", "w");
    if ($file) {
        foreach ($tasks as $task) {
            fwrite($file, $task . "\n");
        }
        fclose($file);
    }
}

// Procesar el formulario para agregar una tarea
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addTaskButton"])) {
    $taskInput = $_POST["taskInput"];
    if (!empty($taskInput)) {
        $tasks = getTasksFromFile();
        $tasks[] = $taskInput;
        saveTasksToFile($tasks);
        // Recargar la página para mostrar la nueva tarea
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Procesar el formulario para eliminar una tarea
if (isset($_POST["deleteTaskButton"])) {
    $taskIndex = $_POST["taskIndex"];
    $tasks = getTasksFromFile();
    if (isset($tasks[$taskIndex])) {
        unset($tasks[$taskIndex]);
        saveTasksToFile(array_values($tasks)); // Reindexar el array después de eliminar una tarea
    }
    // Recargar la página para reflejar los cambios
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Procesar el formulario para editar una tarea
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editTaskButton"])) {
    $taskIndex = $_POST["taskIndex"];
    $newTaskContent = $_POST["editedTaskContent"];
    $tasks = getTasksFromFile();
    if (isset($tasks[$taskIndex])) {
        $tasks[$taskIndex] = $newTaskContent;
        saveTasksToFile($tasks);
    }
    // Recargar la página para reflejar los cambios
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Obtener las tareas existentes
$tasks = getTasksFromFile();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <style>
        /* Estilos */
    </style>
</head>
<body>

<div class="task-container">
    <h2>Lista de Tareas</h2>

    <form id="taskForm" method="POST">
        <input type="text" id="taskInput" name="taskInput" placeholder="Escribe una tarea...">
        <button type="submit" name="addTaskButton">Agregar tarea</button>
    </form>

    <ul id="taskList">
        <?php
        // Mostrar las tareas existentes
        foreach ($tasks as $index => $task) {
            echo "<li class='taskItem'> 
                    <form method='POST' style='display: inline;'> 
                        <input type='hidden' name='taskIndex' value='{$index}'> 
                        <input type='submit' name='deleteTaskButton' value='Eliminar' style='display: inline; float:right;'> 
                    </form>
                    <form method='POST' style='display: inline;'>
                        <input type='hidden' name='taskIndex' value='{$index}'> 
                        <input type='text' name='editedTaskContent' value='{$task}' style='display: inline;'> 
                        <input type='submit' name='editTaskButton' value='Editar' style='display: inline;'>
                    </form>
                    <input type='checkbox' class='taskCheckbox' style='display: inline;'> 
                    <span>{$task}</span> 
                  </li>";
        }
        ?>
    </ul>
</div>

</body>
</html>
