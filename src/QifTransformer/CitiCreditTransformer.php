<?php


namespace QifTransformer;


use Carbon\Carbon;
use MimoGraphix\QIF\Enums\Types;
use MimoGraphix\QIF\Transaction;

class CitiCreditTransformer extends BaseTransformer {
    protected $override_year;

    public function __construct(array $opts) {
        parent::__construct($opts);
        $this->override_year = $opts['year'] ?? null;
    }

    public function transform(string $data): array {
        $lines = explode(PHP_EOL, $data);
        $map = array_map(function(string $line, $i) {
            $transaction = new Transaction(Types::CCARD);

            preg_match('/^(.+) (\d+) (.+?) ((?:[A-Z]+)?\d+) (.+)$/iU', $line, $matches);
            if (!$matches) {
                echo "Error reading line $i: \"$line\"" . PHP_EOL;
                return null;
            }

            $month = $matches[1];
            $day = $matches[2];
            $description = $matches[3];
            $ccNum = $matches[4];
            $amount = (float) $matches[5];

            // Citi stores debits as positive, credits negative
            $amount = -$amount;

            //Configure date
            $year = date_create()->format('Y');
            $date = new Carbon("$year-$month-$day");
            if ($this->override_year) {
                $date->setYear((int) $this->override_year);
            }

            $transaction->setDate($date)
                ->setDescription($description)
                ->setAmount($amount);
            return $transaction;
        }, $lines, array_keys($lines));
        return array_filter($map);
    }
}