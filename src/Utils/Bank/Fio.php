<?php
declare(strict_types=1);

namespace Ludoi\Utils;

use DateTimeImmutable;
use Exception;

/**
 * Class Fio
 */
class Fio {
    /**
     *
     */
    public const FIELD_AMOUNT = 1;
    /**
     *
     */
    public const FIELD_CURRENCY = 14;
    /**
     *
     */
    public const FIELD_EXTREF = 5;
    /**
     *
     */
    public const FIELD_DATE = 0;
    /**
     *
     */
    public const FIELD_MESSAGE = 16;
    /**
     *
     */
    public const FIELD_ACCOUNT = 2;
    /**
     *
     */
    public const FIELD_ACCOUNT_NAME = 10;
    /**
     *
     */
    public const FIELD_BANK_CODE = 3;
    /**
     *
     */
    public const FIELD_ID = 22;
    /**
     *
     */
    public const FIELD_BIC = 26;

    /**
     * @var string
     */
    private string $token;
    /**
     * @var string
     */
    private string $rest_url = 'https://www.fio.cz/ib_api/rest/';

    /**
     * @param string $token SECURE
     */
    public function __construct(string $token) {
        $this->token = $token;
    }

    /**
     * Pohyby na účtu za určené období.
     * JSON only!
     * @param string $from
     * @param string $to
     * @return array|null
     * @throws Exception
     */
    public function transactions(string $from = '-1 month', string $to = 'now'): ?array {

        $from = new DateTimeImmutable($from);
        $to = new DateTimeImmutable($to);
        $url = $this->rest_url . 'periods/' . $this->token . '/' . $from->format('Y-m-d') . '/' . $to->format('Y-m-d') . '/transactions.json';
        return $this->parseJSON($this->download($url));
    }

    /**
     * Oficiální výpisy pohybů z účtu.
     * JSON only!
     * @param string $id
     * @param string|null $year
     * @return array|null
     * @throws Exception
     */
    public function transactionsByID(string $id, ?string $year = null): ?array {
        if ($year === NULL) {
            $year = date('Y');
        }
        $url = $this->rest_url . 'by-id/' . $this->token . '/' . $year . '/' . $id . '/transactions.json';
        return $this->parseJSON($this->download($url));
    }

    /**
     * Pohyby na účtu od posledního stažení.
     * JSON only!
     * @return array|null
     * @throws Exception
     */
    public function transactionsLast(): ?array {
        $url = $this->rest_url . 'last/' . $this->token . '/transactions.json';
        return $this->parseJSON($this->download($url));
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    private function download(string $url): string {
        if (!extension_loaded('curl')) throw new \Ludoi\Utils\Exception('Curl extension, does not loaded.');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        return curl_exec($curl);
    }

    /**
     * @param string|null $data
     * @return array
     * @throws Exception
     */
    private function parseJSON(?string $data): array {
        $json = json_decode($data);
        if (is_null($json)) throw new \Ludoi\Utils\Exception('Fio API overheated. Please wait...');
        if (!$json->accountStatement->transactionList) {
            return array(); // There are no transactions (header only)
        }
        $payments = array();
        foreach ($json->accountStatement->transactionList->transaction as $row) {
            $out = array();
            foreach ($row as $column) {
                if ($column) {
                    $out[$column->id] = $column->value;
                    // $column->id corresponds to constants FIELD_*
                }
            }
            array_push($payments, $out);
        }
        return $payments;
    }
}