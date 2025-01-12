<?php
session_start(); // Start a session
$conn = new mysqli("localhost", "root", "", "voting_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle different actions (admin or voter functionalities)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['admin_action'])) {
        // Admin: Add election or candidate
        $action = $_POST['admin_action'];
        if ($action == 'add_election') {
            $election_name = $_POST['election_name'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $conn->query("INSERT INTO elections (election_name, start_date, end_date) VALUES ('$election_name', '$start_date', '$end_date')");
            echo "Election added successfully!";
        } elseif ($action == 'add_candidate') {
            $election_id = $_POST['election_id'];
            $candidate_name = $_POST['candidate_name'];
            $conn->query("INSERT INTO candidates (election_id, candidate_name) VALUES ('$election_id', '$candidate_name')");
            echo "Candidate added successfully!";
        }
        
    } elseif (isset($_POST['vote'])) {
        // Voter: Cast a vote
        $election_id = $_POST['election_id'];
        $candidate_id = $_POST['candidate_id'];
        $user_id = $_SESSION['user_id'];

        // Check if user already voted
        $check_vote = $conn->query("SELECT has_voted FROM users WHERE user_id = $user_id");
        $has_voted = $check_vote->fetch_assoc()['has_voted'];

        if ($has_voted == 1) {
            echo "You have already voted!";
        } else {
            // Update candidate vote count
            $conn->query("UPDATE candidates SET votes = votes + 1 WHERE candidate_id = $candidate_id");
            $conn->query("UPDATE users SET has_voted = 1 WHERE user_id = $user_id");
            echo "Vote cast successfully!";
        }   
    }
}
// Display results dynamically
if (isset($_GET['results'])) {
    $election_id = $_GET['results'];
    $results = $conn->query("SELECT candidate_name, votes FROM candidates WHERE election_id = $election_id");
    echo "<h3>Election Results</h3>";
    while ($row = $results->fetch_assoc()) {
        echo $row['candidate_name'] . ": " . $row['votes'] . " votes<br>";
    }
    exit();
}
?>
?>