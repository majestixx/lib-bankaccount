<?php
namespace LibBankaccount;
use malkusch\bav\BankNotFoundException;
use malkusch\bav\BAV;
use malkusch\bav\ConfigurationRegistry;
use malkusch\bav\DataBackendException;
use malkusch\bav\UndefinedAttributeAgencyException;

class Account {
  protected $accountNo;
  protected $blz;
  protected $iban;
  protected $bic;


  /**
   * Account constructor.
   * @param Configuration $configuration
   */
  public function __construct($configuration) {
    ConfigurationRegistry::setConfiguration($configuration->getConfiguration());

    $this->accountNo = null;
    $this->blz = null;
    $this->iban = null;
    $this->bic = null;
  }

  /**
   * Get accountNo for account
   * @return bool|null|string AccountNo or false
   */
  public function getAccountNo() {
    if (!empty($this->accountNo)){
      return $this->accountNo;
    }
    elseif (!empty($this->iban)) {
      return iban_get_account_part($this->iban);
    }

    return false;
  }

  /**
   * Get Blz for account
   * @return bool|null|string Blz or false
   */
  public function getBlz() {
    if(!empty($this->blz)){
      return $this->blz;
    }
    elseif (!empty($this->iban)){
      return iban_get_bank_part($this->iban);
    }
    elseif (!empty($this->bic)){
      $databackPDO = new BAV();
      $agencies = $databackPDO->getBICAgencies($this->bic);

      foreach ($agencies as $agency) {
        return $agency->getBank()->getBankID();
      }
    }

    return false;
  }

  /**
   * Get IBAN for account
   * @return bool|null|string IBAN or false
   */
  public function getIban() {
    if(!empty($this->iban)){
      return $this->iban;
    }
    elseif (!empty($this->accountNo) && !empty($this->blz)){
      return $this->buildIban($this->accountNo, $this->getBlz());
    }
    elseif (!empty($this->accountNo) && !empty($this->bic)){
      $blz = $this->getBlz();
      return $this->buildIban($this->accountNo, $blz);
    }
    return false;
  }

  /**
   * Get BIC of Account
   * @return string
   * @throws BankNotFoundException
   * @throws DataBackendException
   * @throws UndefinedAttributeAgencyException
   */
  public function getBic() {
    if(!empty($this->bic)){
      return $this->bic;
    }
    elseif (!empty($this->blz)) {
      return $this->blzToBIC($this->blz);
    }
    elseif (!empty($this->iban)) {
      $blz = $this->getBlz(); // Get blz from iban
      return $this->blzToBIC($blz);
    }
    else {
      return null;
    }
  }

  /**
   * Get Name of Bank
   * @throws BankNotFoundException
   * @throws DataBackendException
   *
   * @return string
   */
  public function getBankName(){
  	$databackPDO = new BAV();
  	return $databackPDO->getBank($this->getBlz())->getMainAgency()->getName();
  }

  /**
   * Check, if accountNo is valid
   * @throws BankNotFoundException
   * @throws DataBackendException
   * @return boolean
   */
  public function validateAccountNo(){
    $databackPDO = new BAV();
    return $databackPDO->getBank($this->getBlz())->isValid($this->getAccountNo());
  }

  /**
   * Check, if blz is valid
   * @return boolean
   * @throws DataBackendException
   */
  public function validateBlz(){
    try {
      $databackPDO = new BAV();
      $bank = $databackPDO->getBank($this->getBlz());
      if ($bank->getBankID() == $this->getBlz())
        return true;
      else
        return false;
    } catch (BankNotFoundException $e) {
      return false;
    }
  }

  /**
   * Check, if bic is valid
   * @return boolean
   * @throws DataBackendException
   */
  public function validateBIC(){
    $databackPDO = new BAV();
    $agencies = $databackPDO->getBICAgencies($this->getBic());

    return count($agencies) > 0 ? true : false;
  }

  /**
   * Check, if iban is valid
   * @return boolean
   */
  public function validateIban(){
    return iban_verify_checksum($this->getIban());
  }

  /**
   * Set accountNo for account
   * @param string $accountNo
   */
  public function setAccountNo($accountNo) {
    $this->accountNo = rSpaces($accountNo);
  }

  /**
   * Set blz for account
   * @param string $blz
   */
  public function setBlz($blz) {
    $this->blz = rSpaces($blz);
  }

  /**
   * Set iban for account
   * @param string $iban
   */
  public function setIban($iban) {
    $this->iban = rSpaces($iban);
  }

  /**
   * Set bic for account
   * @param string $bic
   */
  public function setBic($bic) {
    $bic = rSpaces($bic);
    //Transform all Input to BIC-11
    if(strlen($bic) == 11)
      $this->bic = $bic;
    elseif (strlen($bic) == 8)
      $this->bic = $bic . "XXX";

    //TODO throw exception
  }

  /**
   * @param string $blz
   * @return string BIC
   * @throws BankNotFoundException
   * @throws DataBackendException
   * @throws UndefinedAttributeAgencyException
   *
   */
  protected function blzToBIC($blz) {
    $databackPDO = new BAV();
    return $databackPDO->getBank($blz)->getMainAgency()->getBIC();
  }

  /**
   * Build iban from account and blz
   * @param string $accountNo
   * @param string $blz
   * @return string
   */
  protected function buildIban($accountNo, $blz){
    $lk = "DE";
    $pz = "00";

    $tmpAcc = "0000000000";
    $accNo = substr($tmpAcc, 0, 10 - strlen($accountNo)) . $accountNo;

    $iban = $lk . $pz . $blz . $accNo;

    return iban_set_checksum($iban);
  }
}

/**
 * Remove spaces
 * @param $string
 * @return string
 */
function rSpaces($string){
  return str_replace(" ", "", $string);
}
