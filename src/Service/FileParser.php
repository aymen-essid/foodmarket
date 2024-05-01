<?php

namespace App\Service;

use SplFileObject;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

Class FileParser{

    public function __construct(
        private string $targetDirectory
    ) {
    }

    // change these options about the file to read
    private $csvParsingOptions = array(
        'ignoreFirstLine' => false,
        'csvSeparator'    => ',',
        'nbColumns'       => 3
    );


    /**
     * Parse a csv file
     * 
     * @return array
     */
    public function parseCSV(string $fileName)
    {

        $finder = new Finder();
        $finder->files()->in($this->getTargetDirectory())->name($fileName);

        foreach ($finder as $file) { $csv = $file; }

        $rows = array();
        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, $this->csvParsingOptions['csvSeparator'])) !== FALSE) {
                $i++;
                // keep only 3 first columns 
                $data = array_splice($data, 0, $this->csvParsingOptions['nbColumns']);
                if ($this->csvParsingOptions['ignoreFirstLine'] && $i == 1) { continue; }
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
    }


    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

}
