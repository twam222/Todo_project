<?php
    define('DB_USER', 'root');
    define('DB_PASS', 'hmad_lok');
    define('DB_NAME', 'todolist'); 
    define('DB_HOST', '127.0.0.1'); 
    define('DB_PORT', '3306');
    




// Establish a database connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actionADD']) && $_POST['actionADD'] === 'new') {
        // Add a new task to the "todo" table
        if (isset($_POST['task-title']) && !empty($_POST['task-title'])) {
            $taskTitle = $mysqli->real_escape_string($_POST['task-title']);
            $sql = "INSERT INTO todo (task_title) VALUES ('$taskTitle')";
            $mysqli->query($sql);
        }
    } elseif (isset($_POST['delete']) && $_POST['delete'] === 'Delete') {
        // Delete a task from the "todo" table
        if (isset($_POST['delete_id'])) {
            $taskId = $mysqli->real_escape_string($_POST['delete_id']);
            $sql = "DELETE FROM todo WHERE id = $taskId";
            $mysqli->query($sql);
        }
    } elseif (isset($_POST['toggle']) && $_POST['toggle'] === 'Toggle') {
        // Toggle the "done" field of a task in the "todo" table
        if (isset($_POST['toggle_id'])) {
            $taskId = $mysqli->real_escape_string($_POST['toggle_id']);
            $sql = "UPDATE todo SET done = 1 - done WHERE id = $taskId";
            $mysqli->query($sql);
        }
    }

    // Redirect to avoid form resubmission on refresh
    header("Location: index.php");
    exit();
}

// Fetch tasks from the "todo" table and store them in $taches
$sql = "SELECT * FROM todo ORDER BY creation_date DESC";
$result = $mysqli->query($sql);

$taches = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $taches[] = $row;
    }
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Todo List</a>
    </nav>

    <div class="container mt-4">
        <!-- Todo Form -->
        <form id="todo-form" method="post" class="mb-3">
            <div class="input-group">
                <input type="text" id="task-title" name="task-title" class="form-control" placeholder="Task Title" required>
                <div class="input-group-append">
                    <button type="submit" name="actionADD" value="new" class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>

        <!-- Todo List -->
        <ul class="list-group">
            <?php foreach ($taches as $tache): ?>
                <form method="post" class="mb-2">
                    <li class="list-group-item <?php echo $tache['done'] ? 'list-group-item-success' : 'list-group-item-warning'; ?>">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="toggle_id" value="<?php echo $tache['id']; ?>" class="form-check-input" <?php echo $tache['done'] ? 'checked' : ''; ?>>
                                <label class="form-check-label"><?php echo $tache['task_title']; ?></label>
                            </div>
                        </div>
                        <button type="submit" name="" value="toggle" class="btn btn-sm btn-info">Toggle</button>
                        <button type="submit" name="" value="delete" class="btn btn-sm btn-danger">Delete</button>
                        <input type="hidden" name="delete_id" value="<?php echo $tache['id']; ?>">
                    </li>
                </form>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>