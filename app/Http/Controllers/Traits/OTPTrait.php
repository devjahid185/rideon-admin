<?php

namespace App\Http\Controllers\Traits;

use App\Models\AppUserOtp;
use Carbon\Carbon;
use DB;

trait OTPTrait
{
    public function generateOtp($phoneNumber, $countryCode)
    {

        DB::table('app_user_otps')
            ->where('phone', $phoneNumber)
            ->where('country_code', $countryCode)
            ->delete();

        $otp = $this->createOTP();

        // Calculate the expiration time (e.g., 10 minutes from now)
        $expiresAt = Carbon::now()->addMinutes(100);

        // $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . "/OTP_insert.txt", "w") or die("Unable to open file!");

        // $txt = "phoneNumber = " . $phoneNumber . "\n";
        // fwrite($myfile, $txt);
        // $txt = "countryCode = " . $countryCode . "\n";
        // fwrite($myfile, $txt);

        // fclose($myfile);

        $otpEntry = new AppUserOtp;
        $otpEntry->phone = trim($phoneNumber);
        $otpEntry->country_code = trim($countryCode);
        $otpEntry->otp_code = trim($otp);
        $otpEntry->created_at = Carbon::now();
        $otpEntry->expires_at = $expiresAt;
        $otpEntry->save();

        return $otpEntry->otp_code;
    }

    public function validateOtpFromDB($phoneNumber, $countryCode, $inputOtp)
    {
        // Fetch the latest OTP record for this phone number and country code
        $otpRecord = DB::table('app_user_otps')
            ->where('phone', $phoneNumber)
            ->where('country_code', $countryCode)
            ->orderByDesc('created_at')
            ->first();

        if (! $otpRecord) {
            return [
                'status' => 'failed',
                'message' => 'No OTP record found for this phone number.',
            ];
        }

        // Check if the OTP has expired
        $currentTime = Carbon::now();
        $expiresAt = Carbon::parse($otpRecord->expires_at);

        if ($currentTime->greaterThanOrEqualTo($expiresAt)) {
            return [
                'status' => 'failed',
                'message' => 'The OTP has expired.',
            ];
        }

        // Check if the provided OTP matches the one in the record
        if ($otpRecord->otp_code === $inputOtp) {
            DB::table('app_user_otps')
                ->where('id', $otpRecord->id)
                ->delete();

            return [
                'status' => 'success',
                'message' => 'OTP verified successfully.',
            ];
        } else {
            return [
                'status' => 'failed',
                'message' => 'Incorrect OTP.',
            ];
        }
    }

    public function createOTP()
    {
        $chars = '0123456789';
        $otp = mt_rand(1, 9);

        for ($i = 1; $i < 6; $i++) {
            $otp .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $otp;
    }

    public function createPickDropOTP()
    {
        $chars = '0123456789';
        $otp = mt_rand(1, 9);

        for ($i = 1; $i < 4; $i++) {
            $otp .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $otp;
    }
}
