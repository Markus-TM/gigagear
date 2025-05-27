<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <title>Impressum</title>

    <!-- jQuery und Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="..." crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="..."
        crossorigin="anonymous"></script>

    <!-- Eigene CSS-Datei (Pfad bitte prüfen!) -->
    <link src="..\res\css"> <!-- Hinweis: "src" ist kein gültiges Attribut für <link> -->
</head>

<body>
    <?php include("../includes/navbar.php"); // Navigationsleiste einfügen ?>

    <main>
        <div class="container">
            <div class="row">
                <!-- Linker Bereich: Impressumsinformationen -->
                <div class="col-xs-12 col-sm-12 col-md-5">
                    <h1>Gigagear</h1>

                    <br><br>
                    <h3>Computerteilfachhandel</h3>
                    <p>UID-Nr: ATU68472908 </p>
                    <p>FN: FN 395284k FB-Gericht </p>
                    <p>Sitz: Marxergasse 1, 1030 Wien</p>
                    <p>Tel: +43 1 905 62 78 </p>

                    <!-- Kontaktpersonen mit Mailto-Links -->
                    <p><b>E-Mail:</b></p>
                    <p>Enes Demir: <a href="mailto:wi23b009@technikum-wien.at">wi23b009@technikum-wien.at</a></p>
                    <p>Markus Tuma: <a href="mailto:wi23b056@technikum-wien.at">wi23b056@technikum-wien.at</a></p>
                    <p>Omar Abd El Latif: <a href="mailto:wi23b072@technikum-wien.at">wi23b072@technikum-wien.at</a></p>

                    <!-- Rechtliche Hinweise -->
                    <p>Mitglied der WKÖ, WKW</p>
                    <p>Berufsrecht:
                        <a
                            href="https://www.ris.bka.gv.at/GeltendeFassung.wxe?Abfrage=Bundesnormen&Gesetzesnummer=10007517">Gewerbeordnung</a>
                        <a href="https://www.risk.bka.gv.at">www.risk.bka.gv.at</a>
                    </p>
                    <p>Bezirkshauptmannschaft Wien</p>
                    <p>Online-Streitbeilegung:
                        <a href="http://ec.europa.eu/odr">http://ec.europa.eu/odr</a>
                    </p>

                    <!-- Geschäftsführung -->
                    <br>
                    <p>Geschäftsführer: Enes Demir</p>
                    <p>Geschäftsanteile: Enes Demir (33%), Markus Tuma (33%), Omar Abd El Latif (33%)</p>
                </div>

                <!-- Rechter Bereich: Google Maps Standortanzeige -->
                <div class="col-xs-12 col-sm-12 col-md-1"></div> <!-- Leerraumspalte -->
                <div class="col-xs-12 col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </main>
</body>

</html>