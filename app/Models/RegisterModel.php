<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;
use Exception;

/**
 * Class RegisterModel
 * Handles all database interactions related to user registration, including retrieving, inserting, and updating customer details.
 */
class RegisterModel extends Model
{
    /**
     * @var BaseBuilder Reference to the 'customer_registration' table in the database.
     */
    protected $customerRegistrationTable;

    /**
     * RegisterModel constructor.
     * Initializes the table reference for customer registration.
     */
    public function __construct()
    {
        parent::__construct();
        // Initialize the table reference for the 'customer_registration' table
        $this->customerRegistrationTable = $this->db->table(CUSTOMER_REGISTRATION_TABLE);
    }

    /**
     * Retrieves customer details from the database.
     *
     * @param array|null $conditions An associative array for the WHERE clause conditions.
     * @param string|null $columns A comma-separated string or array of columns to retrieve. Defaults to '*' for all columns.
     * @return array|null Returns an array of results or null if an error occurs.
     */
    public function getCustomerDetails(?array $conditions = null, $columns = '*'): ?array
    {
        try {
            $query = $this->customerRegistrationTable->select($columns);
            if ($conditions) {
                $query->where($conditions);
            }
            return $query->get()->getResultArray();
        } catch (Exception $e) {
            logMessage('RegisterModel::getCustomerDetails - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Inserts a new customer's details into the database.
     *
     * @param array $customerData An associative array of customer data to insert.
     * @return bool Returns true on success, false on failure.
     */
    public function insertCustomerDetails(array $customerData): bool
    {
        try {
            return $this->customerRegistrationTable->insert($customerData);
        } catch (Exception $e) {
            logMessage('RegisterModel::insertCustomerDetails - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing customer's details in the database.
     *
     * @param array $conditions An associative array of conditions to find the customer.
     * @param array $updateData An associative array of data to update.
     * @return bool Returns true on success, false on failure.
     */
    public function updateCustomerDetails(array $conditions, array $updateData): bool
    {
        try {
            return $this->customerRegistrationTable->where($conditions)->set($updateData)->update();
        } catch (Exception $e) {
            logMessage('RegisterModel::updateCustomerDetails - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calls the 'CREATE_BUSINESS_TABLE_PROCEDURE' stored procedure with a single parameter.
     *
     * @param string $email The email address used to fetch the parameter value.
     * @return bool Returns true if the procedure executes successfully, false otherwise.
     */
    public function callCreateBusinessTablesProcedure(string $email): bool
    {
        try {
            $customerDetails = $this->getCustomerDetails(['email_address' => $email], 'business_key');
            if (empty($customerDetails)) {
                logMessage("RegisterModel::callCreateBusinessTablesProcedure - No customer found for email: $email");
                return false;
            }

            $businessKey = $customerDetails[0]['business_key'];
            $query = "CALL " . CREATE_BUSINESS_TABLE_PROCEDURE . "(?) ";
            $this->db->query($query, [$businessKey]);

            // Check if any errors occurred during the procedure execution
            if ($this->db->error()) {
                $error = $this->db->error();
                logMessage('RegisterModel::callCreateBusinessTablesProcedure - Database error: ' . $error['message']);
                return false;
            }

            return true;
        } catch (Exception $e) {
            logMessage('RegisterModel::callCreateBusinessTablesProcedure - ' . $e->getMessage());
            return false; // Return false in case of an error
        }
    }
    /**
     * Call stored procedure to generate unique business_id and business_key
     */
    public function getGeneratedBusinessIdentifiers(): array
    {
        try {
            // Start the stored procedure call
            $this->db->query("CALL generate_customerRegistrationIdentifiers(@business_id, @business_key)");

            // Fetch output variables from the procedure
            $query = $this->db->query("SELECT @business_id AS business_id, @business_key AS business_key");

            $result = $query->getRowArray();

            if (empty($result['business_id']) || empty($result['business_key'])) {
                log_message('error', 'Failed to generate business_id and business_key via stored procedure.');
                throw new \RuntimeException('Failed to generate business_id and business_key.');
            }

            return $result;
        } catch (\Throwable $e) {
            log_message('error', 'Exception in getGeneratedBusinessIdentifiers: {message}', ['message' => $e->getMessage()]);
            throw $e;  // Re-throw for controller to handle
        }
    }
}
