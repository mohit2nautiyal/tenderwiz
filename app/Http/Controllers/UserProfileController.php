<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    /**
     * Initialize PHPMailer instance
     */
    private function initializeMailer(): PHPMailer
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = 'sandbox.smtp.mailtrap.io';
        $mailer->SMTPAuth = true;
        $mailer->Port = 2525;
        $mailer->Username = '09fbea9e337559';
        $mailer->Password = '42609987da39c5';
        $mailer->setFrom('no-reply@yourapp.com', 'Your App Name');
        return $mailer;
    }


//     private function initializeMailer(): PHPMailer
// {
//     try {
//         $mailer = new PHPMailer(true);
//         $mailer->SMTPDebug = 2; // Enable for debugging
//         $mailer->Debugoutput = function($str, $level) {
//             Log::debug("PHPMailer Debug level $level: $str");
//         };
//         $mailer->isSMTP();
//         $mailer->Host = 'smtp.gmail.com';
//         $mailer->SMTPAuth = true;
//         $mailer->Port = 587;
//         $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mailer->Username = 'your-email@gmail.com'; // Your Gmail address
//         $mailer->Password = 'your-app-password'; // Gmail App Password
//         $mailer->setFrom('your-email@gmail.com', 'Your App Name');
//         $mailer->CharSet = PHPMailer::CHARSET_UTF8;
//         return $mailer;
//     } catch (Exception $e) {
//         Log::error('Failed to initialize PHPMailer: ' . $e->getMessage());
//         throw new Exception('Email service configuration failed.');
//     }
// }

    /**
     * Display the user profile edit form
     */
    public function edit()
    {
        return view('profile.details', ['user' => Auth::user()]);
    }

    /**
     * Update user profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        try {
            DB::beginTransaction();
            
            $data = ['name' => $request->name];

            if ($request->email !== $user->email) {
                $otp = Str::random(6);
                $data['pending_email'] = $request->email;
                $data['pending_email_verified_at'] = null;
                $data['email_otp'] = Hash::make($otp);
                $data['email_otp_expires_at'] = now()->addMinutes(15);

                $user->update($data);
                
                $this->sendOtpEmail($user, $otp);

                DB::commit();
                return redirect()->route('profile.edit')
                    ->with('showOtpModal', true)
                    ->with('success', 'Please check your email for the OTP to verify your new email address.');
            }

            $user->update($data);
            DB::commit();
            
            return redirect()->route('profile.edit')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile update failed: ' . $e->getMessage());
            return redirect()->route('profile.edit')
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Password update failed: ' . $e->getMessage());
            return redirect()->route('profile.edit')
                ->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Send OTP email for verification
     */
    private function sendOtpEmail(User $user, string $otp): void
    {
        try {
            $mailer = $this->initializeMailer();
            $mailer->addAddress($user->pending_email);
            $mailer->Subject = 'Verify Your Email Address';
            $mailer->Body = "Your OTP for email verification is: {$otp}\n\nThis OTP is valid for 15 minutes.";
            $mailer->send();
        } catch (Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            throw new \Exception('Failed to send verification email.');
        }
    }

    /**
     * Verify OTP for email
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        try {
            if (!$user->pending_email || !$user->email_otp || now()->greaterThan($user->email_otp_expires_at)) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Invalid or expired OTP.');
            }

            if (!Hash::check($request->otp, $user->email_otp)) {
                return redirect()->route('profile.edit')
                    ->with('showOtpModal', true)
                    ->with('error', 'Invalid OTP. Please try again.');
            }

            DB::beginTransaction();
            $user->update([
                'email' => $user->pending_email,
                'email_verified_at' => now(),
                'pending_email' => null,
                'email_otp' => null,
                'email_otp_expires_at' => null,
            ]);
            DB::commit();

            return redirect()->route('profile.edit')
                ->with('success', 'Email verified successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OTP verification failed: ' . $e->getMessage());
            return redirect()->route('profile.edit')
                ->with('showOtpModal', true)
                ->with('error', 'Failed to verify OTP. Please try again.');
        }
    }

    /**
     * Resend OTP email
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user->pending_email || $user->pending_email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'No pending email to verify.'
            ], 400);
        }

        try {
            $otp = Str::random(6);
            $user->update([
                'email_otp' => Hash::make($otp),
                'email_otp_expires_at' => now()->addMinutes(15),
            ]);

            $this->sendOtpEmail($user, $otp);

            return response()->json([
                'success' => true,
                'message' => 'Verification OTP resent successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Resend OTP failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Display user details form
     */
    public function details()
    {
        return view('profile.details', ['user' => Auth::user()]);
    }

    /**
     * Store user details
     */
    public function storeDetails(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'account_holder_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'mobile_number' => ['required', 'string', 'max:15', 'regex:/^[0-9]{10,15}$/'],
            'reg_address_line1' => ['required', 'string', 'max:255'],
            'reg_address_line2' => ['nullable', 'string', 'max:255'],
            'reg_district' => ['required', 'string', 'max:100'],
            'reg_state' => ['required', 'string', 'max:100'],
            'reg_country' => ['required', 'string', 'max:100'],
            'reg_pincode' => ['required', 'string', 'max:10', 'regex:/^[0-9]{5,10}$/'],
            'same_as_registered' => ['sometimes', 'boolean'],
            'bill_address_line1' => ['required_if:same_as_registered,0', 'string', 'max:255'],
            'bill_address_line2' => ['nullable', 'string', 'max:255'],
            'bill_district' => ['required_if:same_as_registered,0', 'string', 'max:100'],
            'bill_state' => ['required_if:same_as_registered,0', 'string', 'max:100'],
            'bill_country' => ['required_if:same_as_registered,0', 'string', 'max:100'],
            'bill_pincode' => ['required_if:same_as_registered,0', 'string', 'max:10', 'regex:/^[0-9]{5,10}$/'],
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'account_holder_name' => $validated['account_holder_name'],
                'company_name' => $validated['company_name'],
                'mobile_number' => $validated['mobile_number'],
                'reg_address_line1' => $validated['reg_address_line1'],
                'reg_address_line2' => $validated['reg_address_line2'],
                'reg_district' => $validated['reg_district'],
                'reg_state' => $validated['reg_state'],
                'reg_country' => $validated['reg_country'],
                'reg_pincode' => $validated['reg_pincode'],
            ];

            if ($request->input('same_as_registered', 0)) {
                $data['bill_address_line1'] = $validated['reg_address_line1'];
                $data['bill_address_line2'] = $validated['reg_address_line2'];
                $data['bill_district'] = $validated['reg_district'];
                $data['bill_state'] = $validated['reg_state'];
                $data['bill_country'] = $validated['reg_country'];
                $data['bill_pincode'] = $validated['reg_pincode'];
            } else {
                $data['bill_address_line1'] = $validated['bill_address_line1'];
                $data['bill_address_line2'] = $validated['bill_address_line2'];
                $data['bill_district'] = $validated['bill_district'];
                $data['bill_state'] = $validated['bill_state'];
                $data['bill_country'] = $validated['bill_country'];
                $data['bill_pincode'] = $validated['bill_pincode'];
            }

            if ($request->email !== $user->email) {
                $otp = Str::random(6);
                $data['pending_email'] = $validated['email'];
                $data['pending_email_verified_at'] = null;
                $data['email_otp'] = Hash::make($otp);
                $data['email_otp_expires_at'] = now()->addMinutes(15);

                $user->update($data);
                $this->sendOtpEmail($user, $otp);

                DB::commit();
                return redirect()->route('profile.details')
                    ->with('showOtpModal', true)
                    ->with('success', 'Please check your email for the OTP to verify your new email address.');
            }

            $data['email'] = $validated['email'];
            $user->update($data);
            DB::commit();

            return redirect()->route('profile.details')
                ->with('success', 'Account details saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Details update failed: ' . $e->getMessage());
            return redirect()->route('profile.details')
                ->with('error', 'Failed to save account details. Please try again.');
        }
    }
}