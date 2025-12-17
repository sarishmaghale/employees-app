<?php

namespace App\Repositories;

use App\Models\Otp;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailRepository
{
    private function setUpMailer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_USERNAME');
        $mail->Password = env('MAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = env('MAIL_PORT');
        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $mail->isHTML(true);
        return $mail;
    }

    public function sendTaskAssignedMail(
        $toEmail = '',
        $subject = '',
        $taskInfo = [],
    ) {
        try {
            $mail = $this->setUpMailer();

            $mail->addAddress($toEmail);
            $mail->isHTML(true);

            $mail->Subject = $subject;
            $mail->Body = "Assigned Task: " . $taskInfo['title'] . " . Deadline is " . $taskInfo['end'];
            return $mail->send();
        } catch (Exception $e) {;
            return false;
        }
    }

    public function sendAccountRelatedMail(
        $toEmail = '',
        $subject = '',
        $details = ''
    ) {
        try {
            $mail = $this->setUpMailer();
            $mail->addAddress($toEmail);
            $mail->Subject = $subject;
            $mail->Body = 'DO NOT SHARE! . Email: '
                . $details['email'] . ' <br> Password : 12345
                <br> 
                You can edit your details in profile section 
            after loggig to the systen with given credentials.';
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function createOtpForLogIn($email)
    {
        $otpCode = rand(100000, 999999);
        Otp::where('email', $email)->delete();
        $created = Otp::create([
            'email' => $email,
            'code' => $otpCode,
            'expres_at' => now()->addMinutes(10),
            'created_at' => now()
        ]);
        if ($created) return $otpCode;
        else return null;
    }

    public function verifyOtp($email, $otpCode)
    {
        $validRequest = Otp::where('email', $email)->where('code', $otpCode)
            ->where('isUsed', false)->where('expires_at', '>', now())
            ->first();
        if (!$validRequest) {
            return false;
        }
        $validRequest->update([
            'isUsed' => true
        ]);
        return true;
    }

    public function sendOtpMail($email, $otpCode)
    {
        try {
            $mail = $this->setUpMailer();
            $mail->addAddress($email);
            $mail->Subject = 'Login OTP';
            $mail->Body = 'DO NOT SHARE! Your OTP is: ' . $otpCode;
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}
