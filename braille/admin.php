<?php
$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'];
    $concept = $_POST['concept'];
    $subConcept = $_POST['subconcept'];
    $description = $_POST['description'];

    // Ensure the language array exists
    if (!isset($data[$language])) {
        $data[$language] = [];
    }
    // Ensure the concept array exists
    if (!isset($data[$language][$concept])) {
        $data[$language][$concept] = [];
    }
    // Add the sub-concept and description
    $data[$language][$concept][$subConcept] = [
        'description' => $description,
    ];

    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Learning Portal</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="learn.php">Learn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search_test.php">Test</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Add Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Add New Language/Concept/Sub-Concept</h1>
        <form method="post">
            <div class="mb-3">
                <label for="language" class="form-label">Programming Language</label>
                <input type="text" class="form-control" id="language" name="language" required>
            </div>
            <div class="mb-3">
                <label for="concept" class="form-label">Concept</label>
                <input type="text" class="form-control" id="concept" name="concept" required>
            </div>
            <div class="mb-3">
                <label for="subconcept" class="form-label">Sub-Concept</label>
                <input type="text" class="form-control" id="subconcept" name="subconcept" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Add Concept</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
