<?php

namespace Adanut\Sap\Functions\System\Table;

use Adanut\Sap\Communication\Connection;
use Adanut\Sap\Facades\Arr;
use Adanut\Sap\Functions\FunctionModule;
use Adanut\Sap\Functions\System\Table\QueryBuilder;
use Carbon\Carbon;

class Table extends FunctionModule
{
    /**
     * @var array
     */
    protected $parameters = [
        'DELIMITER'   => "\x08",
        'QUERY_TABLE' => '',
        'FIELDS'      => [],
        'OPTIONS'     => [],
    ];

    /**
     * QueryBuilders
     * 
     * @var QueryBuilder
     */
    public $query;
    
    /**
     * Create a new instance of RfcReadTable.
     *
     * @param Connection $handle
     *
     * @return void
     */
    public function __construct(Connection $connection)
    {
        $this->query = new QueryBuilder($this);
        parent::__construct($connection, 'RFC_READ_TABLE');
    }

    /**
     * Delimiter used by SAP to concatenate table rows
     * 
     * @param  string $value
     * 
     * @return $this
     */
    public function delimiter($value)
    {
        return $this->param('DELIMITER', $value);
    }

    /**
     * Return query fields array.
     * 
     * @param  array $fields
     * 
     * @return array
     */
    public function fields($fields) {
        foreach ($fields as $key => $field) {
            $fields[] = ['FIELDNAME' => strtoupper($field)];
            unset($fields[$key]);
        }
        return $fields;
    }

    /**
     * Set fields for retrieval and execute function. Keep in mind this value is limited to
     * 512 bytes per row.
     * 
     * @param  array  $fields
     * 
     * @return Collection
     */
    public function get(array $fields = [])
    {
        $this->param('FIELDS', $this->fields($fields));
        $this->param('OPTIONS', $this->query->options());
        return $this->parse($this->execute());
    }

    /**
     * Limit table rows to provided number.
     * 
     * @param  int $number
     *  
     * @return $this
     */
    public function limit($number)
    {
        return $this->param('ROWCOUNT', (int)$number);
    }

    /**
     * Skip provided number of rows from the result.
     * 
     * @param  int $number
     * 
     * @return $this
     */
    public function offset($number)
    {
        return $this->param('ROWSKIPS', (int)$number);
    }

    /**
     * Set table to be queried.
     * 
     * @param  string $name
     * 
     * @return $this
     */
    public function table($name)
    {
        return $this->param('QUERY_TABLE', strtoupper($name));
    }

    /**
     * Parse output from SAP and transform to Collection
     * 
     * @param  array $result
     * 
     * @return Collection
     */
    public function parse($result)
    {
        // Clear all that spaces.
        $result = Arr::trim($result);

        // Get DATA and FIELDS SAP tables.
        $data   = collect($result['DATA']);
        $fields = collect($result['FIELDS']);

        // Get columns.
        $columns = $fields->pluck('FIELDNAME')->toArray();
        
        // If no raw rows early exit.
        if ($data->count() === 0) {
            return collect();
        }

        // Explode raw data rows and combine with columns.
        $table = $data->pluck('WA')->transform(function($item) use ($columns) 
        {
            $values = Arr::trim(explode($this->parameters['DELIMITER'], $item));
            return array_combine($columns, $values);
        });

        // Apply transformations in corelation with fields type.
        $fields->each(function ($field) use ($table) {
            // Transform dates.
            if ($field['TYPE'] === 'D') {
                $table->transform(function ($row) use($field) {
                    if ($row[$field['FIELDNAME']] == '00000000') {
                        $row[$field['FIELDNAME']] = null;
                    } else {
                        try {
                            $row[$field['FIELDNAME']] = Carbon::createFromFormat('Ymd', $row[$field['FIELDNAME']]);
                        } catch (\InvalidArgumentException $e) {
                            $row[$field['FIELDNAME']] = null;
                        }
                    }
                    return $row;
                });
            }
        });

        return $table;
    }

    /**
     * Dynamically handle calls to object methods.
     * 
     * @param  string $method
     * @param  array  $arguments
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}(...$arguments);
        } elseif (method_exists($this->query, $method)) {
            return $this->query->{$method}(...$arguments);
        } else {
            trigger_error("Call to undefined method ". get_class($this) ."::$method()", E_USER_ERROR);
        }
    }
}