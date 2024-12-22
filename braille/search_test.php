<?php
// File to save data
$jsonFile = 'submitted_code.json';
$output = '';
// Initialize feedback
$feedback = '';
$selectedLanguage = '';

// Retrieve the selected language from the JSON data (if exists)
if (file_exists($jsonFile)) {
    $existingData = json_decode(file_get_contents($jsonFile), true);
    if (!empty($existingData)) {
        $lastEntry = end($existingData);
        $selectedLanguage = $lastEntry['language'] ?? '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted code and selected language from the form
    $submittedCode = $_POST['code_input'] ?? '';
    $language = $_POST['language'] ?? 'Not selected';

    if (!empty($submittedCode)) {
        // Create a new entry with the code, language, and timestamp
        $newEntry = [
            'code' => $submittedCode,
            'language' => $language,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Read existing JSON data if the file exists
        $existingData = [];
        if (file_exists($jsonFile)) {
            $existingData = json_decode(file_get_contents($jsonFile), true) ?? [];
        }

        // Append the new entry to the existing data
        $existingData[] = $newEntry;

        // Save updated data back to the JSON file
        if (file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT))) {
            $output = shell_exec("python test.py");
        } else {
            $feedback = "Failed to save the data.";
        }
    } else {
        $feedback = "No code was submitted!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Compiler Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('code.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }
        .container {
            margin-top: 50px;
            text-align: center;
        }
        .compiler-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
        }
        .code-box {
            width: 100%;
            max-width: 600px;
            height: 300px;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 18px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
            font-family: monospace;
            margin-bottom: 15px;
        }
        .dropdown {
            margin-bottom: 20px;
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

    <!-- Code Compiler Form -->
    <div class="container compiler-container">
        <p><?php echo $output; ?></p>
        <form id="codeForm" method="POST" action="search_test.php">
            <select name="language" id="languageSelect" class="form-select dropdown" required>
                <option value="" disabled selected>Select a language</option>
                <option value="Python">Python</option>
                <option value="HTML">HTML</option>
            </select>
            <textarea name="code_input" class="code-box" placeholder="Write your code here..." required></textarea>
            <button type="submit" class="btn btn-primary">Submit Code</button>
        </form>

        <!-- Display feedback -->
        <?php if (!empty($feedback)): ?>
            <div class="alert alert-<?php echo ($feedback === "Code and language saved successfully!") ? 'success' : 'danger'; ?> mt-3">
                <?php echo htmlspecialchars($feedback); ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const languageSelect = document.getElementById('languageSelect');
    const codeBox = document.querySelector('.code-box');
    const codeForm = document.getElementById('codeForm');

    // Announce to select a language
    const speak = (text) => {
        const speech = new SpeechSynthesisUtterance(text);
        speech.lang = 'en-US';
        window.speechSynthesis.speak(speech);
    };

    // Handle dropdown selection
    languageSelect.addEventListener('change', function () {
        speak(`You selected ${this.value}`);
    });

    // Listen for right or left arrow key press
    document.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
            speak("Please select a language. Use arrow keys Up and Down to navigate. Press Tab to start typing. Press Enter to submit.");
        }

        // Set focus to the text area when 'Tab' is pressed
        if (event.key === 'Tab') {
            event.preventDefault(); // Prevent the default tab behavior
            codeBox.focus();
        }

        // Submit the form when Enter is pressed without Shift
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault(); // Prevent the default Enter behavior
            codeForm.submit(); // Submit the form
        }
    });

    // Speak the output from PHP
    const output = <?php echo json_encode($output); ?>;
    if (output) {
        speak(output);
    }

    // Set focus and navigation for dropdown using arrow keys
    languageSelect.focus();
});
</script>

</body>
</html>
