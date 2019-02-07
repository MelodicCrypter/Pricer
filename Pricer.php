<?php

/**
 * The purpose of this class is to set a fixed price for your product or service. First, you need
 * to convert the base price which is from (any currency, e.g, GBP) to the user's currency.
 * Process is simply to get the rate first at Fixer.io API for GBP-USD
 * and then secondly, get the user's currency using MyCurrency.net API which base is USD.
 * All of these are not needed if you have a subscription to currency APIs, however, they are too
 * costly.
 *
 * So (result from Fixer API multiplied by the result of MyCurrency API) then multiplied by the Service Price
 */
 
class Pricer
{
    protected $servicePrice;
    protected $gbpToUSDRate;
    protected $userCurrencySymbol;
    protected $userCurrencyCode;
    protected $userCurrencyRate;
    protected $userConvertedPrice;
    protected $userIPAddress;
    protected $userCountry;
    protected $userRegion;

    /**
     * @param Int $price
     */
    public function setServicePrice(Int $price)
    {
        $this->servicePrice = $price;
    }

    /**
     * @return string
     */
    public function getConvertedPrice()
    {
        // Turning on all the methods
        self::setUserCurrencySymbolAndCode();
        self::setUserCurrencyRate();
        self::setGBPToUSDRate();

        // Formula ( ($this->gbpToUSDRate * $this->userCurrencyRate) * $this->servicePrice
        $this->userConvertedPrice = ($this->gbpToUSDRate * $this->userCurrencyRate) * $this->servicePrice;
        return number_format($this->userConvertedPrice, 2);
    }

    /**
     * @return mixed
     */
    public function getUserCurrencyCode()
    {
        self::setUserCurrencySymbolAndCode();
        return $this->userCurrencyCode;
    }


    /**
     * @return mixed
     */
    public function getUserCurrencySymbol()
    {
        self::setUserCurrencySymbolAndCode();
        return $this->userCurrencySymbol;
    }

    /**
     * @return mixed
     */
    public function getUserCountry()
    {
        self::setUserCurrencySymbolAndCode();
        return $this->userCountry;
    }

    /**
     * @return mixed
     */
    public function getUserRegion()
    {
        self::setUserCurrencySymbolAndCode();
        return $this->userRegion;
    }

    /**
     * @return mixed
     */
    public function getUserIP()
    {
        self::setUserCurrencySymbolAndCode();
        return $this->userIPAddress;
    }


    /**
     * 
     */
    protected function setUserCurrencySymbolAndCode()
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
     *
     */
    protected function setUserCurrencyRate()
    {
        // Get the latest exchange rate from MyCurrency API (base is USD)
        $url = 'http://www.mycurrency.net/service/rates';
        $retrievedAllRates = json_decode(file_get_contents($url), true);

        // Search the user's code in the $retrievedAllRates array
        foreach ($retrievedAllRates as $index => $rate) {
            if (in_array($this->userCurrencyCode, (array)$rate['currency_code'])) {
                // The currency rate of the user casted as float
                $implodedUserCurrencyRate  = implode('', (array)$retrievedAllRates[$index]['rate']);
                $this->userCurrencyRate = self::formatNumberNoRound($implodedUserCurrencyRate);
            }
        }
    }


    /**
     *
     */
    protected function setGBPToUSDRate()
    {
        // Get the latest exchange rate for USD from Fixer API (base is GBP)
        $url = 'http://api.fixer.io/latest?base=GBP&symbols=GBP,USD';
        $retrievedGBPRate = json_decode(file_get_contents($url), true);
        // Get the retrieved rate of 1 GBP to USD
        foreach ($retrievedGBPRate as $index => $r) {
            if (array_key_exists('USD', (array)$r)) {
                // The GBP to USD rate casted as float
                $implodedGBPtoUSDRate  = implode('', (array)$r['USD']);
                $this->gbpToUSDRate = self::formatNumberNoRound($implodedGBPtoUSDRate);
            }
        }
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
