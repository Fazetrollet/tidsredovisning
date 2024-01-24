<?php

declare (strict_types=1);
require_once __DIR__ . '/Settings.php'; // Settings;

function connectDb(): PDO {
    static $db = null;

    if ($db === null) {
        // Hämta settings 
        $settings = new Settings();
        // Koppla mot databasen
        $dsn = $settings->dsn;
        $dbUser = $settings->dbUser;
        $dbPassword = $settings->dbPassword;
        $db = new PDO($dsn, $dbUser, $dbPassword);
    }

    return $db;
}

function kontrolleraIndata(array $postdata):array {
    $retur=[];
    //kontrollera datum['date']
$datum= DateTimeImmutable::createFromFormat("Y-m-d", $postdata['date'] ??'');
if(!$datum) {
    $retur[]="ogiltigt angivet datum";
}
if($datum && $datum->format('Y-m-d')!==$postdata['date']) {
    $retur[]="ogiltigt formaterat datum";
}
if($datum && $datum->format('Y-m-d')>date('Y-m-d')) {
    $retur[]="datum får inte vara framåt i tiden";
}
    //kontrollera tid['time*]
    $tid=DateTimeImmutable::createFromFormat('H:i', $postdata['time'] ??'');
    if(!$tid) {
        $retur[]="ogiltigt angiven tid";
    }
    if($tid && $tid->format('H:i')!==$postdata['time']) {
        $retur[]="felaktigt angiven tid";
    }
    if($tid && $tid->format('H:i')>"08:00") {
        $retur[]="du får inte rapportera mer än 8 timmar per aktivitet åt gången";
    }
    //kontrollera aktivitetsID['activityId']
    $aktivitet=hamtaEnskildAktivitet($postdata['activityId'] ??'');
    if($aktivitet->getStatus()===400) {
        $retur[]= "angivet aktivitets id saknas";
    }

    //Ta bort konstiga tecken från beskrivningen['description']

    return $retur;
}
