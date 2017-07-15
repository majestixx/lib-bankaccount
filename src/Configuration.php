<?php
namespace LibBankaccount;

use malkusch\bav\AutomaticUpdatePlan;
use malkusch\bav\DefaultConfiguration;
use malkusch\bav\PDODataBackendContainer;
use PDO;

class Configuration
{
  /**
   * @var string
   */
  protected $dbURL;

  /**
   * @var string
   */
  protected $dbUser;

  /**
   * @var string
   */
  protected $dbPassword;

  /**
   * @var string
   */
  protected $dbName;

  /**
   * @var string
   */
  protected $dbPrefix;

  /**
   * Configuration constructor.
   * @param string $dbURL
   * @param string $dbUser
   * @param string $dbPassword
   * @param string $dbName
   * @param string $dbPrefix
   */
  public function __construct($dbURL, $dbUser, $dbPassword, $dbName, $dbPrefix = "bav_")
  {
    $this->dbURL = $dbURL;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;
    $this->dbName = $dbName;
    $this->dbPrefix = $dbPrefix;
  }

  /**
   * @return DefaultConfiguration
   */
  public function getConfiguration() {
    $configuration = new DefaultConfiguration();

    $pdo = new PDO('mysql:host='.$this->getDbURL().';dbname='.$this->getDbName(), $this->getDbUser(), $this->getDbPassword());
    $backendContainer = new PDODataBackendContainer($pdo, $this->getDbPrefix());
    if(!$backendContainer->getDataBackend()->isInstalled()) {
      $backendContainer->getDataBackend()->install();
    }

    $configuration->setDataBackendContainer($backendContainer);

    $configuration->setUpdatePlan(new AutomaticUpdatePlan());

    return $configuration;
  }

  /**
   * @return string
   */
  public function getDbURL(): string
  {
    return $this->dbURL;
  }

  /**
   * @param string $dbURL
   */
  public function setDbURL(string $dbURL)
  {
    $this->dbURL = $dbURL;
  }

  /**
   * @return string
   */
  public function getDbUser(): string
  {
    return $this->dbUser;
  }

  /**
   * @param string $dbUser
   */
  public function setDbUser(string $dbUser)
  {
    $this->dbUser = $dbUser;
  }

  /**
   * @return string
   */
  public function getDbPassword(): string
  {
    return $this->dbPassword;
  }

  /**
   * @param string $dbPassword
   */
  public function setDbPassword(string $dbPassword)
  {
    $this->dbPassword = $dbPassword;
  }

  /**
   * @return string
   */
  public function getDbName(): string
  {
    return $this->dbName;
  }

  /**
   * @param string $dbName
   */
  public function setDbName(string $dbName)
  {
    $this->dbName = $dbName;
  }

  /**
   * @return string
   */
  public function getDbPrefix(): string
  {
    return $this->dbPrefix;
  }

  /**
   * @param string $dbPrefix
   */
  public function setDbPrefix(string $dbPrefix)
  {
    $this->dbPrefix = $dbPrefix;
  }
}
