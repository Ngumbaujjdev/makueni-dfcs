<?php
include "../../config/config.php";
include "../../libs/App.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../mailer/src/Exception.php';
require '../../mailer/src/PHPMailer.php';
require '../../mailer/src/SMTP.php';

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

if (isset($_POST["email"])) {
    // Random token
    $email = $_POST["email"];
    $length = 50;
    $token = bin2hex(openssl_random_pseudo_bytes($length));

    $app = new App;
    
    // Check if email exists in users table
    $query = "SELECT * FROM users WHERE email='{$email}'";
    $usercount = $app->count($query);

    if ($usercount > 0) {
        // Get user details for personalized email
        $userQuery = "SELECT first_name, last_name, role_id FROM users WHERE email='{$email}'";
        $userData = $app->select_all($userQuery);
        $user = $userData[0]; // Get first (and only) result
        
        // Determine user role for personalized message
        $roleNames = [
            1 => 'Farmer',
            2 => 'SACCO Staff', 
            3 => 'Bank Staff',
            4 => 'Agrovet Staff',
            5 => 'System Administrator'
        ];
        $userRole = $roleNames[$user->role_id] ?? 'User';
        $userName = $user->first_name . ' ' . $user->last_name;

        // Update token in the database - assuming you'll add reset_token column to users table
        $updateQuery = "UPDATE users SET reset_token=:token WHERE email=:email";
        $arr = [
            ":token" => $token,
            ":email" => $email
        ];

        $app->updateToken($updateQuery, $arr);

        // Send email with the password reset link
        try {
            // Gmail SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'makuenidfcs@gmail.com';
            $mail->Password = 'iyko megt qaqp enxc';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('makuenidfcs@gmail.com', 'Makueni Distributed Farmers Cooperative System');
            $mail->addAddress($email, $userName);
            $mail->addReplyTo('makuenidfcs@gmail.com', 'DFCS Support Team');

            // Email subject
            $mail->Subject = 'Password Reset Request - Makueni DFCS';
            $mail->isHTML(true);

            // Load email template and set reset link
            $templateFilePath = __DIR__ . '/email-template.php';
            $resetLink = "http://localhost/dfcs/authentication/forgot-reset?forgot={$token}";

            if (file_exists($templateFilePath)) {
                $emailBody = file_get_contents($templateFilePath);

                // Replace placeholders with actual data
                $emailBody = str_replace('<!--{resetLink}-->', $resetLink, $emailBody);
                $emailBody = str_replace('<!--{userName}-->', $userName, $emailBody);
                $emailBody = str_replace('<!--{userRole}-->', $userRole, $emailBody);
                $emailBody = str_replace('<!--{userEmail}-->', $email, $emailBody);
                
                $mail->Body = $emailBody;

                // Add plain text version as fallback
                $mail->AltBody = "Dear {$userName},\n\nWe received a request to reset your password for the Makueni Digital Financial Credit System.\n\nTo reset your password, please visit: {$resetLink}\n\nIf you didn't request this password reset, you can safely ignore this email.\n\nFor security reasons, this link will expire in 24 hours.\n\nBest regards,\nMakueni DFCS Support Team";

                if ($mail->send()) {
                    echo json_encode(['success' => true, 'message' => 'Reset link has been sent to your email']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Email template not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'This email address is not registered with DFCS']);
    }
}
?>