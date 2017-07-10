<?php
namespace LibBankaccount;

use malkusch\bav\AutomaticUpdatePlan;
use malkusch\bav\ConfigurationRegistry;
use malkusch\bav\DefaultConfiguration;
use malkusch\bav\PDODataBackendContainer;
use PDO;

define("DB_URL", "localhost");
define("DB_USERNAME", "user");
define("DB_PASSWD", "pass");
define("DB_NAME", "bankaccount");

$configuration = new DefaultConfiguration();

$pdo = new PDO('mysql:host='.DB_URL.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWD);
$configuration->setDataBackendContainer(new PDODataBackendContainer($pdo));

$configuration->setUpdatePlan(new AutomaticUpdatePlan());

ConfigurationRegistry::setConfiguration($configuration);