<?php
$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);

$language = isset($_GET['subject']) ? urldecode($_GET['subject']) : '';
$concepts = isset($data[$language]) ? $data[$language] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($language); ?> Concepts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('code.jpg');
            color: white;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
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
        <h1 class="mb-4" id="main-heading"><?php echo htmlspecialchars($language); ?> Concepts</h1>
        <p class="lead" id="instructions">Select a concept to learn more.</p>
        <div class="row">
            <?php foreach ($concepts as $concept => $description): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($concept); ?></h5>
                            <button class="btn btn-primary" onclick="speakAndRedirect('<?php echo htmlspecialchars($concept); ?>', 'concept_detail.php?subject=<?php echo urlencode($language); ?>&concept=<?php echo urlencode($concept); ?>')">View Details</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const concepts = <?php echo json_encode(array_keys($concepts)); ?>;
        let currentConceptIndex = 0;

        function readText(text) {
            const speech = new SpeechSynthesisUtterance(text);
            speech.lang = 'en-US';
            window.speechSynthesis.speak(speech);
        }

        function speakAndRedirect(text, url) {
            readText(text);
            setTimeout(() => {
                window.location.href = url;
            }, 1000);
        }

        // Read initial headings
        document.addEventListener('keydown', function(event) {
            if (event.key === ' ') {
                readText("<?php echo htmlspecialchars($language); ?> Concepts. Select a concept to learn more.");
                event.preventDefault();
            }
        });

        // Move to the next or previous concept with arrow keys
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowRight') {
                currentConceptIndex = (currentConceptIndex + 1) % concepts.length;
                readText(`Selected Concept is: ${concepts[currentConceptIndex]}`);
            } else if (event.key === 'ArrowLeft') {
                currentConceptIndex = (currentConceptIndex - 1 + concepts.length) % concepts.length;
                readText(`Selected Concept is: ${concepts[currentConceptIndex]}`);
            }
        });
		// Navigate to the previous page with the Backspace key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Backspace') {
        window.history.back();
        event.preventDefault();
    }
});


        // Navigate to the current concept's detail page with Enter key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                window.location.href = `concept_detail.php?subject=<?php echo urlencode($language); ?>&concept=${encodeURIComponent(concepts[currentConceptIndex])}`;
            }
        });

        // Start voice recognition on "V" key press
        document.addEventListener('keydown', function(event) {
            if (event.key.toLowerCase() === 'v') {
                startVoiceRecognition();
            }
        });

        // Start voice recognition and redirect based on spoken concept
        function startVoiceRecognition() {
            readText("Please say the exact name of the concept you want to learn about.");

            if (!('SpeechRecognition' in window) && !('webkitSpeechRecognition' in window)) {
                alert("Speech recognition is not supported in your browser.");
                return;
            }

            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'en-US';
            recognition.start();

            recognition.onresult = function(event) {
                const spokenText = event.results[0][0].transcript.trim();

                const match = concepts.find(concept => concept.toLowerCase() === spokenText.toLowerCase());

                if (match) {
                    window.location.href = `concept_detail.php?subject=<?php echo urlencode($language); ?>&concept=${encodeURIComponent(match)}`;
                } else {
                    speak("Sorry, I didn't recognize that concept. Please try again.");
                    setTimeout(startVoiceRecognition, 2000); // Retry after 2 seconds
                }
            };

            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
                speak("Could not recognize your speech. Please try again.");
                setTimeout(startVoiceRecognition, 2000); // Retry after 2 seconds
            };
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
