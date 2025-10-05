<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['account_id'];
$suggestionId = intval($_POST['suggestion_id']);
$voteType = $_POST['vote_type']; // "Upvote" or "Downvote"

// Check if user already voted
$check = $conn->prepare("SELECT * FROM vote WHERE user_id = ? AND suggestion_id = ?");
$check->bind_param("ii", $userId, $suggestionId);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Update existing vote
    $update = $conn->prepare("UPDATE vote SET vote_type = ? WHERE user_id = ? AND suggestion_id = ?");
    $update->bind_param("sii", $voteType, $userId, $suggestionId);
    $update->execute();
} else {
    // Insert new vote
    $insert = $conn->prepare("INSERT INTO vote (suggestion_id, user_id, vote_type) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $suggestionId, $userId, $voteType);
    $insert->execute();
}

// Count new totals
$countUp = $conn->prepare("SELECT COUNT(*) FROM vote WHERE suggestion_id = ? AND vote_type = 'Upvote'");
$countUp->bind_param("i", $suggestionId);
$countUp->execute();
$countUp->bind_result($upvotes);
$countUp->fetch();
$countUp->close();

$countDown = $conn->prepare("SELECT COUNT(*) FROM vote WHERE suggestion_id = ? AND vote_type = 'Downvote'");
$countDown->bind_param("i", $suggestionId);
$countDown->execute();
$countDown->bind_result($downvotes);
$countDown->fetch();
$countDown->close();

// Update suggestion table with new totals
$updateSuggestion = $conn->prepare("UPDATE suggestion SET upvotes = ?, downvotes = ? WHERE suggestion_id = ?");
$updateSuggestion->bind_param("iii", $upvotes, $downvotes, $suggestionId);
$updateSuggestion->execute();

echo json_encode(['upvotes' => $upvotes, 'downvotes' => $downvotes]);
?>
