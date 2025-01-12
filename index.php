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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online voting System</title>
</head>
<body>
<h1>Online Voting System</h1>

<!-- Admin Panel -->
<h2>Admin Panel</h2>
<form method="POST">
    <h3>Add Election</h3>
    <input type="text" name="election_name" placeholder="Election Name" required>
    <input type="date" name="start_date" required>
    <input type="date" name="end_date" required>
    <input type="hidden" name="admin_action" value="add_election">
    <button type="submit">Add Election</button>
</form>

<form method="POST">
    <h3>Add Candidate</h3>
    <select name="election_id" required>
        <option value="" disabled selected>Select Election</option>
        <?php
        $elections = $conn->query("SELECT * FROM elections");
        while ($row = $elections->fetch_assoc()) {
            echo "<option value='" . $row['election_id'] . "'>" . $row['election_name'] . "</option>";
        }
        ?>
    </select>
    <input type="text" name="candidate_name" placeholder="Candidate Name" required>
    <input type="hidden" name="admin_action" value="add_candidate">
    <button type="submit">Add Candidate</button>
</form>
<!-- Voter Panel -->
<h2>Voter Panel</h2>
    <form method="POST">
        <h3>Cast Your Vote</h3>
        <select name="election_id" required>
            <option value="" disabled selected>Select Election</option>
            <?php
            $elections = $conn->query("SELECT * FROM elections");
            while ($row = $elections->fetch_assoc()) {
                echo "<option value='" . $row['election_id'] . "'>" . $row['election_name'] . "</option>";
            }
            ?>
        </select>
        <select name="candidate_id" required>
            <option value="" disabled selected>Select Candidate</option>
            <?php
            $candidates = $conn->query("SELECT * FROM candidates");
            while ($row = $candidates->fetch_assoc()) {
                echo "<option value='" . $row['candidate_id'] . "'>" . $row['candidate_name'] . "</option>";
            }
            ?>
        </select>
        <button type="submit" name="vote">Vote</button>
    </form>

    <!-- View Results -->
    <h2>View Results</h2>
    <form method="GET">
        <select name="results" required>
            <option value="" disabled selected>Select Election</option>
            <?php
            $elections = $conn->query("SELECT * FROM elections");
            while ($row = $elections->fetch_assoc()) {
                echo "<option value='" . $row['election_id'] . "'>" . $row['election_name'] . "</option>";
            }
            ?>
        </select>
        <button type="submit">View Results</button>
    </form>
</body>
</html>