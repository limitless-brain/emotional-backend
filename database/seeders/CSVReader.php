<?php


namespace Database\Seeders;


class CSVReader
{
    private $file;
    private $delimiter;
    private $iterator = 0;
    private $header = null;

    public function __construct($filename, $delimiter = ",")
    {
        // load the file
        $this->file = fopen($filename, 'r');

        // initialize properties
        $this->delimiter = $delimiter;
    }

    public function csvToArray()
    {
        // array to store data
        $data = array();

        $is_1000_entry = false;
        // loop while we reading each row of the file
        // taking only 1000 entry
        while (($row = fgetcsv($this->file, 1000, $this->delimiter)) !== false) {

            // start by assuming data is less than 1000
            $is_1000_entry = false;

            // check if we store the header
            if (!$this->header) {
                // store the header
                $this->header = $row;
            } else {
                // increase the iterator
                $this->iterator++;

                // add row to the data
                if (sizeof($row) == sizeof($this->header))
                    $data[] = array_combine($this->header, $row);

                // check if we reach 1000 records
                if ($this->iterator != 0 && $this->iterator % 1000 == 0) {
                    $is_1000_entry = true;
                    // yield the part of data we collect
                    // and reset data array
                    $bucket = $data;
                    $data = array();
                    yield $bucket;
                }
            }
        }

        // close the file
        fclose($this->file);

        // yield the rest of data
        if (!$is_1000_entry) {
            yield $data;
        }
        // end of the function
    }
}
