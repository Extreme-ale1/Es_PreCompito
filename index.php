<?php

// Inizializzazione variabili del form
$prodotto = "";
$prezzo = "";
$sconto ="";

// Inizializzazione variabili errori
$erroreProdotto = "";
$errorePrezzo = "";
$erroreSconto = "";

// Inizializzazione variabili risultato
$mostraRisultato = false;
$prezzoFinale = 0;
$risparmio = 0;
$fascia = "";
$colore = "";

// Controllo di come è stato inviato il form
if ($_SERVER["REQUEST_METHOD"] == "POST"){

    // Recupero dati inseriti
    $prodotto = trim($_POST["prodotto"]);
    $prezzo = trim($_POST["prezzo"]);
    $sconto = trim($_POST["sconto"]);


    // CONTROLLO DEI DATI

    // Controllo prodotto
    if ($prodotto == ""){
        $erroreProdotto = "Inserisci il nome del prodotto";
    }
    elseif (strlen($prodotto) > 30){
        $erroreProdotto = "Il prodotto può avere al massimo 30 caratteri";
    }

    // Controllo prezzo
    if($prezzo == ""){
        $errorePrezzo = "Inserisci il prezzo";
    }
    elseif (!is_numeric($prezzo) || $prezzo <= 0){
        $errorePrezzo = "Il prezzo deve essere maggiore di 0 e deve essere un numero";
    }

    // Controllo sconto 
    if($sconto = ""){
        $erroreSconto = "Inserisci lo sconto";
    }
    elseif(is_numeric($sconto)){
        $erroreSconto = "Lo sconto deve essere un numero";
    }
    elseif($sconto != (int)$sconto || $sconto < 1 || $sconto > 90){
        $erroreSconto = "Lo sconto deve essere un numero intero da 1 a 90";
    }

    // Controllo se non ci sono errori
    if ($erroreProdotto )


}







?>