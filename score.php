<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'logs');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session and get user ID
session_start();
$user_id = $_SESSION['id'];

// Get user responses from `log` table
$response_query = "SELECT * FROM log WHERE user_id = '$user_id'";
$response_result = $conn->query($response_query);

if ($response_result->num_rows > 0) {
    $user_data = $response_result->fetch_assoc();

    // Initialize array to store career set scores
    $career_scores = [];

    // Get career set mappings from `career_set_mapping` table
    $mapping_query = "SELECT question_number, career_set FROM career_set_mapping";
    $mapping_result = $conn->query($mapping_query);

    $question_to_career = [];
    while ($row = $mapping_result->fetch_assoc()) {
        $question_to_career['q' . $row['question_number']] = $row['career_set'];
    }

    // Sum scores for each career set
    foreach ($user_data as $question => $score) {
        if (isset($question_to_career[$question])) {
            $career_set = $question_to_career[$question];
            if (!isset($career_scores[$career_set])) {
                $career_scores[$career_set] = 0;
            }
            $career_scores[$career_set] += intval($score);
        }
    }

    // Return scores as JSON
    echo json_encode($career_scores);

} else {
    echo json_encode(["error" => "No data found for this user"]);
}

$conn->close();
?>
