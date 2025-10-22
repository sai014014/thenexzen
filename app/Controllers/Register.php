<?php

namespace App\Controllers;

use App\Models\RegisterModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

/**
 * Class Register
 * Handles user registration and onboarding steps, including OTP verification, address update, and password setup.
 */
class Register extends BaseController
{
    /**
     * Constructor
     * Initializes the RegisterModel instance.
     */
    private $registrationModel;
    public function __construct()
    {
        $this->registrationModel = new RegisterModel();
    }
    /**
     * Displays the registration view.
     *
     * @return ResponseInterface Renders the 'Register' view.
     */
    public function index(): ResponseInterface
    {
        try {
            // Render the view and send it in the response body
            $viewContent = view('Register');
            return $this->response->setBody($viewContent);
        } catch (Exception $e) {
            // Log the exception with a clear context
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return $this->response->setStatusCode(500, 'An error occurred while loading the registration view.');
        }
    }

    /**
     * Handle the registration process based on the current step (OTP, address, password).
     *
     * @return ResponseInterface JSON response containing the result of the operation.
     */
    public function saveRegistration(): ResponseInterface
    {
        $currentStep = (int)$this->request->getPost('currentStep');
        $email = $this->request->getPost('email');
        $responseData = ['success' => false];

        try {
            $existingUserData = $this->registrationModel->getCustomerDetails(
                ['email_address' => $email],
                'email_address, onboarding_status, mobile_number, agency_name, owner_manager_name'
            );

            if (!empty($existingUserData)) {
                $existingUserData = $existingUserData[0];
            }

            // Determine onboarding step for existing users
            if ($currentStep == 1 && !empty($existingUserData) && !in_array($existingUserData['onboarding_status'], [0, 1])) {
                $currentStep = 5; // Skip to a later step if the user is already onboarded
            }

            // Handle the current step
            switch ($currentStep) {
                case 1:
                    return $this->handleStep1($existingUserData, $email, $responseData);
                case 3:
                    return $this->handleStep2($existingUserData, $email, $responseData);
                case 4:
                    return $this->handleStep3($existingUserData, $email, $responseData);
                default:
                    return $this->handleExistingUser($existingUserData, $responseData);
            }
        } catch (Exception $e) {
            $responseData['message'] = 'An error occurred during registration: ' . $e->getMessage();
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return $this->response->setJSON($responseData);
        }
    }

    /**
     * Handle Step 1: OTP sending process for new users.
     *
     * @param array|null $existingUserData User data from the database, if any.
     * @param string $email User's email address.
     * @param array &$responseData Response data to be returned as JSON.
     * @return ResponseInterface JSON response containing the result of the OTP process.
     */
    private function handleStep1(?array $existingUserData, string $email, array &$responseData): ResponseInterface
    {
        try {
            $otp = rand(100000, 999999);

            // Fetch business_id and business_key directly from stored procedure
            $generated = $this->registrationModel->getGeneratedBusinessIdentifiers();
            $business_id = $generated['business_id'];
            $business_key = $generated['business_key'];
            if (empty($business_id) || empty($business_key)) {
                $responseData['message'] = 'Failed to generate business identifiers. Please try again later.';
                return $this->response->setJSON($responseData);
            }

            $commonData = [
                'email_address' => $email,
                'mobile_number' => $this->request->getPost('mobile'),
                'otp' => $otp,
                'agency_name' => $this->request->getPost('agencyName'),
                'owner_manager_name' => $this->request->getPost('ownerName'),
                'business_id' => $business_id,
                'business_key' => $business_key,
            ];

            // New user registration
            if (empty($existingUserData)) {
                if ($this->sendOtpViaMail($commonData) && $this->registrationModel->insertCustomerDetails($commonData)) {
                    $responseData = [
                        'success' => true,
                        'message' => 'OTP sent successfully. Please verify your email address.',
                    ];
                } else {
                    $responseData['message'] = 'Failed to send OTP. Please try again later.';
                }
            } else {
                // Existing user onboarding
                $onboardingStatus = $existingUserData['onboarding_status'];
                if ($onboardingStatus == 0) {
                    if ($this->sendOtpViaMail($commonData) && $this->registrationModel->updateCustomerDetails(['email_address' => $email], $commonData)) {
                        $responseData = [
                            'success' => true,
                            'message' => 'OTP sent successfully. Please verify your email address.',
                        ];
                    } else {
                        $responseData['message'] = 'Failed to send OTP. Please try again later.';
                    }
                } else {
                    $responseData = [
                        'success' => false,
                        'status' => $onboardingStatus,
                        'message' => 'Email already exists. Please try logging in.',
                    ];
                }
            }

            return $this->response->setJSON($responseData);
        } catch (Exception $e) {
            $responseData['message'] = 'An error occurred while sending OTP: ' . $e->getMessage();
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return $this->response->setJSON($responseData);
        }
    }

    /**
     * Handle Step 2: Address update for existing users.
     *
     * @param array|null $existingUserData User data from the database, if any.
     * @param string $email User's email address.
     * @param array &$responseData Response data to be returned as JSON.
     * @return ResponseInterface JSON response containing the result of the address update.
     */
    private function handleStep2(?array $existingUserData, string $email, array &$responseData): ResponseInterface
    {
        try {
            if (empty($existingUserData)) {
                $responseData['message'] = 'Email not found.';
                return $this->response->setJSON($responseData);
            }

            $postcode = $this->request->getPost('postcode');
            if (empty($postcode)) {
                $responseData['message'] = 'Please provide a valid postcode and address.';
                return $this->response->setJSON($responseData);
            }

            $addressData = [
                'postcode' => $postcode,
                'full_address' => $this->request->getPost('address'),
                'city' => $this->request->getPost('city'),
                'state' => $this->request->getPost('state'),
                'onboarding_status' => 3,
            ];

            if ($this->registrationModel->updateCustomerDetails(['email_address' => $email], $addressData)) {
                $responseData = [
                    'success' => true,
                    'status' => 3,
                    'message' => 'Address updated successfully.',
                ];
            } else {
                $responseData['message'] = 'Failed to update address. Please try again later.';
            }

            return $this->response->setJSON($responseData);
        } catch (\Exception $e) {
            $responseData['message'] = 'An error occurred while updating the address: ' . $e->getMessage();
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return $this->response->setJSON($responseData);
        }
    }

    /**
     * Handle Step 3: Password update for existing users.
     *
     * @param array|null $existingUserData User data from the database, if any.
     * @param string $email User's email address.
     * @param array &$responseData Response data to be returned as JSON.
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response containing the result of the password update.
     */
    private function handleStep3(?array $existingUserData, string $email, array &$responseData): ResponseInterface
    {
        $db = \Config\Database::connect();

        try {
            if (empty($existingUserData)) {
                $responseData['message'] = 'Email not found.';
                return $this->response->setJSON($responseData);
            }

            $password = $this->request->getPost('password');
            if (empty($password)) {
                $responseData['message'] = 'Password is required.';
                return $this->response->setJSON($responseData);
            }

            $db->transStart(); // Start transaction

            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
            $passwordData = [
                'password' => $hashedPassword,
                'onboarding_status' => 1,
            ];

            if ($this->registrationModel->updateCustomerDetails(['email_address' => $email], $passwordData)) {
                // Call stored procedure to create business tables
                $this->registrationModel->callCreateBusinessTablesProcedure($email);

                $db->transComplete(); // Complete transaction

                if ($db->transStatus()) {
                    $commonData = [
                        'email_address' => $email,
                        'mobile_number' => $existingUserData['mobile_number'],
                        'agency_name' => $existingUserData['agency_name'],
                        'owner_manager_name' => $existingUserData['owner_manager_name']
                    ];
                    $this->sendThankYouEmail($commonData);
                    $responseData = [
                        'success' => true,
                        'status' => 1,
                        'message' => 'Account created successfully. Password updated.',
                    ];
                } else {
                    // Handle transaction failure
                    $db->transRollback();
                    $responseData['message'] = 'Failed to create account. Please try again later.';
                }
            } else {
                // Handle update failure
                $db->transRollback();
                $responseData['message'] = 'Failed to update account details. Please try again later.';
            }
        } catch (\Exception $e) {
            // Ensure rollback on exception
            $db->transRollback();
            $responseData['message'] = 'An error occurred while updating the password: ' . $e->getMessage();
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
        }

        return $this->response->setJSON($responseData);
    }


    /**
     * Handle existing users based on their onboarding status.
     *
     * @param array|null $existingUserData User data from the database, if any.
     * @param array &$responseData Response data to be returned as JSON.
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response containing the result of the existing user check.
     */
    private function handleExistingUser(?array $existingUserData, array &$responseData): ResponseInterface
    {
        try {
            if (empty($existingUserData)) {
                // No user data found
                $responseData['message'] = 'Email already exists in the system.';
                return $this->response->setJSON($responseData);
            }

            // Retrieve onboarding status
            $onboardingStatus = $existingUserData['onboarding_status'] ?? null;

            switch ($onboardingStatus) {
                case 2:
                    $responseData = [
                        'success' => true,
                        'status' => 3,
                        'message' => 'This email address already exists, but the address has not been provided yet.',
                    ];
                    break;

                case 3:
                    $responseData = [
                        'success' => true,
                        'status' => 4,
                        'message' => 'This email address already exists with an address provided.',
                    ];
                    break;

                default:
                    // Default case for other onboarding statuses
                    $responseData['message'] = 'This email address is already in use.';
                    break;
            }

            return $this->response->setJSON($responseData);
        } catch (\Exception $e) {
            // Handle any unexpected exceptions
            $responseData['message'] = 'An error occurred while processing the existing user: ' . $e->getMessage();
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return $this->response->setJSON($responseData);
        }
    }

    /**
     * Send OTP via email to the user.
     *
     * @param array $userData User data including email, OTP, etc.
     * @return bool
     */
    private function sendOtpViaMail(array $userData, int $reason = 0): bool
    {
        try {
            // Load the email helper if not already loaded
            helper('email');

            // Prepare the HTML body from a view template
            if ($reason == 1) {
                $htmlBody = view('forgetPasswordEmailTemplate', $userData);
                // Define the subject and recipient
                $subject = 'Password Reset Request – TheNexZen';
            } else {
                $htmlBody = view('registerOtpEmailTemplate', $userData);
                $subject = 'Your OTP for TheNexZen Sign Up';
            }

            // Define the recipient
            $from = DO_NOT_REPLY_MAIL_ID; // Sender email address
            $to = [$userData['email_address']]; // Recipient email address

            // Send the email using the sendEmail function
            return sendEmail($from, $to, $subject, $htmlBody, null, 'html');
        } catch (\Exception $e) {
            // Log the error with class and function details for better traceability
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate the OTP provided by the user.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response containing the validation result.
     */
    public function validateOtp(): ResponseInterface
    {
        // Extract email and OTP from POST data
        $email = $this->request->getPost('email');
        $otp = $this->request->getPost('otp');
        $responseData = ['success' => false];

        try {
            // Validate the input data
            if (empty($email) || empty($otp)) {
                $responseData['error'] = 'Email and OTP are required.';
                return $this->response->setJSON($responseData);
            }

            // Check if OTP is valid for the given email
            $otpData = $this->registrationModel->getCustomerDetails(
                ['email_address' => $email, 'otp' => $otp],
                'email_address, otp'
            );

            // If OTP is valid, update onboarding status and remove OTP
            if (!empty($otpData)) {
                $this->registrationModel->updateCustomerDetails(
                    ['email_address' => $email],
                    ['otp' => null, 'onboarding_status' => 2]
                );

                $responseData = [
                    'success' => true,
                    'message' => 'OTP verified successfully.',
                ];
            } else {
                $responseData['error'] = 'Invalid OTP.';
            }

            return $this->response->setJSON($responseData);
        } catch (\Exception $e) {
            // Log any errors with details
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    /**
     * Displays the Forget Password view.
     *
     * @return ResponseInterface Renders the 'ForgetPassword' view.
     */
    public function forgetPassword(): ResponseInterface
    {
        try {
            // Render the ForgetPassword view
            $viewContent = view('ForgetPassword');
            return $this->response->setBody($viewContent);
        } catch (Exception $e) {
            // Log the exception with a clear context
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());

            // Return an error response with a 500 status code
            return $this->response->setStatusCode(500)->setBody('An error occurred while loading the Forget Password view.');
        }
    }

    /**
     * Sends an OTP to the user for Forget Password functionality.
     *
     * @return ResponseInterface Returns the status of the OTP process in JSON format.
     */
    public function forgetPasswordSendOtp(): ResponseInterface
    {
        $email = $this->request->getPost('email');
        $responseData = ['success' => false];

        try {
            // Validate email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $responseData['message'] = 'Invalid email address.';
                $responseData['status'] = 4;
                return $this->response->setJSON($responseData);
            }

            // Check if the email exists in the database
            $existingUserData = $this->registrationModel->getCustomerDetails(['email_address' => $email], '*');

            if (empty($existingUserData)) {
                $responseData['message'] = 'Email not found.';
                $responseData['status'] = 2;
            } elseif ($existingUserData[0]['onboarding_status'] != 1) {
                $responseData['message'] = 'Signup is not complete. You are redirected to the signup page.';
                $responseData['status'] = 3;
            } else {
                // Generate and send OTP
                $otp = random_int(100000, 999999); // Use random_int for cryptographic randomness
                $existingUserData = $existingUserData[0];

                $commonData = [
                    'email_address' => $existingUserData['email_address'],
                    'mobile_number' => $existingUserData['mobile_number'],
                    'otp' => $otp,
                    'agency_name' => $existingUserData['agency_name'],
                    'owner_manager_name' => $existingUserData['owner_manager_name'],
                ];

                $postData = ['otp' => $otp];

                // Attempt to send the OTP and update the database
                $isMailSent = $this->sendOtpViaMail($commonData, 1);
                $isOtpUpdated = $this->registrationModel->updateCustomerDetails(['email_address' => $email], $postData);

                if ($isMailSent && $isOtpUpdated) {
                    $responseData = [
                        'success' => true,
                        'status' => 1,
                        'message' => 'OTP sent successfully.',
                    ];
                } else {
                    $responseData = [
                        'success' => false,
                        'status' => 0,
                        'message' => 'Failed to send OTP. Please try again later.',
                    ];
                }
            }
        } catch (Exception $e) {
            // Log the exception with detailed context
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            $responseData['message'] = 'An error occurred: ' . $e->getMessage();
        }

        // Return the response as JSON
        return $this->response->setJSON($responseData);
    }

    /**
     * Validates the OTP for the Forget Password functionality.
     *
     * @return ResponseInterface Returns the result of OTP validation in JSON format.
     */
    public function forgetPasswordValidateOtp(): ResponseInterface
    {
        // Extract email and OTP from POST data
        $email = $this->request->getPost('email');
        $otp = $this->request->getPost('otp');
        $responseData = ['success' => false];

        try {
            // Validate the input data
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $responseData['error'] = 'Invalid or missing email address.';
                return $this->response->setJSON($responseData);
            }

            if (empty($otp) || !is_numeric($otp) || strlen($otp) !== 6) {
                $responseData['error'] = 'Invalid or missing OTP.';
                return $this->response->setJSON($responseData);
            }

            // Check if OTP is valid for the given email
            $otpData = $this->registrationModel->getCustomerDetails(
                ['email_address' => $email, 'otp' => $otp],
                'email_address, otp'
            );

            if (!empty($otpData)) {
                // OTP is valid: Update onboarding status and clear the OTP
                $this->registrationModel->updateCustomerDetails(
                    ['email_address' => $email],
                    ['otp' => null]
                );

                $responseData = [
                    'success' => true,
                    'message' => 'OTP verified successfully.',
                ];
            } else {
                $responseData['error'] = 'Invalid OTP.';
            }
        } catch (\Exception $e) {
            // Log any errors with details
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            $responseData['error'] = 'An error occurred: ' . $e->getMessage();
        }

        // Return the response as JSON
        return $this->response->setJSON($responseData);
    }

    /**
     * Updates the user's password after validating the request.
     *
     * @return ResponseInterface Returns the result of the password update operation in JSON format.
     */
    public function updatePassword(): ResponseInterface
    {
        $responseData = ['success' => false];

        try {
            // Extract email and password from POST data
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Validate email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $responseData['message'] = 'Invalid or missing email address.';
                return $this->response->setJSON($responseData);
            }

            // Validate password
            if (empty($password)) {
                $responseData['message'] = 'Password is required.';
                return $this->response->setJSON($responseData);
            }

            // Check if the email exists in the database
            $existingUserData = $this->registrationModel->getCustomerDetails(
                ['email_address' => $email],
                'email_address'
            );

            if (empty($existingUserData)) {
                $responseData['message'] = 'Email not found.';
                return $this->response->setJSON($responseData);
            }

            // Hash the password securely
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

            // Update the password in the database
            $passwordData = ['password' => $hashedPassword];
            if ($this->registrationModel->updateCustomerDetails(['email_address' => $email], $passwordData)) {
                $responseData = [
                    'success' => true,
                    'status' => 1,
                    'message' => 'Password updated successfully.',
                ];
            } else {
                $responseData['message'] = 'Failed to update the password. Please try again later.';
            }
        } catch (\Exception $e) {
            // Log the exception
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            $responseData['message'] = 'An error occurred while updating the password: ' . $e->getMessage();
        }

        return $this->response->setJSON($responseData);
    }

    /**
     * Send OTP via email to the user.
     *
     * @param array $userData User data including email, OTP, etc.
     * @return bool
     */
    private function sendThankYouEmail(array $userData): bool
    {
        try {
            // Load the email helper if not already loaded
            helper('email');


            $htmlBody = view('registerThankYouEmailTemplate', $userData);
            $subject = ' Welcome to TheNexZen! 🚀';

            // Define the recipient
            $from = DO_NOT_REPLY_MAIL_ID; // Sender email address
            $to = [$userData['email_address']]; // Recipient email address

            // Send the email using the sendEmail function
            return sendEmail($from, $to, $subject, $htmlBody, null, 'html');
        } catch (\Exception $e) {
            // Log the error with class and function details for better traceability
            logMessage(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $e->getMessage());
            return false;
        }
    }
}
