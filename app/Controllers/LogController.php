<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class LogController
 * Handles log downloading functionality, allowing users to download logs by date.
 */
class LogController extends Controller
{
    private $logPath = WRITEPATH . 'logs/';
    private $logPassword = 'TheNextGen@2024'; // Set your password here

    /**
     * Displays the log download view.
     *
     * @return mixed
     */
    public function index()
    {
        return view('logs/downloadLogView');
    }

    /**
     * Handles the log download request by date.
     *
     * @return mixed
     */
    public function downloadByDate()
    {
        try {
            // Retrieve log date and password from the request
            $date = $this->request->getPost('log_date');
            $password = $this->request->getPost('password');

            // Validate date and password
            if (!$date || !$password) {
                return redirect()->back()->with('error', 'Date and password are required.');
            }

            // Check if the provided password matches the stored password
            if ($password !== $this->logPassword) {
                return redirect()->back()->with('error', 'Incorrect password.');
            }

            // Construct the log filename
            $filename = 'log-' . $date . '.log';
            $filePath = $this->logPath . $filename;

            // Check if the log file exists and initiate download
            if (file_exists($filePath)) {
                return $this->response->download($filePath, null)->setFileName($filename);
            }

            // Redirect back with an error if the file does not exist
            return redirect()->back()->with('error', 'Log file for the selected date not found.');
        } catch (\Exception $e) {
            // Log error message for troubleshooting
            log_message('error', 'LogController::downloadByDate - ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while downloading the log. Please try again.');
        }
    }
}
