<?php

declare (strict_types=1);
require_once __DIR__ . '/activities.php';

/**
 * Hämtar en lista med alla uppgifter och tillhörande aktiviteter 
 * Beroende på indata returneras en sida eller ett datumintervall
 * @param Route $route indata med information om vad som ska hämtas
 * @return Response
 */
function tasklists(Route $route): Response {
    try {
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaSida($route->getParams()[0]);
        }
        if (count($route->getParams()) === 2 && $route->getMethod() === RequestMethod::GET) {
            return hamtaDatum($route->getParams()[0], $route->getParams()[1]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function tasks(Route $route, array $postData): Response {
    try {
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaEnskildUppgift($route->getParams()[0]);
        }
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::POST) {
            return sparaNyUppgift($postData);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::PUT) {
            return uppdateraUppgift( $route->getParams()[0], $postData);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::DELETE) {
            return raderaUppgift($route->getParams()[0]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }
}

/**
 * Hämtar alla uppgifter för en angiven sida
 * @param string $sida
 * @return Response
 */
function hamtaSida(string $sida, int $posterPerSida=10): Response {

    //kontrollera indata
$sidnummer= filter_var($sida, FILTER_VALIDATE_INT);
if($sidnummer<1) {
    $retur=new stdClass();
    $retur->error=["Bad request", "Felaktigt angivet datum"];
    return new Response($retur, 400);
}
    //koppla databas
    $db=connectDb();
    //hämta poster
    $stmt=$db->query("SELECT COUNT(*) FROM uppgifter");
    $antalPoster=$stmt->fetchColumn();
    if(!$antalPoster){
        $retur=new stdClass();
        $retur->error=["inga poster kunde hittas"];
        return new Response($retur, 400);
    }
    $antalSidor=ceil($antalPoster/$posterPerSida);
    if($sidnummer>$antalSidor){
        $retur=new stdClass();
        $retur->error=["bad request", "felaktigt sidnummer", "det finns bara $antalSidor sidor"];
        return new Response($retur, 400);
   
}
$forstaPost=($sidnummer-1)*$posterPerSida;
 $stmt=$db->query("SELECT u.id, datum, tid ,beskrivning, aktivitetId, namn "
. "FROM uppgifter u INNER JOIN aktiviteter a on aktivitetid=a.id "
. "order by datum "
. "limit $forstaPost, $posterPerSida");
$result=$stmt->fetchAll();
$uppgifter=[];
foreach($result as $row){
    $rad=new stdClass();
    $rad->id=$row['id'];
    $rad->activityId=$row['aktivitetId'];
    $rad->date=$row['datum'];
    $tid=new DateTime($row['tid']);
    $rad->time=$tid->format("H:i");
    $rad->activity=$row['namn'];
    $rad->description=$row['beskrivning'];
    $uppgifter[]=$rad;
}
 //returnera svar

 $retur=new stdClass();
 $retur->pages=$antalSidor;
 $retur->tasks=$uppgifter;

 return new response($retur);
}
/**
 * Hämtar alla poster mellan angivna datum
 * @param string $from
 * @param string $tom
 * @return Response
 */
function hamtaDatum(string $from, string $tom): Response {
    
}

/**
 * Hämtar en enskild uppgiftspost
 * @param string $id Id för post som ska hämtas
 * @return Response
 */
function hamtaEnskildUppgift(string $id): Response {
    
}

/**
 * Sparar en ny uppgiftspost
 * @param array $postData indata för uppgiften
 * @return Response
 */
function sparaNyUppgift(array $postData): Response {
    
}

/**
 * Uppdaterar en angiven uppgiftspost med ny information 
 * @param string $id id för posten som ska uppdateras
 * @param array $postData ny data att sparas
 * @return Response
 */
function uppdateraUppgift(string $id, array $postData): Response {
    
}

/**
 * Raderar en uppgiftspost
 * @param string $id Id för posten som ska raderas
 * @return Response
 */
function raderaUppgift(string $id): Response {
    
}
