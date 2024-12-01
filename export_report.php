<?php
require_once 'config.php';
checkAuth();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="financial_report_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Set default date range if not provided
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Write headers
fputcsv($output, ['Financial Report', '', '']);
fputcsv($output, ['Period:', $start_date, 'to', $end_date]);
fputcsv($output, ['']);

// Export Income Summary
$income_query = "
    SELECT 
        t.category,
        SUM(t.amount) as total,
        COUNT(*) as count
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.type = 'income'
    AND t.transaction_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY t.category
    ORDER BY total DESC";
$income = $conn->query($income_query);

fputcsv($output, ['Income Summary']);
fputcsv($output, ['Category', 'Amount', 'Number of Transactions']);
while ($row = $income->fetch_assoc()) {
    fputcsv($output, [
        ucfirst($row['category']),
        number_format($row['total'], 2),
        $row['count']
    ]);
}
fputcsv($output, ['']);

// Export Expense Summary
$expenses_query = "
    SELECT 
        t.category,
        SUM(t.amount) as total,
        COUNT(*) as count
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.type = 'expense'
    AND t.transaction_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY t.category
    ORDER BY total DESC";
$expenses = $conn->query($expenses_query);

fputcsv($output, ['Expense Summary']);
fputcsv($output, ['Category', 'Amount', 'Number of Transactions']);
while ($row = $expenses->fetch_assoc()) {
    fputcsv($output, [
        ucfirst($row['category']),
        number_format($row['total'], 2),
        $row['count']
    ]);
}
fputcsv($output, ['']);

// Export Detailed Transactions
$transactions_query = "
    SELECT 
        t.transaction_date,
        a.account_name,
        t.type,
        t.category,
        t.description,
        t.amount
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.transaction_date BETWEEN '$start_date' AND '$end_date'
    ORDER BY t.transaction_date DESC";
$transactions = $conn->query($transactions_query);

fputcsv($output, ['Detailed Transactions']);
fputcsv($output, ['Date', 'Account', 'Type', 'Category', 'Description', 'Amount']);
while ($row = $transactions->fetch_assoc()) {
    fputcsv($output, [
        $row['transaction_date'],
        $row['account_name'],
        ucfirst($row['type']),
        ucfirst($row['category']),
        $row['description'],
        ($row['type'] === 'expense' ? '-' : '') . number_format($row['amount'], 2)
    ]);
}

fclose($output);
exit();
?> 