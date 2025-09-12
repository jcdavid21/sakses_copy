<?php 
    function system_logs($conn, $user_id, $action, $description) {
        date_default_timezone_set('Asia/Manila');
        $current_date = date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, description, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $action, $description, $current_date);
        $stmt->execute();
        $stmt->close();
    }

?>