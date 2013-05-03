<?php

if (!class_exists("pointRetraitAcheminementResult", false)) 
{
class pointRetraitAcheminementResult
{

  /**
   * 
   * @var int $errorCode
   * @access public
   */
  public $errorCode;

  /**
   * 
   * @var string $errorMessage
   * @access public
   */
  public $errorMessage;

  /**
   * 
   * @var pointRetraitAcheminement $listePointRetraitAcheminement
   * @access public
   */
  public $listePointRetraitAcheminement;

  /**
   * 
   * @var int $qualiteReponse
   * @access public
   */
  public $qualiteReponse;

  /**
   * 
   * @var string $wsRequestId
   * @access public
   */
  public $wsRequestId;

}

}
