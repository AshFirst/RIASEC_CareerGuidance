<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    die("User not authenticated.");
}

$user_id = $_SESSION['id']; // Retrieve the user ID from session

// Connect to the database using MySQLi with error handling
$conn = new mysqli('localhost', 'root', '', 'logs');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use a prepared statement to fetch the set totals
$totals_query = $conn->prepare("SELECT set_number, total FROM user_set_totals WHERE user_id = ?");
$totals_query->bind_param("i", $user_id);
$totals_query->execute();
$result = $totals_query->get_result();

$set_totals = [];
while ($row = $result->fetch_assoc()) {
    $set_totals[$row['set_number']] = $row['total'];
}

$totals_query->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .result-container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .chart-container {
            width: 100%;
            height: 400px;
            margin-top: 30px;
        }
        h2 {
            text-align: center;
            color: #444;
        }
        .explanation-container {
            margin-top: 30px;
        }
        .accordion {
            background-color: #eee;
            color: #444;
            cursor: pointer;
            padding: 15px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 18px;
            transition: 0.4s;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .accordion.active, .accordion:hover {
            background-color: #ccc;
        }
        .panel {
            padding: 0 15px;
            display: none;
            background-color: white;
            overflow: hidden;
            font-size: 16px;
        }
        .note {
            color: red;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <h2>Quiz Results for User <?php echo htmlspecialchars($user_id); ?></h2>
        
        <div class="note">Results should be viewed with a parent or guardian for support and guidance.</div>

        <div class="chart-container">
            <canvas id="resultsChart"></canvas>
        </div>
        
        <div class="explanation-container">
            <h3 style="text-align: center; color: #444; margin-top: 30px;">Detailed Explanation of Each Set</h3>
            <?php
            // Descriptions for each set
            $set_descriptions = [
                1 => "Computer Science and Information Technology - A journey into the world of computers, algorithms, and coding.",
                2 => "Engineering Science and Technology - Learn about mechanical, electrical, and civil engineering principles.",
                3 => "Biology, Biotechnology, and Life Science - Explore genetics, ecosystems, and the science of living organisms.",
                4 => "Physics and Earth Science - Dive deep into the laws of nature and the science of our planet.",
                5 => "Mathematics, Statistics, and Applied Data - Sharpen your analytical and problem-solving skills.",
                6 => "Chemistry - Understand the molecular makeup of the world and how substances interact.",
                7 => "Performing Arts, Fashion, and Design - Express creativity through arts, design, and fashion.",
                8 => "Social Sciences and Humanities - Delve into sociology, psychology, and understanding human behavior.",
                9 => "Languages and Literature - Enhance language proficiency and explore classic and modern literature.",
                10 => "Constructions and Architecture - Focus on designing buildings and structural engineering.",
                11 => "Agriculture and Food Science Technology - Learn about sustainable agriculture and food production.",
                12 => "Business and Management - Develop skills in management, entrepreneurship, and marketing.",
                13 => "Accounting, Banking, and Insurance - Master finance, investments, and managing risks.",
                14 => "Public Policy and Law - Understand the intricacies of governance, law, and legal frameworks.",
                15 => "Mass Media, Entertainment, and Journalism - Dive into media studies, content creation, and reporting.",
                16 => "Creating Arts, Designs, and Cinematography - Focus on film-making, photography, and creative arts.",
                17 => "Architecture and Structural Engineering - Advanced techniques in building design and structures.",
                18 => "Merchant Navy and Air - Explore careers in maritime navigation and aviation.",
                19 => "Para Medicals - Learn about healthcare support and medical assistance.",
                20 => "Biotechnology and Environmental Science - Innovations in biology to solve environmental issues.",
                21 => "Applied Mathematics and Advanced Data Analysis - Special focus on data interpretation and statistical models.",
                22 => "Marine Engineering and Aeronautics - Dive into the mechanics of ships, planes, and other vessels."
            ];

            // Display the descriptions using accordions
            foreach ($set_descriptions as $set => $description) {
                echo '<button class="accordion">Set ' . $set . ': ' . $description . '</button>';
                echo '<div class="panel"><p>' . $description . '</p></div>';
            }
            ?>
        </div>

        <script>
            // Prepare data for Chart.js
            const setNumbers = <?php echo json_encode(array_keys($set_totals)); ?>;
            const setTotals = <?php echo json_encode(array_values($set_totals)); ?>;

            // Create the chart using Chart.js
            const ctx = document.getElementById('resultsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: setNumbers,
                    datasets: [{
                        label: 'Total Score per Set',
                        data: setTotals,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // Accordion functionality for explanation
            const accordions = document.getElementsByClassName("accordion");
            for (let i = 0; i < accordions.length; i++) {
                accordions[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    const panel = this.nextElementSibling;
                    if (panel.style.display === "block") {
                        panel.style.display = "none";
                    } else {
                        panel.style.display = "block";
                    }
                });
            }
        </script>
    </div>
</body>
</html>
