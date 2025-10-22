<?php

namespace App\Controllers;

use App\Models\LoginModel;
use Modules\AccountManagement\Models\AccountManagementModel;

/**
 * Class Login
 * Handles user authentication, including login, registration, and logout functionality.
 */
class Login extends BaseController
{
    private $validation;
    private $loginModel;

    public function __construct()
    {
        $this->validation = \Config\Services::validation();
        $this->loginModel = new LoginModel();
    }

    /**
     * Displays the login view.
     *
     * @return mixed
     */
    public function index()
    {
        log_message('info', 'index');
        return view('homePage.php');
    }

    public function requestFormSubmit()
    {
        $data = $this->request->getJSON(true);  // Get JSON data directly

        $saveData = [
            'name'    => $data['name'] ?? '',
            'email'   => $data['email'] ?? '',
            'phone'   => $data['phone'] ?? '',
            'company' => $data['company'] ?? '',
            'created_at' => date("Y-m-d H:i:s"),
        ];
        if ($this->loginModel->requestContactSave($saveData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Request sent successfully']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save data'])->setStatusCode(500);
    }

    public function loginPage()
    {
        return view('Login');
    }

    /**
     * Handles the login form submission and user authentication.
     *
     * @return mixed
     */
    public function loginSubmit()
    {
        try {
            // Set validation rules for login form
            $this->validation->setRules([
                'id' => 'required',
                'password' => 'required',
            ]);

            // Check if the request data is valid
            if (!$this->validation->withRequest($this->request)->run()) {
                // Fetch all error messages and convert them into a string
                $errors = $this->validation->getErrors();
                $errorMessage = implode(' ', $errors);
                return $this->response->setJSON([
                    'success' => false,
                    'error' => $errorMessage,
                ]);
            }

            // Get user credentials from the request
            $userName = $this->request->getPost('id');
            $password = $this->request->getPost('password');

            // Validate user credentials
            $user = $this->loginModel->validateUserCredentials($userName, $password);

            if ($user) {
                // Set user ID in session upon successful login
                session()->set('businessId', $user['business_id']);
                session()->set('businessKey', strtolower($user['business_key']));
                session()->set('userId', 1);
                $accountModel = new AccountManagementModel();
                $businessLogo = $accountModel->getOnlyBusinessDetails([], 'business_logo');
                $businessLogo = $businessLogo['business_logo'];
                session()->set('businessLogo', $businessLogo);
                return $this->response->setJSON(['success' => true]);
            }

            // Return error if authentication fails
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Incorrect ID or password.',
            ]);
        } catch (\Exception $e) {
            // Log error message for troubleshooting
            log_message('error', 'Login::loginSubmit - ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'An error occurred during login. Please try again.',
            ]);
        }
    }

    /**
     * Handles user logout and session destruction.
     *
     * @return mixed
     */
    public function logout()
    {
        try {
            // Ensure the session is active
            if (session()->has('businessKey')) {
                // Remove all session data
                session()->destroy();
            }

            // Redirect to the login page
            return redirect()->to(base_url('login'));
        } catch (\Exception $e) {
            // Log error message for troubleshooting
            log_message('error', 'Login::logout - ' . $e->getMessage());
            return redirect()->to(base_url('login'))->with('error', 'An error occurred while logging out. Please try again.');
        }
    }
}
