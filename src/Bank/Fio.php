<?php

namespace Ludoi\Bank;

use Exception;
use Nette\DateTime;

/**
 * Class Fio
 */
class Fio {

    private $token;
    private $rest_url = 'https://www.fio.cz/ib_api/rest/';

    /**
     * @param string $token SECURE
     */
    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * Pohyby na účtu za určené období.
     * JSON only!
     * @param string $from
     * @param string $to
     * @return array|mixed
     */
    public function transactions($from = '-1 month', $to = 'now') {
        $from = DateTime::from($from)->format('Y-m-d');
        $to = DateTime::from($to)->format('Y-m-d');
        $url = $this->rest_url . 'periods/' . $this->token . '/' . $from . '/' . $to . '/transactions.json';
        return $this->parseJSON($this->download($url));
    }

    /**
     * Oficiální výpisy pohybů z účtu.
     * JSON only!
     * @param $id
     * @param null $year
     * @return array|mixed
     */
    public function transactionsByID($id, $year = NULL) {
        if ($year === NULL) {
            $year = date('Y');
        }
        $url = $this->rest_url . 'by-id/' . $this->token . '/' . $year . '/' . $id . '/transactions.json';
        return $this->parseJSON($this->download($url));
    }

    /**
     * Pohyby na účtu od posledního stažení.
     * JSON only!
     * @return array|mixed
     */
    public function transactionsLast() {
        $url = $this->rest_url . 'last/' . $this->token . '/transactions.json';
        return $this->parseJSON($this->download($url));
    }

    /**
     * @param $url
     * @return mixed
     * @throws Exception
     */
    private function download($url) {
        if (!extension_loaded('curl')) {
            throw new Exception('Curl extension, does\'t loaded.');
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);
        $result = curl_exec($curl);
        return $result;
        //return file_get_contents($url); //ALTERNATIVE
    }

    /**
     * @param $data
     * @return array|mixed
     */
    private function parseJSON($data) {
        $json = json_decode($data);
        if ($json === NULL) {
            //Moc ryhlé požadavky na Fio API
            throw new Exception('Fio API overheated. Please wait...');
            //Když se posílá stále moc požadavků, tak se to z Exception nikdy nevyhrabe. Musí se opravdu počkat.
        }
        if (!$json->accountStatement->transactionList) {
            return array(); // There are no transactions (header only)
        }
        $payments = array();
        foreach ($json->accountStatement->transactionList->transaction as $row) {
            $out = array();
            foreach ($row as $column) {
                if ($column) {
                    $out[$column->id] = $column->value; //v $column->name je název položky
                    /*
                     * 0  - Datum
                     * 1  - Částka (!)
                     * 5  - Variabilní symbol (!)
                     * 14 - Měna (!)
                     * Hodnoty (!) se musí použít ke kontrole správnosti...
                     */
                }
            }
            array_push($payments, $out);
        }
        return $payments;
    }

}