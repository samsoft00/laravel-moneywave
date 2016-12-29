<?php
/**
 * Created by PhpStorm.
 * User: WF-INNOVATION
 * Date: 12/28/2016
 * Time: 10:49 AM
 */

namespace Samsoft\Moneywave;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Config;
use Samsoft\Moneywave\Exceptions\isNullException;

class Moneywave
{
    //request for lists of banks -/banks
    // create access token - /v1/merchant/verify

    protected $client;

    private $apiKey;

    private $secretkey;

    private $baseUrl;

    private $tokenUrl = "/v1/merchant/verify";
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Moneywave constructor.
     * @param ClientInterface $http
     */
    public function __construct()
    {
        $this->getApiKey();
        $this->getSecretKey();
        $this->getBaseUrl();
        $this->setRequestOptions();
    }

    private function getApiKey()
    {
        $this->apiKey = Config::get('moneywave.apikey');
    }

    private function getSecretKey()
    {
        $this->secretkey = Config::get('moneywave.secretkey');
    }

    private function getBaseUrl()
    {
        $this->baseUrl = Config::get('moneywave.baseUrl');
    }

    private function setRequestOptions()
    {
        $authBearer = 'Bearer '. $this->getAccessToken();

        $this->client = new Client([
            'base_uri'  =>  $this->baseUrl,
            'header'    =>  [
                'Authorization' => $authBearer,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json'
            ]
        ]);

    }


    /*
     * Get Moneywave access token
     */
    private function getAccessToken()
    {
        $response = $this->getHttpClient()->post("https://moneywave.herokuapp.com".$this->tokenUrl, [
            'form_params' => [
                    "apiKey" => $this->apiKey,
                    "secret" => $this->secretkey
                ]
        ]);

        return json_decode($response->getBody(), true)['token'];
    }

    public function getListOfBanks()
    {
        $banks = $this->client->post("http://moneywave.herokuapp.com/banks", []);
        return json_decode($banks->getBody(), true);
    }

    public function makePaymentRequest()
    {
        //get expire month/year
        $expiry = explode('/', request()->cardExpiry);

        $data = [
            "firstname"                 =>  request()->firstname,
            "lastname"                  =>  request()->lastname,
            "phonenumber"               =>  request()->phonenumber,
            "email"                     =>  request()->email,
            "card_no"                   =>  request()->cardNumber,
            "cvv"                       =>  request()->cardCVC,
            "expiry_year"               =>  $expiry[0],
            "expiry_month"              =>  $expiry[1],
            "apiKey"                    =>  $this->apiKey,
            "recipient_bank"            =>  Config::get('moneywave.bank_name'),
            "recipient_account_number"  =>  Config::get('moneywave.account_number'),
            "amount"                    =>  intval( Config::get('moneywave.amount') ),
            "fee"                       =>  intval( Config::get('moneywave.fee') ),
            "redirecturl"               =>  Config::get('moneywave.redirect_url'),
            "medium"                    =>  "web"
        ];

        array_filter($data);

//        dd($data);

        $this->setHttpResponse('/v1/transfer', 'POST', $data);
    }

    /**
     * @param $relativeUrl
     * @param $method
     * @param array $body
     * @throws isNullException
     */
    private function setHttpResponse($relativeUrl, $method, $body = [])
    {
        if(is_null($method)){
            throw new isNullException("Empty method not allow");
        }

        $authBearer = 'Bearer '. $this->getAccessToken();

        $this->client->{strtolower($method)}("https://moneywave.herokuapp.com/v1/transfer",
            [
                "form_params"   =>  $body,
                "header"    =>  [ 'Authorization' => $authBearer ]
            ]
        );
    }

    private function getHttpClient()
    {
        if(is_null($this->httpClient)){
            $this->httpClient = new Client();
        }

        return $this->httpClient;
    }

}