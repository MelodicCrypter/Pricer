<?php
/**
 * The purpose of this class is to set a fixed-auto-converted price for your product or service.
 * First, you need to convert the base price (your currency) to your user's currency. e.g, 1GBP to HKD
 * Then after that you can already convert your price to your user's equivalent-currency price.
 *
 * API used for latest currency is from exchangeratesapi.io
 * API used for user's details is from geoplugin.net
 */

class Pricer
{
    // Your Properties
    protected $myCurrencyCode;
    protected $myPrice;
    // Your User's Properties
    protected $userConvertedPrice;
    protected $userCurrencySymbol;
    protected $userCurrencyCode;
    protected $userCurrencyRate;
    protected $userIPAddress;
    protected $userCountry;
    protected $userRegion;


    /**
     * @param String $cur
     */
    public function setMyCurrencyCode(String $cur)
    {
        $this->myCurrencyCode = $cur;
    }

    /**
     * @param Int $price
     */
    public function setMyPrice(Int $price)
    {
        $this->myPrice = $price;
    }

    /**
     * @return string
     */
    public function getConvertedPrice()
    {
        // Turning on all the methods
        self::setUserDetails();
        self::setUserCurrencyRate();
        // Formula : price * userCurrencyRate
        $this->userConvertedPrice = $this->myPrice * $this->userCurrencyRate;
        return number_format($this->userConvertedPrice, 2);
    }

    /**
     * @return String
     */
    public function getMyCurrencyCode()
    {
        return $this->myCurrencyCode;
    }

    /**
     * @return Float
     */
    public function getMyPrice()
    {
        return $this->myPrice;
    }

    /**
     * @return String
     */
    public function getUserCurrencyRate()
    {
        self::setUserCurrencyRate();
        return $this->userCurrencyRate;
    }

    /**
     * @return mixed
     */
    public function getUserCurrencyCode()
    {
        self::setUserDetails();
        return $this->userCurrencyCode;
    }

    /**
     * @return mixed
     */
    public function getUserCurrencySymbol()
    {
        self::setUserDetails();
        return $this->userCurrencySymbol;
    }

    /**
     * @return mixed
     */
    public function getUserCountry()
    {
        self::setUserDetails();
        return $this->userCountry;
    }

    /**
     * @return mixed
     */
    public function getUserRegion()
    {
        self::setUserDetails();
        return $this->userRegion;
    }

    /**
     * @return mixed
     */
    public function getUserIP()
    {
        self::setUserDetails();
        return $this->userIPAddress;
    }

    /**
     * This will set all your user's details:
     * IP, Currency Symbol, Currency Code, Country, and Region
     */
    protected function setUserDetails()
    {
        // User's IP
        $proxyPublicIP = filter_var(getenv('REMOTE_ADDR'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        // Getting the user's Currency Symbol and Currency Code
        $geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$proxyPublicIP));
        // User's currency symbol
        $this->userCurrencySymbol = $geo['geoplugin_currencySymbol'] ?? '*';
        // User's currency code
        $this->userCurrencyCode = $geo['geoplugin_currencyCode'];
        // User's Country
        $this->userCountry = $geo['geoplugin_countryName'];
        // User's Region
        $this->userRegion = $geo['geoplugin_region'];
        // User's IP Add
        $this->userIPAddress = $proxyPublicIP;
    }

    /**
     * This is will convert your base currency to the user's currency
     *
     * For example, if you set your currency to GBP, and your user is from Hongkong
     * then setUserCurrencyRate() will call the API to get a conversion rate for 1GBP to HKD
     */
    protected function setUserCurrencyRate()
    {
        // Get the latest exchange rate from MyCurrency API (base is USD)
        $url = 'https://api.exchangeratesapi.io/latest?base='.$this->myCurrencyCode.'&symbols='.$this->userCurrencyCode;
        $retrievedData = json_decode(file_get_contents($url), true);
        // Your user's rate casted from string to float
        $this->userCurrencyRate = self::formatNumberNoRound($retrievedData['rates'][$this->userCurrencyCode]);
    }

    /**
     * @param $num
     * @return float
     */
    protected function formatNumberNoRound($num)
    {
        $step1 = $num * 100;
        $step2 = floor($step1);
        $result = (float)($step2 / 100);
        return $result;
    }
}
