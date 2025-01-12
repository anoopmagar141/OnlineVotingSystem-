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
?>