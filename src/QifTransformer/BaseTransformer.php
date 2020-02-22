<?php


namespace QifTransformer;


use MimoGraphix\QIF\Transaction;

abstract class BaseTransformer {
    public function __construct(array $opts) {

    }

    public static function cleanDescription(string $str): string {
        return preg_replace("/\s+/", ' ', $str);
    }
    /**
     * @param $data
     * @return Transaction[]
     */
    abstract public function transform(string $data): array;
}