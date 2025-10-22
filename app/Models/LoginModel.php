<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class LoginModel
 * Handles database interactions related to user authentication, including retrieving and validating users.
 */
class LoginModel extends Model
{
    protected $usersTable;
    protected $requestContactTable;

    public function __construct()
    {
        parent::__construct();
        // Initialize the table reference for 'users'
        $this->usersTable = $this->db->table(CUSTOMER_REGISTRATION_TABLE);
        $this->requestContactTable = $this->db->table(REQUEST_CONTACT_TABLE);
    }

    /**
     * Validate user credentials (for login)
     *
     * @param string $id The ID of the user attempting to log in
     * @param string $password The password provided for authentication
     * @return array|null Returns user data as an associative array if credentials are valid, false otherwise
     */
    public function validateUserCredentials($userName, $password)
    {
        try {
            // Fetch the user by id
            $user = $this->usersTable->where('email_address', $userName)->get()->getRowArray();

            // Check if user exists and verify the password
            if ($user && password_verify($password, $user['password'])) {
                return $user; // Return user data if credentials are valid
            }

            return false; // Return null if credentials are invalid
        } catch (\Exception $e) {
            // Log error message for troubleshooting
            log_message('error', 'LoginModel::validateUserCredentials - ' . $e->getMessage());
            return false; // Return false in case of an error
        }
    }
    public function requestContactSave(array $customerData): bool
    {
        try {
            // Check if table exists
            if (!$this->tableExists('request_contacts')) {
                $this->createRequestContactsTable();
            }

            // Now insert data
            return $this->requestContactTable->insert($customerData);
        } catch (\Exception $e) {
            log_message('error', 'RegisterModel::requestContactSave - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the table exists in the database
     */
    private function tableExists(string $tableName): bool
    {
        $db = \Config\Database::connect();
        return $db->tableExists($tableName);
    }

    /**
     * Create `request_contacts` table if it doesn't exist
     */
    private function createRequestContactsTable(): void
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        $fields = [
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true
            ],
            'company' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ];

        $forge->addField($fields);
        $forge->addKey('id', true);
        $forge->createTable(REQUEST_CONTACT_TABLE, true); // true = only create if it doesn't exist
    }
}
