<?php


namespace QifTransformer;


use Carbon\Carbon;
use MimoGraphix\QIF\Transaction;

abstract class BaseTransformer {
	/** @var Carbon $fromDate */
	protected $fromDate;
	protected $debug = false;

    public function __construct(array $opts) {
    	if (isset($opts['date'])) {
			$this->fromDate = new Carbon($opts['date']);
		}
    	$this->debug = isset($opts['v']);
    }

    /**
     * @param $data
     * @return Transaction[]
     */
    public function transform(string $data): array {
    	$transactions = $this->getTransactions($data);
		if (isset($this->fromDate)) {
			$transactions = array_filter($transactions, function(Transaction $t) {
				$keep = $this->fromDate->isBefore($t->getDate());
				if (!$keep && $this->isDebug()) {
					printf("Discarding transaction with date %s" . PHP_EOL, $t->getDate()->toFormattedDateString());
				}
				return $keep;
			});
		}
		return $transactions;
	}

	/**
	 * @param string $data
	 * @return Transaction[]
	 */
	abstract protected function getTransactions(string $data): array;

	protected static function cleanDescription(string $str): string {
		return preg_replace("/\s+/", ' ', $str);
	}

	protected function isDebug() {
		return $this->debug;
	}
}