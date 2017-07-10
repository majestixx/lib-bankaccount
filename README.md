# lib-bankaccount
The lib-bankaccount offers the possiblity to verify and convert german bank accounts to iban and back.

## Installation
1. Download sources to your webserver
2. Install dependencies using composer
3. Create a configuration.php

## How to use

```
$accountNo = "402532800";
$blz = "49092650";

$account = new Account();
$account->setAccountNo($accountNo);
$account->setBlz($blz);

// Get IBAN and BIC for bank account
$account->getIban();
$account->getBic();

// Validate the input data
$account->validateAccountNo();
$account->validateBlz();
```

Additional usage examples are supplied in tests/accountTest.php.

## Open Issues
* Make available via packagist
* Check if configuration.php is usable, if used as a third party library: Maybe the configuration should not be in the autoload of the library but the using application
* On windows handling with files there is a unlink()-error