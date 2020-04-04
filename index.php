<?php

require_once 'vendor/autoload.php';
require_once 'src/autoload.php';

use GetOpt\ArgumentException;
use GetOpt\GetOpt;
use GetOpt\Operand;
use GetOpt\Option;
use QifTransformer\BaseTransformer;
use QifTransformer\CitiCreditTransformer;
use QifTransformer\KoganCreditTransformer;

class Runner {
    protected static $sources = [
        'citi' => CitiCreditTransformer::class,
        'kogan' => KoganCreditTransformer::class
    ];

    /** @var GetOpt */
    protected $args;

    function getArgs() {
        $this->args = new GetOpt();
        $this->args->addOperands([
            Operand::create('input', Operand::REQUIRED)->setValidation('is_readable'),
            Operand::create('source', Operand::REQUIRED)->setValidation(function($source) {
                return array_key_exists($source, self::$sources);
            }, 'Unknown source. Options are: ' . implode(', ', array_keys(self::$sources)))
        ]);
        $this->args->addOptions([
            Option::create('y', 'year', GetOpt::REQUIRED_ARGUMENT)
                ->setValidation('is_numeric')
                ->setDescription('For supported transformers, override year for input files without year info.'),
            Option::create('d', 'date', GetOpt::REQUIRED_ARGUMENT)
				->setValidation('date_create')
				->setDescription('Only output transactions occuring after this date.'),
            Option::create('o', 'output', GetOpt::REQUIRED_ARGUMENT)
                ->setDescription('Write output .qif file to this filename.'),
			Option::create('v', 'verbose', GetOpt::NO_ARGUMENT)
				->setDescription('Print debug output.')
        ]);

        try {
            $this->args->process();
        } catch (ArgumentException $e) {
            echo $e->getMessage() . PHP_EOL;
            echo $this->args->getHelpText();
            die();
        }
    }

    function getQif() {
        $input = $this->args->getOperand('input');
        $source = $this->args->getOperand('source');
        $transformerClass = self::$sources[$source];

        /** @var BaseTransformer $transformer */
        $transformer = new $transformerClass($this->args->getOptions());

        $qif = new MimoGraphix\QIF\Writer();
        $transactions = $transformer->transform(file_get_contents($input));

        foreach ($transactions as $transaction) {
            $qif->addTransaction($transaction);
        }

        $output = $this->args->getOption('output');
        if ($output) {
            $dirname = dirname($output);
            if (!is_dir($dirname)) {
                if (!mkdir($dirname) && !is_dir($dirname)) {
                    throw new \RuntimeException(sprintf('Directory "%s" could not be created.', $dirname));
                }
            }

            $qif->saveToFile($output);
        } else {
            echo $qif; //For piping
        }
    }

    public function run() {
        $this->getArgs();
        $this->getQif();
    }
}

(new Runner())->run();