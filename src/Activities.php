<?php

declare (strict_types=1);
require_once __DIR__ . '/funktioner.php';

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function activities(Route $route, array $postData): Response {
    try {
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::GET) {
            return hamtaAllaAktiviteter();
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaEnskildAktivitet($route->getParams()[0]);
        }
        if (isset($postData["activity"]) && count($route->getParams()) === 0 &&
                $route->getMethod() === RequestMethod::POST) {
            return sparaNyAktivitet((string) $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::PUT) {
            return uppdateraAktivitet( $route->getParams()[0],  $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::DELETE) {
            return raderaAktivitet($route->getParams()[0]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Returnerar alla aktiviteter som finns i databasen
 * @return Response
 */
function hamtaAllaAktiviteter(): Response {
    //koppla mot databas
    $db= connectDb();
    //hämta alla aktiviteter
    $result = $db->query("SELECT id, namn From aktiviteter");

    //Skapa returvärden
    $retur=[];
    foreach ($result as $item) {
        $post=new stdClass();
        $post->id=$item['id'];
        $post->namn=$item['namn'];
        $retur[]=$post;

    }
    //skicka svar
    return new Response($retur);
}

/**
 * Returnerar en enskild aktivitet som finns i databasen
 * @param string $id Id för aktiviteten
 * @return Response
 */
function hamtaEnskildAktivitet(string $id): Response {
    //kontrollera inparameter
    $kontrolleratId= filter_var($id, FILTER_VALIDATE_INT);

    if($kontrolleratId=== false || $kontrolleratId<1) {
        $retur=new stdClass();
        $retur->error=['bad request', 'ogiltigt id'];
        return new response($retur, 400);
    }
    //koppla mot databsen  
    $db= connectDb();

    //skicka fråga
$stmt=$db->prepare("SELECT id, namn FROM aktiviteter WHERE id=:id");
$result=$stmt->execute(['id'=>$kontrolleratId]);
    //kontrollera svar
    if($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $retur=new stdClass();
        $retur->id=$row['id'];
        $retur->activity=$row['namn'];
        return new Response($retur);
    } else {
        $retur=new stdClass();
        $retur->error=['Bad request', "angivet id ($kontrolleratId) finns inte"];
        return new response($retur, 400);
    }
}


/**
 * Lagrar en ny aktivitet i databasen
 * @param string $aktivitet Aktivitet som ska sparas
 * @return Response
 */
function sparaNyAktivitet(string $aktivitet): Response {
    //kontrollera indata - rensa bort onödiga tecken
$kontrolleradAktivitet=filter_var($aktivitet, FILTER_SANITIZE_SPECIAL_CHARS);
//kontrollerar att aktiviteten inte är tom
if(trim($aktivitet)===''){
    $retur=new stdClass();
    $retur->error=['Bad request', 'aktivitet får inte vara tom'];
    return new response($retur, 400);
}

    try {
//koppla mot databasen 
    $db=connectDb();
    //exekvera frågan
    $stmt=$db->prepare("INSERT INTO aktiviteter (namn) values (:aktiviteter)");
    $svar=$stmt->execute(['aktiviteter'=>$kontrolleradAktivitet]);

    //kontrollera svaret och returnerar svaret
    if ($svar===true) {
        $retur=new stdClass();
        $retur->id=$db->lastInsertId();
        $retur->meddelande = ['Spara lyckades', '1 post lades till'];
        return new response($retur);
    } else {
        $retur=new stdClass();
        $retur->error=['Bad request', 'något gickfel vid spara'];
        return new response($retur, 400);
    }
}catch (exception $e){
    $retur=new stdClass();
    $retur->error = ['Bad request', 'fel vid spara', $e->getMessage()];
    return new response($retur, 400);
}
} 

/**
 * Uppdaterar angivet id med ny text
 * @param string $id Id för posten som ska uppdateras
 * @param string $aktivitet Ny text
 * @return Response
 */
function uppdateraAktivitet(string $id, string $aktivitet): Response {
//kontrollera indata
$kontrolleratId= filter_var($id, FILTER_VALIDATE_INT);
$kontrolleradAktivitet= filter_var($aktivitet, FILTER_SANITIZE_SPECIAL_CHARS);
$kontrolleradAktivitet=trim($kontrolleradAktivitet);

if($kontrolleratId===false || $kontrolleratId<1
        || $kontrolleradAktivitet==='') {
            $retur=new stdClass();
            $retur->error=['bad request','felaktig indata till uppdatera aktivitet'];
            return new response($retur, 400);
        }
        try {
//koppla databas
$db=connectDb();
//förbereda fråga
$stmt=$db->prepare("UPDATE aktiviteter SET namn=:aktivitet WHERE id=:id");
$stmt->execute(['aktivitet'=>$kontrolleradAktivitet, 'id'=>$kontrolleratId]);

//hantera svar
if($stmt->rowCount()===1) {
    $retur=new stdClass();
    $retur->result=true;
    $retur->message=['Uppdatera aktivitet lyckades', '1 rad uppdaterad'];
    return new response($retur);
} else {
    $retur=new stdClass();
    $retur->result=false;
    $retur->message=['uppdatera aktivitet misslyckades', 'ingen rad uppdaterades'];
    return new response($retur);
}

} catch (Exception $e) {
    $retur= new stdClass();
    $retur->error=['bad request', 'något gick fel vid databasanropet'
    , $e->getMessage()];
    return new Response($retur, 400);
}
}
/**
 * Raderar en aktivitet med angivet id
 * @param string $id Id för posten som ska raderas
 * @return Response
 */
function raderaAktivitet(string $id): Response {
    //kontrollera indata
    $kontrolleratId= filter_var($id, FILTER_VALIDATE_INT);
    if ($kontrolleratId === false || $kontrolleratId < 1) {
    $retur=new stdClass();
            $retur->error=['bad request','felaktig indata till uppdatera aktivitet'];
            return new response($retur, 400);
    }
    try {
    //koppla databas
    $db= connectDb();
    //exekvera sql
    $stmt=$db->prepare("DELETE FROM aktiviteter where id=:id");
    $stmt->execute(['id'=>$kontrolleratId]);
    //skicka svar
if($stmt->rowCount()===1) {
    $retur=new stdClass();
    $retur->result=true;
    $retur->message=['Radera lyckades', "1 post raderades från databasen"];
 
} else {
    $retur=new stdClass();
    $retur->result=false;
    $retur->message=['Radera misslyckades', "ingen post raderades från databasen"];
   
}
return new response ($retur);
    } catch (Exception $e) {
     
        $retur= new stdClass();
        $retur->error=['bad request', 'något gick fel vid databasanropet'
        , $e->getMessage()];
        return new Response($retur, 400);
    }
}