
<?php
// Inizializzazione Variabili 
// Perché successivamente utilizziamo queste variabili anche quando non è stato inviato il form.

// Ad esempio:
// value="php echo htmlspecialchars($prodotto);"

// PHP deve sapere quale valore mostrare nel campo HTML anche alla prima apertura della pagina.
$prodotto = "";     // alla prima apertura, il campo verra mostrato vuoto
$prezzo = "";
$sconto = "";

$erroreProdotto = "";   // contiene l'eventuale errore del prodotto
$errorePrezzo = "";
$erroreSconto = "";

$risultato = false;     // indica se il calcolo è riuscito


// Controllo dell'invio del form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupero dei dati inseriti
    $prodotto = trim($_POST["prodotto"]);
    $prezzo = trim($_POST["prezzo"]);
    $sconto = trim($_POST["sconto"]);


    // Controllo del prodotto
    if ($prodotto == "") {
        $erroreProdotto = "Inserisci il nome del prodotto";
    } else if (strlen($prodotto) > 30) {    // strlen() conta il numero di caratteri di una stringa
        $erroreProdotto = "Il prodotto può avere massimo 30 caratteri";
    }

    // Controllo del prezzo
    if ($prezzo == "" || !is_numeric($prezzo) || $prezzo <= 0) {    // !is_numeric() controlla se non è un valore numerico
        $errorePrezzo = "Il prezzo deve essere un numero maggiore di 0";
    }

    // Controllo dello sconto
    if ($sconto == "" || !is_numeric($sconto) || $sconto != intval($sconto) || $sconto < 1 || $sconto > 90) {   // intval() trasforma un valore in un numero intero, eliminando la parte decimale (ES: 20.5 ---> 20)
                                                                                                                // in questo caso significa: Il valore inserito è diverso dalla sua versione intera? (ES: 20.5 != 20 ??? ---> True)
        $erroreSconto = "Lo sconto deve essere un numero INTERO da 1 a 90";
    }


    // Controllo della presenza di errori
    if ($erroreProdotto == "" && $errorePrezzo == "" && $erroreSconto == "") {      // Controlla che tutte e tre le variabili degli errori siano vuote (ovvero che non ci siano errori) e, solo dopo effettua il calcolo

        // Calcolo del risparmio e del prezzo finale
        $risparmio = $prezzo * $sconto / 100;
        $prezzoFinale = $prezzo - $risparmio;

        // Scelta della fascia e del colore
        if ($sconto < 20) {     // Da 1% a 19%
            $fascia = "PICCOLO";
            $colore = "#F8D7DA";      // Rosa

        } else if ($sconto < 50) {      // Da 20% a 49%
            $fascia = "BUONO";
            $colore = "#FFF3CD";     // Giallo
            
        } else {                      // Da 50% a 90%
            $fascia = "SUPER";      
            $colore = "#D4EDDA";    // Verde
        }

        $risultato = true;      // permette di mostrare la card del risultato (nel php), quando i dati sono tutti validi
        $esito = "OK";      // indica che i dati sono corretti


    } else {    // Questo è il caso in cui almeno uno dei campi contiene un errore

        $esito = "KO";      // indica che il calcolo non è riuscito
        $risparmio = "";    // lascia vuoto il risparmio
        $prezzoFinale = "";
        $fascia = "";
    }


    // Preparazione del messaggio degli errori
    $errori = $erroreProdotto . " " . $errorePrezzo . " " . $erroreSconto;      // uniamo i tre possibili messaggi di errore in un'unica variabile, perché successivamente vogliamo salvare tutti gli eventuali errori nel file CSV, in un'unica colonna chiamata Errori
    // ES: $erroreProdotto = "Nome mancante";
    //     $errorePrezzo = "Prezzo non valido";
    //     $erroreSconto = "";
    // DOPO LA CONCATENAZIONE:
    // Nome mancante Prezzo non valido


    // Apertura del file CSV
    $file = fopen("log.csv", "a");      // FORMULA GENERALE: fopen("nomefile", "modalità");

    // Inserimento delle intestazioni se il file è vuoto
    if (filesize("log.csv") == 0) {     // controlla se il file è vuoto, per EVITARE di INSERIRE NUOVAMENTE LE INTESTAZIONI ogni volta che viene effettuato un calcolo
                                        // filesize() restituisce la dimensione di un file in byte ---> Se il risultato è 0, significa che il file è vuoto

        fputcsv($file, array(   // fputcsv() scrive una riga all'interno di un file CSV. In questo caso scrive le intestazioni delle colonne.
            "Data",
            "Prodotto",
            "Prezzo",
            "Sconto",
            "Esito",    // OK --> calcolo riuscito | KO --> calcolo non riuscito
            "Prezzo finale",
            "Risparmio",
            "Fascia",
            "Errori"
        ));
    }


    // Salvataggio dei dati nel file CSV
    fputcsv($file, array(
        date("d/m/Y H:i:s"),    // registra data e ora del calcolo (ES: 05/11/2026 17:30:25)
        $prodotto,
        $prezzo,
        $sconto,
        $esito,
        $prezzoFinale,
        $risparmio,
        $fascia,
        $errori
    ));

    // Chiusura del file
    fclose($file);
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Calcolatore di sconto</title>
    <link rel="stylesheet" href="stile.css">
</head>

<body>

    <h1>Calcolatore di sconto</h1>

    <!-- Form per inserire i dati -->
    <form method="post" action="index.php">

        <label for="prodotto">Prodotto</label>

        <input type="text"
               id="prodotto"
               name="prodotto"
               value="<?php echo htmlspecialchars($prodotto); ?>"
               class="<?php if ($erroreProdotto != "") echo 'errore'; ?>"> 
                <!-- value="?php echo htmlspecialchars($prodotto); ?" -> serve a mantenere nel campo del form il prodotto che l'utente aveva già inserito
                     ES: Se l'utente inserisce "Computer" -> value="Computer" -->

        <?php
        // Visualizzazione dell'errore del prodotto tramite php (lato server)
        if ($erroreProdotto != "") {
            echo "<p class='msg-errore'>$erroreProdotto</p>";   // l'errore appare sotto il campo di inserimento del prodotto (input ...)
                                                                // la classe msg-errore permette di applicare lo stile CSS
        }                                           
        ?>


        <label for="prezzo">Prezzo (€)</label>

        <input type="text"
               id="prezzo"
               name="prezzo"
               value="<?php echo htmlspecialchars($prezzo); ?>"
               class="<?php if ($errorePrezzo != "") echo 'errore'; ?>">

        <?php
        // Visualizzazione dell'errore del prezzo
        if ($errorePrezzo != "") {
            echo "<p class='msg-errore'>$errorePrezzo</p>";
        }
        ?>


        <label for="sconto">Sconto (%)</label>

        <input type="text"
               id="sconto"
               name="sconto"
               value="<?php echo htmlspecialchars($sconto); ?>"
               class="<?php if ($erroreSconto != "") echo 'errore'; ?>">

        <?php
        // Visualizzazione dell'errore dello sconto
        if ($erroreSconto != "") {
            echo "<p class='msg-errore'>$erroreSconto</p>";
        }
        ?>


        <button type="submit">Calcola</button>

    </form>

    
    <!-- Visualizzazione del risultato -->
    <?php

    if ($risultato == true) {   // controlla se il calcolo è riuscito.
                                // Ricordiamo che prima c'era scritto: $risultato = true; --> solamente quando tutti i dati erano validi.
                                // Quindi la card viene mostrata soltanto in caso di successo.

    ?>

        <div class="risultato" style="background-color: <?php echo $colore; ?>;">   <!-- style permette di applicare uno stile direttamente all'elemento | ?php echo $colore; ?: inserisce il colore scelto precedentemente nel background (della card).
                                                                                         ES: style="background-color: #FFF3CD;" -->

            <h2>Risultato</h2>

            <p>Prodotto: <?php echo htmlspecialchars($prodotto); ?></p> <!-- Mostra il nome del prodotto inserito dall'utente | htmlspecialchars() serve a evitare che il testo inserito venga interpretato come codice HTML -->

            <p>Prezzo iniziale:
                € <?php echo number_format($prezzo, 2, ",", "."); ?>    <!-- Serve a formattare un numero 
                                                                        number_format(numero, decimali, separatore_decimale, separatore_migliaia) -->
            </p>

            <p>Risparmio:
                € <?php echo number_format($risparmio, 2, ",", "."); ?>
            </p>

            <p>Prezzo finale:
                € <?php echo number_format($prezzoFinale, 2, ",", "."); ?>
            </p>

            <h3><?php echo $fascia; ?></h3>

        </div>

    <?php
    }

    ?>

    <!-- Visualizzazione dello storico -->
    <h2>Storico dei calcoli</h2>

    <?php

    // Controllo dell'esistenza del file
    if (file_exists("log.csv")) {

        // Apertura del file in lettura
        $file = fopen("log.csv", "r");

        echo "<table border='1' style='margin: 20px auto; border-collapse: collapse;'>";

        echo "<tr>
                <th>Prodotto</th>
                <th>Prezzo finale</th>
                <th>Risparmio</th>
                <th>Fascia</th>
              </tr>";

        // Lettura delle righe del file
        while (($riga = fgetcsv($file)) != false) {

            // Visualizzazione dei soli calcoli riusciti
            if ($riga[0] != "Data" && $riga[4] == "OK") {

                echo "<tr>";

                echo "<td>" . htmlspecialchars($riga[1]) . "</td>";
                echo "<td>€ " . number_format($riga[5], 2, ",", ".") . "</td>";
                echo "<td>€ " . number_format($riga[6], 2, ",", ".") . "</td>";
                echo "<td>" . htmlspecialchars($riga[7]) . "</td>";

                echo "</tr>";
            }
        }

        echo "</table>";

        // Chiusura del file
        fclose($file);

    } else {

        echo "<p>Nessun calcolo effettuato.</p>";

    }

    ?>

</body>
</html>
