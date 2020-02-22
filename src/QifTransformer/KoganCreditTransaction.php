<?php


namespace QifTransformer;

class KoganCreditTransaction {
    const TYPE_CREDIT = "CREDIT";
    const TYPE_DEBIT = "DEBIT";
    const STATUS_BILLED = "BILLED";
    const STATUS_UNBILLED = "UNBILLED";

    public $displayAccountNumber;
    public $transactionDate;
    public $transactionDescription;
    public $transactionAmount;
    public $currencyCode;
    public $transactionType;
    public $transactionStatus;
    public $transactionAuthorizationCode;
}