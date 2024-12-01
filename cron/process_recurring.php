<?php
require_once '../config.php';

// Process recurring transactions
$query = "
    SELECT * FROM recurring_transactions 
    WHERE (last_processed IS NULL OR last_processed < CURRENT_DATE)
    AND (end_date IS NULL OR end_date >= CURRENT_DATE)";
$recurring = $conn->query($query);

while ($transaction = $recurring->fetch_assoc()) {
    // Calculate next processing date based on frequency
    $last_processed = $transaction['last_processed'] ?? $transaction['start_date'];
    $next_date = date('Y-m-d');
    
    // Create transaction
    $insert_query = "
        INSERT INTO transactions (account_id, type, category, amount, description, transaction_date) 
        VALUES (
            " . $transaction['account_id'] . ",
            '" . $transaction['type'] . "',
            '" . $transaction['category'] . "',
            " . $transaction['amount'] . ",
            'Recurring: " . $transaction['description'] . "',
            '" . $next_date . "'
        )";
    
    if ($conn->query($insert_query)) {
        // Update account balance
        $balance_modifier = ($transaction['type'] === 'expense') ? -$transaction['amount'] : $transaction['amount'];
        $update_balance = "
            UPDATE accounts 
            SET balance = balance + $balance_modifier 
            WHERE id = " . $transaction['account_id'];
        $conn->query($update_balance);
        
        // Update last processed date
        $update_query = "
            UPDATE recurring_transactions 
            SET last_processed = '" . $next_date . "' 
            WHERE id = " . $transaction['id'];
        $conn->query($update_query);
    }
}

echo "Recurring transactions processed successfully\n"; 