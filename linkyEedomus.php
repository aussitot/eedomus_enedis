<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

require("phpLinkyAPI.php"); //Linky custom API
require("log.class.php"); //log class

//--------------------------------------------- Paramètres enedis
$enedis_user = 'login enedis'; //votre login pour le site https://espace-client-particuliers.enedis.fr/group/espace-particuliers/accueil
$enedis_pass = 'password enedis'; //votre password pour le site https://espace-client-particuliers.enedis.fr/group/espace-particuliers/accueil

//--------------------------------------------- Paramètres eedomus
$api_user = 'wwwwwwwwww'; //api_user eedomus
$api_secret = 'zzzzzzzzzzzzzzzz'; //api_secret eedomus

//------- Etats de sauvegarde
$capteurCumul = '1231788'; //code api eedomus de l etat cumul
$capteurConso = '1231789'; //code api eedomus de l etat conso

$log = new Log("linky.log");

//-------------- Parametres
if (isset($_GET['mode'])) {$mode = $_GET['mode'];} else {$mode=0;} //si mode =1 on force la mise à jour
if (isset($_GET['conso'])) $conso = $_GET['conso'];
if (isset($_GET['cumul'])) $cumul = $_GET['cumul'];

/* ----Gestion des cookies----------*/
function saveVariable($variable_name, $variable_content, $log) {
  $log->eraseLog();
  return $log->ecrireLog($variable_content);
  //return setcookie($variable_name, $variable_content, time()+3600*24*7);  /* expire dans 7 jours */
}

function loadVariable($variable_name, $log) {
  //return $_COOKIE[$variable_name];
  return $log->lireLog();
}
/* ----------------------------------*/

//____Initialize datas:
$timezone = 'Europe/Paris';
$today = new DateTime('NOW', new DateTimeZone($timezone));
$today->sub(new DateInterval('P1D')); //Enedis last data are yesterday
$yesterday = $today->format('d/m/Y');

if ($mode ==1) //on force la màj
  {
    //Envoi des informations à eedomus
    $majconso = "https://api.eedomus.com/set?api_user=".$api_user."&api_secret=".$api_secret."&action=periph.value&periph_id=".$capteurConso."&value=".$conso;
    $contents = file_get_contents($majconso);
    $majcumul = "https://api.eedomus.com/set?api_user=".$api_user."&api_secret=".$api_secret."&action=periph.value&periph_id=".$capteurCumul."&value=".$cumul;
    $contents = file_get_contents($majcumul);
    saveVariable('last', $today->format('d/m/Y')."|".$cumul, $log);

    $eestatus = "<root>";
    $eestatus .= "<mode>1-force</mode>";
    $eestatus .= "<conso>".$conso."</conso>";
    $eestatus .= "<cumul>".$cumul."</cumul>";
    $eestatus .= "</root>";
    echo $eestatus;

  } else {
    $_Linky = new Linky($enedis_user, $enedis_pass, false);
    if (isset($_Linky->error)) echo '__ERROR__: ', $_Linky->error, "<br>";


    //Consommation par jour:
    //____Get per day
    $var = clone $today;
    $fromMonth = $var->sub(new DateInterval('P30D'));
    $fromMonth = $fromMonth->format('d/m/Y');
    $data = $_Linky->getData_perday($fromMonth, $yesterday);

    //recupération de la valeur du cumul stocké en cookie
    $lastvalue = explode("|",loadVariable('last',$log));
    $lastvaluedate = $lastvalue[0];
    $lastvalueindex = $lastvalue[1];

    //On sauvegarde la dernière valeur
    if (($lastvaluedate != $today->format('d/m/Y')) && ($lastvalueindex != substr($data[$yesterday], 0, -3)) && ($data[$yesterday] !=0) && ($data[$yesterday] !=""))
    {
    	$cumul = $lastvalueindex + substr($data[$yesterday], 0, -3);
    	saveVariable('last', $today->format('d/m/Y')."|".$cumul, $log);

      //Envoi des informations à eedomus
      $majconso = "https://api.eedomus.com/set?api_user=".$api_user."&api_secret=".$api_secret."&action=periph.value&periph_id=".$capteurConso."&value=".substr($data[$yesterday], 0, -3);
      $contents = file_get_contents($majconso);
      $majcumul = "https://api.eedomus.com/set?api_user=".$api_user."&api_secret=".$api_secret."&action=periph.value&periph_id=".$capteurCumul."&value=".$cumul;
      $contents = file_get_contents($majcumul);

      $eestatus = "<root>";
      $eestatus .= "<mode>0-calcul-Transmit</mode>";
      $eestatus .= "<lastdate>".$lastvaluedate."</lastdate>";
      $eestatus .= "<lastvalue>".$lastvalueindex."</lastvalue>";
      $eestatus .= "<conso>".substr($data[$yesterday], 0, -3)."</conso>";
      $eestatus .= "<cumul>".$cumul."</cumul>";
      $eestatus .= "</root>";
      echo $eestatus;
    } else {
      $cumul = $lastvalueindex + substr($data[$yesterday], 0, -3);

      $eestatus = "<root>";
      $eestatus .= "<mode>0-calcul-NOTransmit</conso>";
      $eestatus .= "<conso>".substr($data[$yesterday], 0, -3)."</conso>";
      $eestatus .= "<cumul>".$cumul."</cumul>";
      $eestatus .= "</root>";
      echo $eestatus;
    }
  }


?>
