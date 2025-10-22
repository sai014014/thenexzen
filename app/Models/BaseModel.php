<?php
// app/Models/BaseModel.php
namespace App\Models;

use CodeIgniter\Model;
use App\Services\QueryLogger;

class BaseModel extends Model
{
    protected $queryLogger;

    public function __construct()
    {
        parent::__construct();  // Call the parent constructor to initialize the model

        // Initialize the query logger service
        $this->queryLogger = new QueryLogger();
    }

    // Override the query method to capture all queries
    public function query($sql, $bindings = [], $returnObject = true)
    {
        $startTime = microtime(true);

        // Execute the query using the parent method
        $result = parent::query($sql, $bindings, $returnObject);

        // Calculate the execution time
        $executionTime = microtime(true) - $startTime;

        logMessage($sql);
        logMessage($bindings);
        logMessage($executionTime);

        // Log the query
        $this->queryLogger->logQuery($sql, $bindings, $executionTime);

        return $result;
    }
}
