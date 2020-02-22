<?php


namespace QifTransformer;


use Carbon\Carbon;
use MimoGraphix\QIF\Enums\Types;
use MimoGraphix\QIF\Transaction;

class KoganCreditTransformer extends BaseTransformer {
    public function transform(string $data): array {
        $json = json_decode($data);

        return array_map(static function($t) {
            /**
             * @var $t KoganCreditTransaction
             */

            $transaction = new Transaction(Types::CCARD);
            // Amount always positive
            $amount = $t->transactionAmount;

            if ($t->transactionType === KoganCreditTransaction::TYPE_DEBIT) {
                $amount = 0 - $amount;
            }

            $transaction->setAmount($amount)
                ->setDate(new Carbon($t->transactionDate))
                ->setDescription(self::cleanDescription($t->transactionDescription));
            return $transaction;
        }, $json->transactions);
    }
}