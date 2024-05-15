<?php

declare (strict_types=1);
require_once __DIR__ . '/../src/tasks.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaTaskTester(): string {
// Kom ihåg att lägga till alla testfunktioner
    $retur = "<h1>Testar alla uppgiftsfunktioner</h1>";
    $retur .= test_HamtaEnUppgift();
    $retur .= test_HamtaUppgifterSida();
    $retur .= test_RaderaUppgift();
    $retur .= test_SparaUppgift();
    $retur .= test_UppdateraUppgifter();
    return $retur;
}

/**
 * Tester för funktionen hämta uppgifter för ett angivet sidnummer
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaUppgifterSida(): string {
    $retur = "<h2>test_HamtaUppgifterSida</h2>";
    try {
    //misslyckades med att hämta sida -1
$svar=hamtaSida("-1");
if($svar->getStatus()===400) {
    $retur .="<p class='ok'>misslyckades med att hämta sida -1 som förväntat</p>";
} else {
    $retur .="<p class='error'>misslyckat test att hämta sida -1<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
}
    //misslyckades med att hämta sida 0
    $svar=hamtaSida("0");
if($svar->getStatus()===400) {
    $retur .="<p class='ok'>misslyckades med att hämta sida 0 som förväntat</p>";
} else {
    $retur .="<p class='error'>misslyckat test att hämta sida 0<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
}
    //misslyckades med att hämta sida sju
    $svar=hamtaSida("sju");
    if($svar->getStatus()===400) {
        $retur .="<p class='ok'>misslyckades med att hämta sida <i>sju</i> som förväntat</p>";
    } else {
        $retur .="<p class='error'>misslyckat test att hämta sida <i>sju</i><br>"
        . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
    }
    //lyckades med att hämta sida 1
    $svar=hamtaSida("1");
if($svar->getStatus()===200) {
    $retur .="<p class='ok'>lyckades med att hämta sida 1</p>";
    $sista=$svar->getContent()->pages;
} else {
    $retur .="<p class='error'>misslyckat test att hämta sida 1<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
}
    //misslyckades med att hämta sida > antal sidor
    if(isset($sista)) {
        $sista++;
        $svar= hamtaSida("$sista");
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'>lyckades med att hämta sida >antal sidor, som förväntat</p>";
        } else {
            $retur .="<p class='error'>misslyckat test att hämta sida > antal sidor<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
        }
    }




    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test för funktionen hämta uppgifter mellan angivna datum
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaAllaUppgifterDatum(): string {
    $retur = "<h2>test_HamtaAllaUppgifterDatum</h2>";
    try {
        //misslyckas med från=igår till 2024-01-01
    $svar= hamtaDatum('igår', '2024-01-01');
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> misslyckades med att hämta poster mellan <i>igår</i> och 2024-01-01</p>";
        } else {
            $retur .="<p class='error'>misslyckat test med att hämta poster mellan <i>igår</i> och 2024-01-01<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
        }
        //misslyckas med från=2024-01-01 till i morgon
        $svar= hamtaDatum('2024-01-01', 'imorgon');
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> misslyckades med att hämta poster mellan 2024-01-01 och <i>imorgon</i></p>";
        } else {
            $retur .="<p class='error'>misslyckat test med att hämta poster mellan 2024-01-01 och <i>imorgon</i><br>"
            . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
        }
        //misslyckas med från=2024-12-37 till 2024-01-01
        $svar= hamtaDatum('2024-12-37', '2024-01-01');
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> misslyckades med att hämta poster mellan 2024-12-37 och 2024-01-01</p>";
        } else {
            $retur .="<p class='error'>misslyckat test med att hämta poster mellan 2024-12-37 och 2024-01-01<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
        }
        //misslyckas med från=2024-01-01 till 2024-01-37
        $svar= hamtaDatum('2024-01-01', '2024-01-37');
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> misslyckades med att hämta poster mellan 2024-01-01 och 2024-01-07</p>";
        } else {
            $retur .="<p class='error'>misslyckat test med att hämta poster mellan 2024-01-01 och 2024-01-37<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
        }
        //misslyckas med från=2024-01-01 till 2023-01-01
        $svar= hamtaDatum('2024-01-01', '2023-01-01');
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> misslyckades med att hämta poster mellan 2024-01-01 och 2023-01-01</p>";
        } else {
            $retur .="<p class='error'>misslyckat test med att hämta poster mellan 2024-01-01 och 2023-01-01<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
        }
        //misslyckas med från=igår till 2024-01-01
        $svar= hamtaDatum('igår', '2024-01-01');
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> misslyckades med att hämta poster mellan <i>igår</i> och 2024-01-01</p>";
        } else {
            $retur .="<p class='error'>misslyckat test med att hämta poster mellan <i>igår</i>och 2024-01-01<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 400</p>";
        }
        //lyckas med korrekta datum
$db= connectDb();
$stmt=$db->query("SELECT YEAR(datum), MONTH(datum), COUNT(*) AS antal "
       . "FROM uppgifter "
       . "GROUP BY YEAR(datum), MONTH(datum) "
       . "ORDER BY antal DESC "
       . "LIMIT 0,1 " );
       $row=$stmt->fetch();
    $ar=$row[0];
    $manad=substr("0$row[1]",-2);
    $antal=$row[2];

    //hämta alla poster från den funna månaden
    $svar= hamtaDatum("$ar-$manad-01", date('Y-m-d', strtotime("Last day of $ar-$manad")));
        if($svar->getStatus()===200 && count($svar->getContent()->tasks)===$antal) {

            $retur .="<p class='ok'> lyckades hämta $antal poster för månad $ar-$manad</p>";
        } else {
            $retur .="<p class='error'>misslyckades med att hämta poster för $ar-$manad<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 200<br>"
            . print_r($svar->getContent(), true) . "</p>";
        }


    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test av funktionen hämta enskild uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaEnUppgift(): string {
    $retur = "<h2>test_HamtaEnUppgift</h2>";

    try {
       //misslyckades med att hämta id=0
       $svar= hamtaEnskildUppgift("0");
       if($svar->getStatus()===400) {
        $retur .="<p class='ok'>misslyckades hämta uppgift med id=0, som förväntat";
    } else {
        $retur .="<p class'error'>misslyckades med att hämta uppgift med id=0<br>"
        . $svar->getStatus(). " returnerades istället för förväntat 400<br>"
        . print_r($svar->getContent(), true) . "</p>";
       }
        //misslyckades med att hämta id=sju
        $svar= hamtaEnskildUppgift("sju");
        if($svar->getStatus()===400) {
         $retur .="<p class='ok'>misslyckades hämta uppgift med id=sju, som förväntat";
     } else {
         $retur .="<p class'error'>misslyckades med att hämta uppgift med id=sju<br>"
         . $svar->getStatus(). " returnerades istället för förväntat 400<br>"
         . print_r($svar->getContent(), true) . "</p>";
        }
        //misslyckades med att hämta id=3.14
        $svar= hamtaEnskildUppgift("3.14");
        if($svar->getStatus()===400) {
         $retur .="<p class='ok'>misslyckades hämta uppgift med id=3.14, som förväntat";
     } else {
         $retur .="<p class'error'>misslyckades med att hämta uppgift med id=3.14<br>"
         . $svar->getStatus(). " returnerades istället för förväntat 400<br>"
         . print_r($svar->getContent(), true) . "</p>";
        }
    /*
    * lyckats med att hämta id som finns
    */
    //koppla databas
    $db=connectDb();
    $db->beginTransaction();

    //förbered data post
    $content=hamtaAllaAktiviteter()->getContent();
        $aktiviteter=$content['activities'];
       $aktivitetId=$aktiviteter[0]->id;
    $postdata=["date"=> date('Y-m-d'),
        "time"=>"01:00",
        "description"=>"Testpost",
        "activityId"=>"$aktivitetId" ];

        //skapa post
        $svar= sparaNyUppgift($postdata);
        $taskId=$svar->getContent()->id;

        //hämta nyss skapad post
        $svar= hamtaEnskildUppgift("$taskId");
        if($svar->getStatus()===200) {
         $retur .="<p class='ok'>lyckades hämta en uppgift";
     } else {
         $retur .="<p class'error'>misslyckades hämta nyskapad uppgift<br>"
         . $svar->getStatus(). " returnerades istället för förväntat 400<br>"
         . print_r($svar->getContent(), true) . "</p>";
        }

        //misslyckas med att hämta id som inte finns
        $taskId++;
 $svar= hamtaEnskildUppgift("$taskId");
        if($svar->getStatus()===400) {
         $retur .="<p class='ok'>misslyckades med att hämta en uppgift som inte finns";
     } else {
         $retur .="<p class'error'>misslyckades hämta uppgift som inte finns<br>"
         . $svar->getStatus(). " returnerades istället för förväntat 400<br>"
         . print_r($svar->getContent(), true) . "</p>";
     }

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        if($db) {
            $db->rollBack();
        }
    }

    return $retur;
}

/**
 * Test för funktionen spara uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_SparaUppgift(): string {
    $retur = "<h2>test_SparaUppgift</h2>";

    try {
        $db= connectDb();

        $db->beginTransaction();

        $postdata=['time'=>'01:00',
        'date'=>'2023-12-31',
        'description'=>'detta är en testpost'];

        $svar=sparaNyUppgift($postdata);
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'>misslyckades med att spara utan aktivitetid, som förväntat";
        } else {
            $retur .="<p class'error'>misslyckades med att spara post aktivitetid<br>"
            . $svar->getStatus(). " returnerades istället för förväntat 400<br>"
            . print_r($svar->getContent(), true) . "</p>";
        }

        //lyckades med att spara utan beskrivning
        $content=hamtaAllaAktiviteter()->getContent();
        $aktiviteter=$content['activities'];
       $aktivitetId=$aktiviteter[0]->id;
       $postdata=['time'=>'01:00',
       'date'=>'2023-12-31',
       'activityId'=>"$aktivitetId"];

       //testa
       $svar= sparaNyUppgift($postdata);
       if($svar->getStatus()===200)  {
       $retur .="<p class='ok'>lyckades med att spara uppgift utan beskrivning";
    } else {
        $retur .="<p class'error'>misslyckades med att spara uppgift utan beskrivning<br>"
        . $svar->getStatus(). " returnerades istället för förväntat 200<br>"
        . print_r($svar->getContent(), true) . "</p>";
    }
        //lyckas spara post med alla uppgifter

$postdata['description']="detta är en testpost";
    $svar= sparaNyUppgift($postdata);
    if($svar->getStatus()===200)  {
        $retur .="<p class='ok'>lyckades med att spara uppgift med alla uppgifter";
     } else {
         $retur .="<p class'error'>misslyckades med att spara uppgift med alla uppgifter<br>"
         . $svar->getStatus() . " returnerades istället för förväntat 200<br>"
         . print_r($svar->getContent(), true) . "</p>";
     }

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        if($db) {
            $db->rollback();
        }
    }

    return $retur;
}

/**
 * Test för funktionen uppdatera befintlig uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_UppdateraUppgifter(): string {
    $retur = "<h2>test_UppdateraUppgifter</h2>";

    try {
//koppla databas och skapa transaktion
$db= connectDb();
$db->beginTransaction();

//hämta postdata!
$svar=hamtaSida("1");
if($svar->getStatus()!=200) {
    throw new Exception('kunde inte hämta poster för test av uppdatera uppgift');
}
    $aktiviteter=$svar->getContent()->tasks;

//misslyckas med ogiltigt id=0
$postData=get_object_vars($aktiviteter[0]);
    $svar= uppdateraUppgift('0', $postData);
    if($svar->getStatus()===400)  {
        $retur .="<p class='ok'>misslyckades med att hämta post med id=0, som förväntat</p>";
     } else {
         $retur .="<p class'error'>misslyckades med att hämta post med id=0<br>"
         . $svar->getStatus() . " returnerades istället för förväntat 400<br>"
         . print_r($svar->getContent(), true) . "</p>";
     }
//misslyckas med ogiltigt id=sju
$svar= uppdateraUppgift('sju', $postData);
if($svar->getStatus()===400)  {
    $retur .="<p class='ok'>misslyckades med att hämta post med id=sju, som förväntat</p>";
 } else {
     $retur .="<p class'error'>misslyckades med att hämta post med id=sju<br>"
     . $svar->getStatus() . " returnerades istället för förväntat 400<br>"
     . print_r($svar->getContent(), true) . "</p>";
 }
//misslyckas med ogiltigt id=3.14
$svar= uppdateraUppgift('3.14', $postData);
if($svar->getStatus()===400)  {
    $retur .="<p class='ok'>misslyckades med att hämta post med id=3.14, som förväntat</p>";
 } else {
     $retur .="<p class'error'>misslyckades med att hämta post med id=3.14<br>"
     . $svar->getStatus() . " returnerades istället för förväntat 400<br>"
     . print_r($svar->getContent(), true) . "</p>";
 }
//lyckas med id som finns
$id=$postData['id'];
$postData['activityId']=(string) $postData['activityId'];
$postData['description'] =$postData['description'] . "(uppdaterad)";
$svar= uppdateraUppgift("$id", $postData);
if($svar->getStatus()===200 && $svar->getContent()->result===true) {
    $retur .="<p class='ok'>uppdatera uppgifter lyckdes, som förväntat</p>";
} else {
    $retur .="<p class'error'>uppdatera uppgifter misslyckades<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 200<br>"
    . print_r($svar->getContent(), true) . "</p>";
}
//misslyckas med id som inte finns
$svar= uppdateraUppgift("$id", $postData);
if($svar->getStatus()===200 && $svar->getContent()->result===false) {
    $retur .="<p class='ok'>uppdatera uppgifter misslyckades, som förväntat</p>";
} else {
    $retur .="<p class'error'>uppdatera uppgifter misslyckades<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 200<br>"
    . print_r($svar->getContent(), true) . "</p>";
}
// misslyckas med felaktig indata
$postData['time']='09:70';
$svar= uppdateraUppgift("$id", $postData);
if($svar->getStatus()===400) {
    $retur .="<p class='ok'>misslyckades med att uppdatera post felaktig indata, som förväntat</p>";
} else {
    $retur .="<p class'error'>uppdatera uppgifter med felaktig indata misslyckades<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 400<br>"
    . print_r($svar->getContent(), true) . "</p>";
}
//lyckas med saknad beskrivning
$postData['time']='01:30';
unset($postData['description']);
$svar= uppdateraUppgift("$id", $postData);
if($svar->getStatus()===200) {
    $retur .="<p class='ok'>uppdatera post med saknad description lyckades</p>";
} else {
    $retur .="<p class'error'>uppdatera uppgift utan description misslyckades<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 200<br>"
    . print_r($svar->getContent(), true) . "</p>";
}
//lyckas med beskrivning


    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        if($db) {
            $db->rollback();
        }
    }

    return $retur;
}
    function test_KontrolleraIndata(): string {
        $retur = "<h2>test_KontrolleraIndata</h2>";
    
        try {
            //Test alla saknas
            $postData=[];
            $svar=kontrolleraIndata($postData);
            if(count($svar)===3){
                $retur.="<p class='ok'>Test alla element saknas lyckades</p>";
            } else {
                $retur.="<p class='error'>Test alla element saknas misslyckades<br>"
                . count($svar) . "returnerades istället för förväntat 3<br>"
                . print_r($svar, true) . "</p>";
            }
    
            //Test datum finns
            $postData["date"]=date('Y-m-d');
            $svar=kontrolleraIndata($postData);
            if(count($svar)===2){
                $retur.="<p class='ok'>Test alla element saknas utom datum lyckades</p>";
            } else {
                $retur.="<p class='error'>Test alla element saknas utom datum misslyckades<br>"
                . count($svar) . "returnerades istället för förväntat 3<br>"
                . print_r($svar, true) . "</p>";
            }
    
            //Test tid finns
            $postData["time"]="01:00";
            $svar=kontrolleraIndata($postData);
            if(count($svar)===1){
                $retur.="<p class='ok'>Test alla element saknas utom tid och datum lyckades</p>";
            } else {
                $retur.="<p class='error'>Test alla element saknas utom tid och datum misslyckades<br>"
                . count($svar) . "returnerades istället för förväntat 3<br>"
                . print_r($svar, true) . "</p>";
            }
    
            //Kontrollera datum
            $content=hamtaAllaAktiviteter()->getContent();
            $aktiviteter=$content["activities"];
            $aktivitetId=$aktiviteter[0]->id;
    
            $postData=["time"=>"01:00"
                , "date"=>"imorgon"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===1) {
                $retur .="<p class='ok'>Kontroll av ogiltigt angivet datum lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av ogiltigt angivet datum misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
            $content=hamtaAllaAktiviteter()->getContent();
            $aktiviteter=$content["activities"];
            $aktivitetId=$aktiviteter[0]->id;
    
            $postData=["time"=>"01:00"
                , "date"=>"2024/1/1"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===1) {
                $retur .="<p class='ok'>Kontroll av felaktigt formaterat datum lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av felaktigt formaterat datum misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
            $content=hamtaAllaAktiviteter()->getContent();
            $aktiviteter=$content["activities"];
            $aktivitetId=$aktiviteter[0]->id;
    
            $nextDay=date('Y-m-d', strtotime(date("Y-m-d"). ' +1 day'));
    
            $postData=["time"=>"01:00"
                , "date"=>"$nextDay"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===1) {
                $retur .="<p class='ok'>Kontroll av datum framåt i tiden lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av datum framåt i tiden misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
            //Kontrollera tid
            $postData=["time"=>"entimme"
                , "date"=>"2024-01-01"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===1) {
                $retur .="<p class='ok'>Kontroll av ogiltigt angiven tid lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av ogiltigt angiven tid misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
            $postData=["time"=>"01:00:00"
                , "date"=>"2024-01-01"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===1) {
                $retur .="<p class='ok'>Kontroll av felaktigt angiven tid lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av felaktigt angiven tid misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
            $postData=["time"=>"09:00"
                , "date"=>"2024-01-01"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===1) {
                $retur .="<p class='ok'>Kontroll av tid längre än 8 timmar lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av tid längre än 8 timmar misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
            //Kontrollera att rätt indata fungerar
            $postData=["time"=>"01:00"
                , "date"=>"2024-01-01"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===0) {
                $retur .="<p class='ok'>Kontroll av rätt indata lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av rätt indata misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
            //Kontrollera aktivitetId
            rsort($aktiviteter);
            $aktivitetId=$aktiviteter[0]->id+1;
            $postData=["time"=>"01:00"
                , "date"=>"2024-01-01"
                , "activityId"=>"$aktivitetId"];
            $svar=kontrolleraIndata($postData);
            $numFel=count($svar);
            if($numFel===1) {
                $retur .="<p class='ok'>Kontroll av id som inte finns lyckades</p>";
            }
            else {
                $retur .="<p class='error'>Kontroll av id som inte finns misslyckades<br>"
                . $numFel . " stycken fel returnerades istället för förväntat 1</p>";
            }
    
        } catch (Exception $ex) {
            $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
        }
    
        return $retur;
    }

/**
 * Test för funktionen radera uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_RaderaUppgift(): string {
    $retur = "<h2>test_RaderaUppgift</h2>";

    try {
        //skapa transaktionen
        $db=connectDb();
        $db->beginTransaction();
        //misslyckades med att redera post med id=sju
        $svar= raderaUppgift('sju');
        if($svar->getStatus()===400)  {
            $retur .="<p class='ok'>misslyckades med att radera post med id=sju, som förväntat</p>";
         } else {
             $retur .="<p class'error'>misslyckades med att hämta post med id=sju<br>"
             . $svar->getStatus() . " returnerades istället för 400<br>"
             . print_r($svar->getContent(), true) . "</p>";
         }
        //misslyckas med att radera post med id=0.1
        $svar= raderaUppgift('0.1');
        if($svar->getStatus()===400)  {
            $retur .="<p class='ok'>misslyckades med att radera post med id=0.1, som förväntat</p>";
         } else {
             $retur .="<p class'error'>misslyckades med att hämta post med id=0.1<br>"
             . $svar->getStatus() . " returnerades istället för 400<br>"
             . print_r($svar->getContent(), true) . "</p>";
         }
        //misslyckas med att radera post med id=0
        $svar= raderaUppgift('0');
        if($svar->getStatus()===400)  {
            $retur .="<p class='ok'>misslyckades med att radera post med id=0, som förväntat</p>";
         } else {
             $retur .="<p class'error'>misslyckades med att hämta post med id=0<br>"
             . $svar->getStatus() . " returnerades istället för 400<br>"
             . print_r($svar->getContent(), true) . "</p>";
         }
        /*
        *lyckas med att radera post som finns
        */
        //hämta poster
$poster= hamtaSida("1");
if($poster->getStatus()!==200) {
    throw new exception('kunde inte hämta posten');
}
$uppgifter=$poster->getContent()->tasks;
        //ta fram id för första posten 
$testId=$uppgifter[0]->id;
//lyckas radera id för första posten 
$svar=raderaUppgift("$testId");
if($svar->getStatus()===200 && $svar->getContent()->result===true) {
$retur .="<p class='ok'>lyckades radera post som förväntat</p>";
} else {
    $retur .="<p class'error'>misslyckades med att radera post<br>"
    . $svar->getStatus() . " returnerades istället för förväntat 200<br>"
    . print_r($svar->getContent(), true) . "</p>";
}
        

        //misslyckades med att radera samma id som tidigare
        $svar=raderaUppgift("$testId");
        if($svar->getStatus()===200 && $svar->getContent()->result===false) {
        $retur .="<p class='ok'>lyckades radera post som förväntat</p>";
        } else {
            $retur .="<p class'error'>misslyckades med att radera post<br>"
            . $svar->getStatus() . " returnerades istället för förväntat 200<br>"
            . print_r($svar->getContent(), true) . "</p>";
        }
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        //avsluta transaktionen
    } if($db) {
        $db->rollback();
    }

    return $retur;
}
