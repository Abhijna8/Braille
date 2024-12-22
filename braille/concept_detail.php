<?php
$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);

// Get parameters
$language = isset($_GET['subject']) ? urldecode($_GET['subject']) : '';
$concept = isset($_GET['concept']) ? urldecode($_GET['concept']) : '';
$subConcept = isset($_GET['subconcept']) ? urldecode($_GET['subconcept']) : '';

// Initialize variables for description and found status
$description = '';
$found = false;

// Check if language exists
if (isset($data[$language])) {
    if (isset($data[$language][$concept])) {
        if (!empty($subConcept) && isset($data[$language][$concept][$subConcept])) {
            $description = $data[$language][$concept][$subConcept]['description'];
            $found = true;
        } else {
            $subConcepts = array_keys($data[$language][$concept]);
            if (!empty($subConcepts)) {
                $found = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subConcept); ?> - <?php echo htmlspecialchars($concept); ?> - <?php echo htmlspecialchars($language); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0e6d6;
            color: #333;
            font-family: 'Georgia', serif;
            padding: 20px;
        }
        .book-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .book {
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .book-title {
            background-color: #4a2c2a;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .book-content {
            padding: 20px;
            flex-grow: 1;
            overflow-y: auto;
            height: 400px;
        }
        .book-footer {
            text-align: right;
            padding: 10px;
            background-color: #eae7e1;
        }
        .sub-concept-list {
            list-style: none;
            padding-left: 0;
        }
        .sub-concept-list li {
            margin: 5px 0;
            cursor: pointer;
            padding: 5px;
        }
        .sub-concept-list li.selected {
            background-color: #d3c3b3;
            font-weight: bold;
        }
    </style>
    <script>
        let currentUtterance = null;
        let recognizing = false;
        let selectedIndex = 0;
        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'en-US';
        recognition.interimResults = false;

        function speak(text) {
            stopSpeech();
            currentUtterance = new SpeechSynthesisUtterance(text);
            speechSynthesis.speak(currentUtterance);
        }

        function stopSpeech() {
            if (currentUtterance) {
                speechSynthesis.cancel();
                currentUtterance = null;
            }
        }

        function startRecognition() {
            if (!recognizing) {
                speak("Please say the name of the concept you want more details about.");
                recognition.start();
                recognizing = true;
            }
        }

        function readPageContent() {
            const content = document.querySelector('.book-content').textContent;
            speak(content);
        }

        function navigateConcepts(direction) {
            const listItems = document.querySelectorAll('.sub-concept-list li');
            if (listItems.length > 0) {
                listItems[selectedIndex].classList.remove('selected');
                selectedIndex = (selectedIndex + direction + listItems.length) % listItems.length;
                listItems[selectedIndex].classList.add('selected');
                speak(listItems[selectedIndex].textContent);
            }
        }

        document.addEventListener('keydown', function(event) {
            switch(event.key) {
                case ' ':
                    event.preventDefault();
                    readPageContent();
                    break;
                case 'ArrowRight':
                    navigateConcepts(1);
                    break;
                case 'ArrowLeft':
                    navigateConcepts(-1);
                    break;
                case 'Enter':
                    const selectedLink = document.querySelector('.sub-concept-list li.selected a');
                    if (selectedLink) {
                        window.location.href = selectedLink.href;
                    }
                    break;
                case 'v':
                case 'V':
                    startRecognition();
                    break;
                case 's':
                case 'S':
                    stopSpeech();
                    break;
                case 'Backspace':
                    window.history.back();
                    break;
            }
        });

        recognition.onresult = function(event) {
            const spokenText = event.results[0][0].transcript.trim();
            const concepts = <?php echo json_encode(array_keys($data[$language][$concept])); ?>;
            const match = concepts.find(c => c.toLowerCase() === spokenText.toLowerCase());

            if (match) {
                window.location.href = `concept_detail.php?subject=<?php echo urlencode($language); ?>&concept=<?php echo urlencode($concept); ?>&subconcept=${encodeURIComponent(match)}`;
            } else {
                speak("Sorry, I didn't recognize that command. Please try again.");
            }
            recognizing = false;
        };

        recognition.onerror = function(event) {
            speak("Could not recognize your speech. Please try again.");
            recognizing = false;
        };

        recognition.onend = function() {
            recognizing = false;
        };

        window.onload = function() {
            document.querySelectorAll('.sub-concept-list li')[selectedIndex].classList.add('selected');
            <?php if (!empty($subConcept) && isset($data[$language][$concept][$subConcept])): ?>
                readPageContent();
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Learning Portal</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="book-container">
        <div class="book">
            <div class="book-title">
                <h1><?php echo htmlspecialchars($concept); ?></h1>
            </div>
            <div class="book-content">
                <?php if ($found): ?>
                    <?php if (!empty($subConcept)): ?>
                        <h2><?php echo htmlspecialchars($subConcept); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($description)); ?></p>

                    <?php else: ?>
                        <ul class="sub-concept-list">
                            <?php foreach ($subConcepts as $item): ?>
                                <li><a href="?subject=<?php echo urlencode($language); ?>&concept=<?php echo urlencode($concept); ?>&subconcept=<?php echo urlencode($item); ?>"><?php echo htmlspecialchars($item); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Concept not found.</p>
                <?php endif; ?>
            </div>
            <div class="book-footer">
                <p>Use the Spacebar to read the page content, arrow keys to navigate, Enter to select, "V" to start voice search, "S" to stop reading, and Backspace to go back.</p>
            </div>
        </div>
    </div>
</body>
</html>
