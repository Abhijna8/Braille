<?php
$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);
$languageList = array_keys($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn Page</title>
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
                    <li class="nav-item"><a class="nav-link" href="learn.php">Learn</a></li>
                    <li class="nav-item"><a class="nav-link" href="search_test.php">Test</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Add Data</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4" id="main-heading">Programming Languages</h1>
        <p class="lead" id="instructions">Select a language to learn more.</p>
        <div class="row">
            <?php foreach ($data as $language => $concepts): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($language); ?></h5>
                            <button class="btn btn-primary" onclick="speakAndRedirect('<?php echo htmlspecialchars($language); ?>', 'concept.php?subject=<?php echo urlencode($language); ?>')">View Concepts</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const languages = <?php echo json_encode($languageList); ?>;
        let currentLanguageIndex = 0;

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
                readText("Programming Languages. Select a language to learn more.");
                event.preventDefault();
            }
        });

        // Move to the next or previous language with arrow keys
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowRight') {
                currentLanguageIndex = (currentLanguageIndex + 1) % languages.length;
                readText(`Selected Language is: ${languages[currentLanguageIndex]}`);
            } else if (event.key === 'ArrowLeft') {
                currentLanguageIndex = (currentLanguageIndex - 1 + languages.length) % languages.length;
                readText(`Selected Language is: ${languages[currentLanguageIndex]}`);
            }
        });

        // Navigate to the current language's concept page with Enter key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                window.location.href = `concept.php?subject=${encodeURIComponent(languages[currentLanguageIndex])}`;
            }
        });

        // Start voice recognition on "V" key press
        document.addEventListener('keydown', function(event) {
            if (event.key.toLowerCase() === 'v') {
                startVoiceRecognition();
            }
        });
		
		// Navigate to the previous page with the Backspace key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Backspace') {
        window.history.back();
        event.preventDefault();
    }
});


        // Start voice recognition and redirect based on spoken language
        function startVoiceRecognition() {
            readText("Please say the name of a language.");

            if (!('SpeechRecognition' in window) && !('webkitSpeechRecognition' in window)) {
                alert("Speech recognition is not supported in your browser.");
                return;
            }

            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'en-US';
            recognition.start();

            recognition.onresult = function(event) {
                const spokenText = event.results[0][0].transcript.toLowerCase();

                for (let language of languages) {
                    if (spokenText.includes(language.toLowerCase())) {
                        window.location.href = `concept.php?subject=${encodeURIComponent(language)}`;
                        return;
                    }
                }

                alert("Sorry, I didn't recognize that language. Please try again.");
            };

            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
                alert('Could not recognize your speech. Please try again.');
            };
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
