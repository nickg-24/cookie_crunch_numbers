<?php
// Define directories for test and production environments
$testDirectory = 'test_results/';
$prodDirectory = 'prod_results/';

// Set current directory to test or prod (you can switch this manually for testing)
// $currentDirectory = $testDirectory; // Change this to $prodDirectory when ready for production
$currentDirectory = $prodDirectory;

// Ensure that the directory exists
if (!file_exists($currentDirectory)) {
    mkdir($currentDirectory, 0777, true);
}

// Answer key setup (define the correct values for each task)
$answerKey = [
    1 => [ 'name' => 'sessionID', 'value' => 'abc123' ], // Optional expiration check can be added if stored separately
    2 => [ 'name' => 'userPreferences', 'value' => 'darkMode=true' ],
    3 => [ 'name' => 'trackingConsent', 'value' => 'no' ],
    4 => [ 'name' => 'adConsent', 'deleted' => true ], // Deletion task
    5 => [ 'name' => 'promoCode', 'value' => 'DISCOUNT20' ]
];

// Function to check task correctness
function checkTaskCorrectness($taskData, $answerKey) {
    $taskNumber = $taskData['taskNumber'];
    $taskCategory = $taskData['taskCategory'];

    // Depending on the task category, verify the data
    switch ($taskCategory) {
        case 'Cookie Creation':
        case 'Cookie Update':
            // Fetch cookies from browser (use $_COOKIE)
            $cookies = $_COOKIE;
            $expected = $answerKey[$taskNumber];
            
            // Check if the cookie exists and matches the expected values
            if (isset($cookies[$expected['name']])) {
                $actualValue = $cookies[$expected['name']];
                return $actualValue == $expected['value'];
            }
            return false; // Cookie does not exist or value doesn't match
        
        case 'Cookie Reading':
            // Compare user's true/false response with the correct answer
            return $taskData['userResponse'] == ($answerKey[$taskNumber]['value'] == 'darkMode=true');
        
        case 'Cookie Deletion':
            // Check if the cookie no longer exists
            return !isset($_COOKIE[$answerKey[$taskNumber]['name']]);
        
        default:
            return false; // Invalid task category
    }
}

// Function to save task results and group by session token
function saveTaskResults($data, $currentDirectory, $answerKey) {
    $sessionToken = $data['sessionToken'];

    // Define the file path for this user's session (e.g., session_abc123xyz.json)
    $file = $currentDirectory . 'session_' . $sessionToken . '.json';

    // Open the file for reading and writing
    $fileHandle = fopen($file, 'c+');

    if ($fileHandle) {
        // Lock the file to prevent simultaneous writes
        if (flock($fileHandle, LOCK_EX)) {
            // Read the existing data from the JSON file
            $currentData = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

            // Check if the task was completed correctly
            $correct = checkTaskCorrectness($data, $answerKey);

            // Append the new task data for this session
            $data['correct'] = $correct; // Add correctness flag
            $currentData[] = $data;

            // Write the updated data back to the JSON file
            ftruncate($fileHandle, 0); // Clear the file
            fwrite($fileHandle, json_encode($currentData, JSON_PRETTY_PRINT));

            // Unlock the file
            flock($fileHandle, LOCK_UN);
        }

        fclose($fileHandle);

        return ['status' => 'success', 'message' => 'Task recorded successfully for session ' . $sessionToken];
    } else {
        return ['status' => 'error', 'message' => 'Could not open file for writing.'];
    }
}

// Handle incoming requests
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    // Handle task result recording
    $data = json_decode(file_get_contents("php://input"), true);
    $response = saveTaskResults($data, $currentDirectory, $answerKey);
} elseif ($requestMethod === 'GET' && isset($_GET['report'])) {
    // Generate report (optional)
    $response = generateReport();
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method.'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
