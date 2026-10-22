<?php
session_start();
include 'db.php';

// Check karein ke user login hai ya nahi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Task Add Karne ka Code (User ID ke sath)
if (isset($_POST['add_task'])) {
    $task_name = trim($_POST['task_name']);
    if (!empty($task_name)) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $task_name);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit();
    }
}

// Task Delete Karne ka Code (Sirf wahi delete ho jo ussi user ka ho)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Sirf login kiye hue user ke tasks fetch karna
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Todo App</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 0; padding: 50px; }
        .container { max-width: 500px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .logout { background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        form { display: flex; gap: 10px; margin: 20px 0; }
        input[type="text"] { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        ul { list-style: none; padding: 0; }
        li { display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 10px; margin-bottom: 8px; border-radius: 4px; border-left: 4px solid #007bff; }
        .btn-delete { background: #dc3545; color: white; text-decoration: none; padding: 5px 8px; font-size: 12px; border-radius: 3px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <form method="POST" action="">
        <input type="text" name="task_name" placeholder="Enter a new task..." required>
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <span><?php echo htmlspecialchars($row['task_name']); ?></span>
                <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

</body>
</html>