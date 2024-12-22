<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Portal</title>
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
        <h1 class="mb-4">Welcome to the Learning Portal</h1>
        <p class="lead">Expand your knowledge in programming languages.</p>
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" data-concept="Start learning programming languages.">
                    <div class="card-body">
                        <h5 class="card-title">Learn</h5>
                        <p class="card-text">Start learning programming languages.</p>
                        <button class="btn btn-primary" onclick="speakAndRedirect('Learn', 'learn.php')">Get Started</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" data-concept="Take tests to evaluate your understanding.">
                    <div class="card-body">
                        <h5 class="card-title">Test Your Knowledge</h5>
                        <p class="card-text">Take tests to evaluate your understanding.</p>
                        <button class="btn btn-secondary" onclick="speakAndRedirect('Test Your Knowledge', 'search_test.php')">Start Testing</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    // Variables to track the selected concept
    let selectedConcept = null;

    // Text-to-Speech and Redirect
    function speakAndRedirect(text, url) {
        const speech = new SpeechSynthesisUtterance(text);
        speech.lang = 'en-US';
        window.speechSynthesis.speak(speech);
        speech.onend = function() {
            window.location.href = url;
        };
    }

    // Voice Recognition to Match Keywords and Redirect
    let recognition;

    function startVoiceRecognition() {
        if (!('SpeechRecognition' in window) && !('webkitSpeechRecognition' in window)) {
            alert("Speech recognition is not supported in your browser.");
            return;
        }

        recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'en-US';

        recognition.start();

        recognition.onresult = function(event) {
            const spokenText = event.results[0][0].transcript.toLowerCase();

            // Match spoken commands with navigation
            if (spokenText.includes('learn')) {
                selectedConcept = 'learn';
                speakAndRedirect('Navigating to Learn', 'learn.php');
            } else if (spokenText.includes('test')) {
                selectedConcept = 'test';
                speakAndRedirect('Navigating to Test', 'search_test.php');
            } else {
                alert("Sorry, I didn't recognize that command. Please try again.");
            }
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            alert('Could not recognize your speech. Please try again.');
        };

        recognition.onend = function() {
            setTimeout(() => {
                recognition.stop();
            }, 5000); // Wait for 5 seconds for the user to speak
        };
    }

    // Function to read the "Learn" or "Test" concept based on arrow keys
    function readConcept(direction) {
        if (direction === 'left') {
            selectedConcept = 'learn';
            const speech = new SpeechSynthesisUtterance("Start learning programming languages.");
            speech.lang = 'en-US';
            window.speechSynthesis.speak(speech);
        } else if (direction === 'right') {
            selectedConcept = 'test';
            const speech = new SpeechSynthesisUtterance("Take tests to evaluate your understanding.");
            speech.lang = 'en-US';
            window.speechSynthesis.speak(speech);
        }
    }

    // Keyboard navigation to read concepts and redirect on Enter
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && selectedConcept) {
            if (selectedConcept === 'learn') {
                window.location.href = 'learn.php';
            } else if (selectedConcept === 'test') {
                window.location.href = 'search_test.php';
            }
        } else if (event.key === 'ArrowRight') {
            readConcept('right');
        } else if (event.key === 'ArrowLeft') {
            readConcept('left');
        } else if (event.key.toLowerCase() === 'v') { // Assign 'V' key for voice recognition
            startVoiceRecognition();
        }
    });

    // Function to read initial instructions
    function readInitialInstructions() {
        const welcomeText = "Welcome to the Learning Portal. Expand your knowledge in programming languages. Press the left arrow key to select the 'Learn' concept, or the right arrow key to select the 'Test Your Knowledge' concept.";
        const speech = new SpeechSynthesisUtterance(welcomeText);
        speech.lang = 'en-US';
        window.speechSynthesis.speak(speech);
    }

    // Initial instructions on Space bar press
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keydown', function(event) {
            if (event.key === ' ') {
                readInitialInstructions();
            }
        });
    });
</script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
