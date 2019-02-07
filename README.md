# Pricer
A PHP Class (tool) I created in order to set a fixed and auto-converted price for your products or services regardless of your user's currency. 😎 All you have to do is set your currency and your price, and let *Pricer* do the conversion.

For an instance, you are creating a project (site or online store) and your client is from United Kingdom. And your client is selling products or services wolrdwide (e.g, web maintenance, items, etc.) If a visitor from Japan wants to buy from your client, he/she should see the prices in Yen (that is Japan's currency - JPY). 

## How to use
First, create an instance of the Pricer class then declare your currency and price
```
$item = new Pricer();

// Set your currency
$item->setMyCurrencyCode('GBP');

// Set your price for the specific item or service, e.g, 40 pounds
$item->setMyPrice(40);

// You can now set your price for the item and it will be automatically converted
echo $item->getUserCurrencySymbol() . ' ' . $item->getConvertedPrice(); 
```
The code above will output: ¥ 5,692.40. At the time of this writing, 1GBP to JPY is equal to ¥ 142.31. That's how you can setup Pricer. All you need to setup is your currency code and your price. You can now then add another item and create a new instace of Pricer and so on. Pricer will automatically detect your visitor's IP address, Currency Symbol, Currency Code, Country, and Region. So everything is done automatically in the background for you.

## Other Methods of Pricer
```
// This will return your own currency code
echo $item->getMyCurrencyCode();

// This will return the price you set above
echo $item->getMyPrice();

// This will return your user's currency rate => e.g, ¥ 142.31 (based on 1GBP to JPY)
echo $item->getUserCurrencyRate();

// This will return your user's currency code => e.g, JPY for Japan
echo $item->getUserCurrencyCode();

// This will return your user's currency symbol => e.g, ¥ for Japan
echo $item->getUserCurrencySymbol();

// This will return your user's country
echo $item->getUserCountry(); 

// This will return your user's region
echo $item->getUserRegion(); 

// This will return your user's IP address
echo $item->getUserIP(); 
```

## APIs Used
[Exchange Rates API](https://exchangeratesapi.io/)
It is a free service for current and historical foreign exchange rates. If you can afford a subscription, I would suggest you use [Fixer](https://fixer.io/) for they have lots of amazing APIs. I use this API to fetch all latest conversion rates.

The next one is:

[GeoPlugin](http://www.geoplugin.com/)
It's a great API for geolocating your visitor. So basically, I use this for fetching the user's IP address, country, etc.
