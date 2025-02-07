<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'logs');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to get user data
session_start();
$user_id = $_SESSION['id']; // Retrieve user ID from session after login

// Capture form data (quiz responses)
$q1 = $_POST['q1'];
$q2 = $_POST['q2'];
$q3 = $_POST['q3'];
$q4 = $_POST['q4'];
$q5 = $_POST['q5'];
$q6 = $_POST['q6']; 
$q7 = $_POST['q7'];
$q8 = $_POST['q8'];
$q9 = $_POST['q9'];
$q10 = $_POST['q10'];
$q11 = $_POST['q11'];
$q12 = $_POST['q12'];
$q13 = $_POST['q13'];
$q14 = $_POST['q14'];
$q15 = $_POST['q15'];
$q16 = $_POST['q16'];
$q17 = $_POST['q17'];
$q18 = $_POST['q18'];
$q19 = $_POST['q19'];
$q20 = $_POST['q20'];
$q21 = $_POST['q21'];
$q22 = $_POST['q22'];
$q23 = $_POST['q23'];
$q24 = $_POST['q24'];
$q25 = $_POST['q25'];
$q26 = $_POST['q26'];
$q27 = $_POST['q27'];
$q28 = $_POST['q28'];
$q29 = $_POST['q29'];
$q30 = $_POST['q30'];
$q31 = $_POST['q31'];
$q32 = $_POST['q32'];
$q33 = $_POST['q33'];
$q34 = $_POST['q34'];
$q35 = $_POST['q35'];
$q36 = $_POST['q36'];
$q37 = $_POST['q37'];
$q38 = $_POST['q38'];
$q39 = $_POST['q39'];

// Insert quiz responses into the database
$sql = "INSERT INTO user_responses (user_id, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12, q13, q14, q15, q16, q17, q18, q19, q20, q21, q22, q23, q24, q25, q26, q27, q28, q29, q30, q31, q32, q33, q34, q35, q36, q37, q38, q39) 
        VALUES ('$user_id', '$q1', '$q2', '$q3', '$q4', '$q5', '$q6', '$q7', '$q8', '$q9', '$q10', '$q11', '$q12', '$q13', '$q14', '$q15', '$q16', '$q17', '$q18', '$q19', '$q20', '$q21', '$q22', '$q23', '$q24', '$q25', '$q26', '$q27', '$q28', '$q29', '$q30', '$q31', '$q32', '$q33', '$q34', '$q35', '$q36', '$q37', '$q38', '$q39')";

if ($conn->query($sql) === TRUE) {
    // Initialize an array to store sum of responses for each set
    $set_totals = array_fill(1, 22, 0);

    // Retrieve each question's set from `question_set_map`
    $sql = "SELECT career_set, question_number FROM career_set_mapping";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $set_number = $row['career_set'];
            $question_number = $row['question_number'];

            // Get the user response for this question
            $response_query = "SELECT q$question_number FROM user_responses WHERE user_id = '$user_id'";
            $response_result = $conn->query($response_query);

            if ($response_result->num_rows > 0) {
                $response_row = $response_result->fetch_assoc();
                $set_totals[$set_number] += (int)$response_row["q$question_number"];
            }
        }
    }

    // Insert or update the calculated set totals in `user_set_totals` for the user
    foreach ($set_totals as $set_number => $total) {
        $insert_totals = "INSERT INTO user_set_totals (user_id, set_number, total) VALUES ('$user_id', '$set_number', '$total')
                          ON DUPLICATE KEY UPDATE total = '$total'";
        $conn->query($insert_totals);
    }

    // Display success message with HTML and CSS
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Success</title>
        <style>
            /* CSS Styles */
            body {
                font-family: "Arial", sans-serif;
                background: linear-gradient(to right, #FF7E5F, #FFFCF2);
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                overflow: hidden;
            }

            .container {
                perspective: 1000px;
            }

            .card {
                width: 300px;
                height: 400px;
                position: relative;
                transform-style: preserve-3d;
                transition: transform 0.6s;
                cursor: pointer;
            }

            .card:hover {
                transform: rotateY(180deg); 
            }

            .card .front, .card .back {
                position: absolute;
                width: 100%;
                height: 100%;
                backface-visibility: hidden;
                border-radius: 10px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }

            .card .front {
                background: #fff;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: #333;
            }

            .card .back {
                background: #4CAF50;
                color: white;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                transform: rotateY(180deg); 
                padding: 20px;
            }

            .button {
                padding: 10px 20px;
                background-color: #ff4081;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-decoration: none;
                font-size: 18px;
                transition: background-color 0.3s, transform 0.2s;
            }

            .button:hover {
                background-color: #e91e63;
                transform: scale(1.05);
            }

            /* Animation for the success message */
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .message {
                animation: slideIn 0.5s forwards;
            }

            .gif-container {
                text-align: center;
                margin-top: 20px;
            }

        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="front">
                    <h1 class="message">Quiz Response Saved Successfully!</h1>
                    <p class="message">Thank you for participating in the quiz.</p>
                </div>
                <div class="back">
                    <h2 class="message">Awesome!</h2>
                    <p class="message">Click below to check your results:</p>
                    <a href="results.php" class="button">Check Your Results</a>
                </div>
            </div>
        </div>
        
    </body>
    </html>';
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
