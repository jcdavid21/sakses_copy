<?php 
    require_once "./config_normal.php";
    require_once "./system_logs.php";
    header('Content-Type: application/json');

    session_start();

    if(isset($_POST["username"]) && isset($_POST["password"]))
    {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $sql = "SELECT id, password_hash, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1)
        {
            $row = $result->fetch_assoc();
            if(password_verify($password, $row["password_hash"]))
            {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["role"] = $row["role"];

                // Log the login activity
                system_logs($conn, $row["id"], 'Login', 'User logged in: ' . $username);

                echo json_encode(["success" => true, "message" => "Login successful"]);
                exit();
            }
        }

        // Log the failed login attempt
        system_logs($conn, 0, 'Failed Login', 'Invalid username or password for user: ' . $username);

        echo json_encode(["success" => false, "message" => "Invalid username or password", 'username' => $username]);
        exit();
    }
    else
    {
        echo json_encode(["success" => false, "message" => "Username and password are required"]);
        exit();
    }   

?>