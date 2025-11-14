<?php
require_once "../backend/config_normal.php";



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="../styles/general.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        body {
            margin-top: 0 !important;
            background: rgb(250, 250, 250);
        }

        .center {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            border: 1px solid rgba(170, 170, 170, 1);
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 450px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(200, 200, 200, 1);
            padding-bottom: 6px;
        }

        .header h2 {
            margin: 0;
            font-weight: 600;
            color: rgb(55, 55, 55);
            font-size: 28px;
        }

        .header p {
            margin: 0;
            font-weight: 400;
            color: rgb(120, 120, 120);
            font-size: 14px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: rgb(80, 80, 80);
        }

        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid rgba(180, 180, 180, 1);
            border-radius: 4px;
            font-size: 14px;
        }

        .invalid {
            color: red;
            padding: 6px 0;
            transition: all 0.3s ease-in-out;
        }

        form button {
            width: 100%;
            padding: 10px;
            background: rgb(33, 150, 243);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background: rgb(25, 118, 210);
        }
    </style>
</head>

<body>
    <div class="center">
        <form action="" id="formLogin">
            <div class="header">
                <h2>Log in</h2>
                <p>SAKSES</p>
            </div>
            <label for="username">Username:</label>
            <input type="text" placeholder="Enter your username" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" placeholder="Enter your password" name="password" id="password" required>
            <div class="response">
                <!-- Response message will be displayed here -->
            </div>
            <button type="submit">Log in</button>
        </form>
    </div>

    <script>
        let response = document.querySelector('.response');
        let formData = new FormData();
        let form = document.getElementById('formLogin');
        const username = document.getElementById('username');
        const password = document.getElementById('password');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (username.value.trim() === '' || password.value.trim() === '') {
                response.innerHTML = `<p class="invalid">Please fill in all fields.</p>`;
                setTimeout(() => {
                    response.innerHTML = '';
                }, 3000);
                return;
            }

            formData.append('username', username.value);
            formData.append('password', password.value);

            $.ajax({
                url: "../backend/login.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "./index.php";
                        });
                    } else {
                        response.innerHTML = `<p class="invalid">${data.message}</p>`;
                        response.innerHTML += `<p class="invalid">Username: ${data.username}</p>`;
                        setTimeout(() => {
                            response.innerHTML = '';
                        }, 3000);
                    }
                },
                error: function() {
                    response.innerHTML = `<p class="invalid">An error occurred. Please try again.</p>`;
                }
            })
        })
    </script>
</body>

</html>