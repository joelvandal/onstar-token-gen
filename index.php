<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnStar Authentication Token Generator</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="text-center text-primary">OnStar Token Generator</h1>
                    <p class="text-center text-muted">
                        This system generates an authentication token for OnStar services. No sensitive information, such as passwords, is stored on this system. Your data is securely transmitted to the OnStar server for authentication.
                    </p>

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        if (isset($_POST['username'], $_POST['password']) && empty($_POST['code'])) {
                            // Step 1: Authenticate user
                            $url = "http://localhost:3000/auth";
                            $data = [
                                "email" => $_POST['username'],
                                "password" => $_POST['password'],
                                "uuid" => "device-id-placeholder"
                            ];

                            // Initialize cURL
                            $ch = curl_init($url);

                            // Configure cURL options
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                            // Execute the request
                            $response = curl_exec($ch);

                            // Check for errors
                            if (curl_errno($ch)) {
                                echo '<div class="alert alert-danger">Error: ' . curl_error($ch) . '</div>';
                                exit;
                            }

                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $result = json_decode($response, true);

                            curl_close($ch);

                            if ($httpCode === 200 && isset($result['success']) && $result['success']) {
                                $_SESSION['username'] = $_POST['username'];
                                $_SESSION['password'] = $_POST['password'];
                                echo '<div class="alert alert-success">Authentication successful! Please enter your verification code.</div>';
                                echo '<form method="POST" class="mt-3">';
                                echo '<div class="mb-3">';
                                echo '<label for="code" class="form-label">Verification Code</label>';
                                echo '<input type="text" id="code" name="code" class="form-control" placeholder="Enter verification code" required>';
                                echo '</div>';
                                echo '<button type="submit" class="btn btn-primary w-100">Verify</button>';
                                echo '</form>';
                            } else {
                                echo '<div class="alert alert-danger">Authentication failed. Please try again.</div>';
                            }
                        } elseif (isset($_POST['code'], $_SESSION['username'], $_SESSION['password'])) {
                            // Step 2: Verify code
                            $url = "http://localhost:3000/verify";
                            $data = [
                                "email" => $_SESSION['username'],
                                "code" => $_POST['code']
                            ];

                            // Initialize cURL
                            $ch = curl_init($url);

                            // Configure cURL options
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                            // Execute the request
                            $response = curl_exec($ch);

                            // Check for errors
                            if (curl_errno($ch)) {
                                echo '<div class="alert alert-danger">Error: ' . curl_error($ch) . '</div>';
                                exit;
                            }

                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $result = json_decode($response, true);

                            curl_close($ch);

                            if ($httpCode === 200 && isset($result['success']) && $result['success']) {
                                $hash = hash('sha256', $_SESSION['username'] . $_SESSION['password'] . $_POST['code']);
                                $url = '/token/' . $hash;

                                // Save email in a file specific to this hash
                                $hashFile = 'hash/' . $hash . '.txt';
                                file_put_contents($hashFile, $_SESSION['username']);

                                echo '<div class="alert alert-success">Verification successful!</div>';
                                echo '<p class="text-center">Your token URL:</p>';
                                echo '<p class="text-center"><a href="' . $url . '" class="btn btn-success" target="_blank">' . $url . '</a></p>';

                                // Optionally clear session data
                                session_destroy();
                            } else {
                                echo '<div class="alert alert-danger">Verification failed. Please try again.</div>';
                            }
                        }
                    } else {
                        // Display login form
                        echo '<form method="POST" class="mt-3">';
                        echo '<div class="mb-3">';
                        echo '<label for="username" class="form-label">Username</label>';
                        echo '<input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>';
                        echo '</div>';
                        echo '<div class="mb-3">';
                        echo '<label for="password" class="form-label">Password</label>';
                        echo '<input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>';
                        echo '</div>';
                        echo '<button type="submit" class="btn btn-primary w-100">Login</button>';
                        echo '</form>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
