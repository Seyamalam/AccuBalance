<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    $sql = "INSERT INTO transactions (description, amount, date) VALUES ('$description', '$amount', '$date')";
    if ($conn->query($sql) === TRUE) {
        header('Location: transactions.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold">Add Transaction</h1>
        <form action="add_transaction.php" method="POST" class="bg-white p-4 rounded shadow-md">
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                <input type="text" id="description" name="description" class="w-full border px-4 py-2" required>
            </div>
            <div class="mb-4">
                <label for="amount" class="block text-gray-700">Amount</label>
                <input type="number" id="amount" name="amount" class="w-full border px-4 py-2" required>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700">Date</label>
                <input type="date" id="date" name="date" class="w-full border px-4 py-2" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Transaction</button>
        </form>
    </div>
</body>
</html>
