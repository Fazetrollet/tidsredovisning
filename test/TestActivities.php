<?php

declare (strict_types=1);
require_once '../src/activities.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaActivityTester(): string {
    // Kom ihåg att lägga till alla funktioner i filen!
    $retur = "";
    $retur .= test_HamtaAllaAktiviteter();
    $retur .= test_HamtaEnAktivitet();
    $retur .= test_SparaNyAktivitet();
    $retur .= test_UppdateraAktivitet();
    $retur .= test_RaderaAktivitet();

    return $retur;
}

/**
 * Tester för funktionen hämta alla aktiviteter
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaAllaAktiviteter(): string {
    $retur = "<h2>test_HamtaAllaAktiviteter</h2>";
    try {
        $svar=hamtaAllaAktiviteter();
        if($svar->getStatus()===200){
            $retur .="<p class='ok'>Hämta alla aktiviteter lyckades, " . count($svar->getContent())
                    ." poster returnerades</>";
        } else {
            $retur .="<p class='error'>Hämta alla aktiviteter misslyckades<br>"
                    . $svar->getStatus() . "returnerades</p>";
        }
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Tester för funktionen hämta enskild aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaEnAktivitet(): string {
    $retur = "<h2>test_HamtaEnAktivitet</h2>";
    try{
    //Misslyckas hämta post id=-1
    $svar=hamtaEnskildAktivitet('-1');
    if($svar->getStatus()===400){
        $retur .="<p class='ok'>Hämta post med id=-1 misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>Hämta post med id=-1 returnerade " .$svar->getStatus()
        . " istället för förväntat 400</p>";
    }
    //Misslyckas hämta post id=0
    $svar=hamtaEnskildAktivitet('0');
    if($svar->getStatus()===400){
        $retur .="<p class='ok'>Hämta post med id=0 misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>Hämta post med id=0 returnerade " .$svar->getStatus()
        . " istället för förväntat 400</p>";
    }
    //Misslyckas hämta post id=3.14
    $svar=hamtaEnskildAktivitet('3.14');
    if($svar->getStatus()===400){
        $retur .="<p class='ok'>Hämta post med id=3.14 misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>Hämta post med id=3.14 returnerade " .$svar->getStatus()
        . " istället för förväntat 400</p>";
    }
    //Koppla databas
    $db=connectDb();

    //Skapa transaktion
    $db->beginTransaction();

    //Skapa ny post för att vara säker på att posten finns
    $svar=sparaNyAktivitet('Aktivitet' .time());
    if($svar->getStatus()===200){
        $nyttId=$svar->getContent()->id;
    } else {
        throw new Exception('Kunde inte skapa ny post för kontroll');
    }
    //Lyckas hämta skapad post
    $svar=hamtaEnskildAktivitet("$nyttId");
    if($svar->getStatus()===200){
        $retur .="<p class='ok'>Hämta en aktivitet gick bra</p>";
    } else {
        $retur .="<p class='error'>Hämta en aktivitet misslyckades, status" .$svar->getStatus()
        . "returnerades istället för förväntat 200</p>";
    }

    //Misslyckas med att hämta post med id +1
    $nyttId++;
    $svar=hamtaEnskildAktivitet("$nyttId");
    if($svar->getStatus()===400){
        $retur .="<p class='ok'>Hämta en aktivitet med id som saknas misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>Hämta en aktivitet misslyckades, status" .$svar->getStatus()
        . "returnerades istället för förväntat 400</p>";
    }


    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        //Återställ databasen
        if($db){
           $db->rollBack(); 
        }
    }

    return $retur;
}

/**
 * Tester för funktionen spara aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_SparaNyAktivitet(): string {
    $retur = "<h2>test_SparaNyAktivitet</h2>";

    $nyAktivitet="Aktivitet" . time();

    try {
    //Koppla databas
    $db=connectDb();

    //Starta transaktion
    $db->beginTransaction();

    //Spara tom aktivitet - Misslyckat
    $svar=sparaNyAktivitet('');
    if($svar->getStatus()===400){
        $retur .="<p class='ok'>Spara tom aktivitet misslyckades, som förväntat</p>";
    } else{
        "<p class='error'>Spara tom aktivitet misslyckades, status" . $svar->getStatus() . "returnerades istället för förväntat 400</p>";
    }

    //Spara ny aktivitet - Lyckat
    $svar=sparaNyAktivitet($nyAktivitet);
    if($svar->getStatus()===200){
        $retur .="<p class='ok'>Spara aktivitet lyckades</p>";
    } else{
        "<p class='error'>Spara aktivitet misslyckades, status" . $svar->getStatus() . "returnerades istället för förväntat 200</p>";
    }

    //Spara ny aktivitet - Misslyckat
    $svar=sparaNyAktivitet($nyAktivitet);
    if($svar->getStatus()===400){
        $retur .="<p class='ok'>Spara duplicerad aktivitet misslyckades, som förväntat</p>";
    } else{
        "<p class='error'>Spara duplicerad aktivitet misslyckades, status" . $svar->getStatus() . "returnerades istället för förväntat 400</p>";
    }
    
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        //Återställa databasen
        if($db){
            $db->rollBack();
        }
    }

    return $retur;
}

/**
 * Tester för uppdatera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_UppdateraAktivitet(): string {
    $retur = "<h2>test_UppdateraAktivitet</h2>";
    try {
        //kopla databaser
        $db=connectDb();
        //starta transaktion
        $db->beginTransaction();

    //misslyckas med att updatera id=-1
$svar=uppdateraAktivitet("-1", "aktivitet");
if($svar->getStatus()===400) {
    $retur .="<p class='ok'>uppdatera aktivitet med id=-1 misslyckades, som förväntat</p>";
} else {
    $retur .="<p class='error'>uppdatera aktivitet med id=-1 misslyckades, status" . $svar->getStatus()
        . "istället för förväntad 400</p>";
}
    //misslyckades med att uppdatera id=0
    $svar=uppdateraAktivitet("0", "aktivitet");
if($svar->getStatus()===400) {
    $retur .="<p class='ok'>uppdatera aktivitet med id=0 misslyckades, som förväntat</p>";
} else {
    $retur .="<p class='error'>uppdatera aktivitet med id=0 misslyckades, status" . $svar->getStatus()
        . "istället för förväntad 400</p>";
}
    //misslyckas med att uppdatera id=3.14
    $svar=uppdateraAktivitet("3.14", "aktivitet");
if($svar->getStatus()===400) {
    $retur .="<p class='ok'>uppdatera aktivitet med id=-3.14 misslyckades, som förväntat</p>";
} else {
    $retur .="<p class='error'>uppdatera aktivitet med id=-3.14 misslyckades, status" . $svar->getStatus()
        . "istället för förväntad 400</p>";
}
    //misslyckades med att uppdatera aktivitet=''
    $svar=uppdateraAktivitet("", "aktivitet");
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>uppdatera aktivitet med tom aktivitet misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>uppdatera aktivitet med med tom aktivitet misslyckades, status" . $svar->getStatus()
            . "istället för förväntad 400</p>";
    }
    //uppdatera med samma informaion misslyckades
    $aktivitet="aktivitet" . time();
    $svar= sparaNyAktivitet($aktivitet);
    if($svar->getStatus()===200) {
        $nyttId=$svar->getContent()->id;
    } else {
        throw new Exception("spara aktivitet för uppdatering misslyckades");
    }

    $svar= uppdateraAktivitet("$nyttId", $aktivitet);
    if($svar->getStatus()===200 && $svar->getContent()->result===false) {
        $retur .="<p class='ok'> uppdatera aktivitet med samma information misslyckades, som förväntat</p>";
    } else {
        $retur .="<p class='error'>uppdatera aktivitet med samma information misslyckades<br>"
        . "status:" . $svar->getStatus() . " returneras med följande innerhåll:<br>"
        . print_r($svar->getContent(), true) ."</br>";
    }
    //lyckas med att uppdatera aktivitet
    $svar=uppdateraAktivitet("$nyttId", "NY" . $aktivitet);
    if($svar->getStatus()===200 && $svar->getContent()->result===true) {
        $retur .="<p class='ok'>uppdatera aktivitet lyckades</p>";
    } else {
        $retur .="<p class='error'>uppdatera aktivitet misslyckades<br>"
        . "Status:" . $svar->getStatus() . "returneras med förljande innerhåll:<br>"
        . print_r($svar->getContent(), true) ."</p>";
    }
    //misslyckas med att uppdatera aktivitet som inte finns
    $nyttId++;
    $svar=uppdateraAktivitet("$nyttId", "What ever");
    if($svar->getStatus()===200 && $svar->getContent()->result===false) {
        $retur .="<p class='ok'>uppdatera aktivitet misslyckades som förväntat</p>";
    } else {
        $retur .="<p class='error'>uppdatera aktivitet misslyckades<br>"
        . "Status:" . $svar->getStatus() . "returneras med förljande innerhåll:<br>"
        . print_r($svar->getContent(), true) ;"</p>";
    }
    //misslyckas med att uppdatera aktivitet som redan finns
    
    $svar=sparaNyAktivitet($aktivitet);
    if($svar->getStatus()===200){
        $nyttId=$svar->getContent()->id;
    } else {
        throw new Exception("Spara aktivitet för uppdatering misslyckades");
    }
    $svar=uppdateraAktivitet("$nyttId", "NY" .$aktivitet);
    if($svar->getStatus()===400){
        $retur.="<p class='ok'>Uppdatera aktivitet till en redan befintlig misslyckades, som förväntat</p>";
    } else {
        $retur.="<p class='error'>Uppdatera aktivitet misslyckades<br>"
        . "Status: " .$svar->getStatus() . " returnerades med följande innehåll:<br>"
        .print_r($svar->getContent(), true);"</p>";
    } 
    
   
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally{
        //återställ databasen
        if($db) {
            $db->rollback();
        }
    }

    return $retur;
}

/**
 * Tester för funktionen radera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_RaderaAktivitet(): string {
    $retur = "<h2>test_RaderaAktivitet</h2>";
    try {
        //testa felaktigt id
        $svar= raderaAktivitet("-1");
        if($svar->getStatus()===400){
            $retur .="<p class='ok'>Rdera felaktigt id (-1) misslyckades som förväntat</p>";
        } else {
            $retur .="<p class='ok'>Radera aktivitet med felaktigt id (-1) misslyckades.<br>"
            . $svar->getStatus() . " returnerades istället som förväntat 400.</p>";
        }
        $svar= raderaAktivitet("0");
        if($svar->getStatus()===400){
            $retur .="<p class='ok'>Rdera felaktigt id (0) misslyckades som förväntat</p>";
        } else {
            $retur .="<p class='ok'>Radera aktivitet med felaktigt id (0) misslyckades.<br>"
            . $svar->getStatus() . " returnerades istället som förväntat 400.</p>";
        }
        $svar= raderaAktivitet("-3.14");
        if($svar->getStatus()===400){
            $retur .="<p class='ok'>Rdera felaktigt id (-3.14) misslyckades som förväntat</p>";
        } else {
            $retur .="<p class='ok'>Radera aktivitet med felaktigt id (-3.14) misslyckades.<br>"
            . $svar->getStatus() . " returnerades istället som förväntat 400.</p>";
        }
        //testa radera befintlig 
        $db=connectDb();
        $db->beginTransaction();
        $nyPost=sparaNyAktivitet("Ny aktivitet");
        if($nyPost->getStatus()===200) {
            $nyttId=$nyPost->getContent()->id;
        } else {
            throw new Exception("kan inte skapa ny aktivitet, tester avbryts");
        }
        
        $svar= raderaAktivitet("$nyttId");
        if($svar->getStatus()===200 && $svar->getContent()->result===true) {
            $retur .="<p class='ok'>Radera aktivitet fungerade</p>";
        } else {
            $retur .="<p class='error'>Radera aktivitet misslyckades.<br>"
            . $svar->getStatus() . " och " . print_r($svar->getContent(), true)
            . " returnerades istället som förväntat 200 och 'true'</p>";
        }
        $db->rollBack();
        // testa radera som inte finns
        $svar= raderaAktivitet("$nyttId");
        if($svar->getStatus()===200 && $svar->getContent()->result===false) {
            $retur .="<p class='ok'>Radera aktivitet som inte finns fungerade</p>";
        } else {
            $retur .="<p class='error'>Radera aktivitet som inte finns misslyckades.<br>"
            . $svar->getStatus() . " och " . print_r($svar->getContent()->result, true)
            . " returnerades istället som förväntat 200 och 'true'</p>";
        }


    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}