<?php
if (isset($_GET['hash'])) {
    $hash = $_GET['hash'];
    $hashFile = 'hash/' . $hash . '.txt';
    
    // Vérifier si le fichier existe
    if (file_exists($hashFile)) {
	// Lire l'email depuis le fichier
	$email = file_get_contents($hashFile);
	
	
	$url = "http://localhost:3000/token?email=$email";
	
	// Initialize cURL
	$ch = curl_init($url);
	
	// Configure cURL options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// Execute the request
	$response = curl_exec($ch);
	
	// Check for errors
	if (curl_errno($ch)) {
	    echo "Error: " . curl_error($ch);
	    exit;
	}
	
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$result = json_decode($response, true);
	
	curl_close($ch);
	
	if ($httpCode === 200 && isset($result['success']) && $result['success']) {
	    echo json_encode(['success' => true, 'access_token' => $result['access_token']]);
	} else {
	    echo json_encode(['success' => false, 'error' => 'Token not found.']);
	}
	
    } else {
	echo json_encode(['success' => false, 'error' => 'Hash not found.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No hash provided.']);
}
