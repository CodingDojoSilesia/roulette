<?php
ob_start();
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>API Ruletki</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <style type="text/css">
        body { padding: 32px 32px 0; }
        main { max-width: 1024px; margin: 0 auto; }
        h1 { margin: 0; }
        h2, h3, h4 { margin: 0 0 32px; }
        p { margin: 0 0 32px; }
        pre { margin: 0 0 32px; }
        p + pre { margin: -32px 0 32px; }
        ul { margin-bottom: 32px; }
        p + ul { margin-top: -32px; }
        table.table { margin-bottom: 32px; }
    </style>
</head>
<body>
    <main role="main">
        
        <h1>Dokumentacja API Ruletki</h1>
        
        <p class="lead">Proste API służące do symulacji gry w ruletkę.</p>
        
        <p>W każdej symulacji gry może uczestniczyć tylko jeden gracz, dlatego symulacja obrotu kołem wymaga uwierzytelnienia. Po wykonaniu obrotu zakłady są automatyczanie rozliczane a stan konta gracza jest aktualizowany.
        Gracz może postawić dowolną liczbę zakładów (których lista znajduje się poniżej) w ramach posiadanej przez siebie puli żetonów. Liczbę żetonów, którą stawiamy przekazujemy za pomocą następującego żądania:</p>
        
        <pre><code>{
    "chips": {liczba stawianych żetonów}
}</code></pre>
        
        <h2>Zasady gry w ruletkę</h2>
        
        <p>Reguły zostały oparte o wariant europejski gry.</p>
        
        <h3>Reguły gry</h3>
        <p>Ruletka podzielona jest na tzw. <strong>spiny</strong>, czyli rzuty kulką po okręgu obracającego się koła ruletki posiadającej <strong>37 przegródek</strong> oznaczonych przez <strong>liczby od 1 do 36 oraz 0</strong>. Znajdujące się w kole liczby wieksze od zera dodatkowo posiadają czerwony lub czarny kolor. W każdym spinie gracze obstawiają <strong>wybrane numery lub ich kombinacje</strong> przy pomocy otrzymanych od krupiera kolorów żetonów (kolor identyfikuje gracza). Krupier wypowiadając formułę <em>no more bets</em> (lub językowy odpowiednik) kończy fazę obstawiania i po zatrzymaniu się kulki w przegrodzie zbiera przegrywające żetony i wypłaca wygrane.</p>
        <h4>Rodzaje zakładów</h4>
        <p>Gracz w każdym spinie może obstawić dowolną liczbę pól i kombinacji, o ile mieszczą się one w limitach poszczególnych zakładów i gracz posiada odpowiednią liczbę żetonów. Limity stołu ustalane są przez prowadzącego grę.</p>
        <p>W grze istnieją dwa warianty zakładów: wewnętrzne i zewnętrzne. Jeżeli gracz chce postawić zakład wewnątrz planszy głównej (tj. na polach oznaczonych od 0 do 36), ma do wyboru następujące opcje:</p>
        <ul>
            <li>zakład na pojedyńczy numer (<em>straight</em>);</li>
            <li>zakład na dwa sąsiadujące ze sobą numery (<em>split</em>), np. 4-5, 14-17, 0-2;</li>
            <li>zakład na trzy sąsiadujące ze sobą kolejno w linii numery (<em>street</em>), np. 7-8-9, 19-20-21 ale również 0-1-2 oraz 0-2-3;</li>
            <li>zakład na cztery sąsiadujące ze sobą numery (<em>corner</em>), gdzie punktem wyznaczającym sąsziedztwo jest punkt przecięcia linii, np. 5-6-8-9, 16-17-19-20,</li>
            <li>zakład na sześć sąsiadujących ze sobą numerów (<em>line</em>), są to dwa zakłady <em>street</em> obok siebie, np. 7-8-9-10-11-12, 31-32-33-34-35-36.</li>
        </ul>
        <p>Jeżeli gracz chce postawić zakład zewnętrzny, ma do wyboru następujące opcje:</p>
        <ul>
            <li>zakład na jedną z trzech kolumn złożonych z tuzina numerów (<em>column</em>), np. 1-4-7-10-13-16-19-22-25-28-31-34;</li>
            <li>zakład na jedną z trzech grup numerów złożonych z tuzina numerów (<em>dozen</em>) tj. od 1 do 12, od 13 do 24 i od 25 do 36;</li>
            <li>zakład na wysokie numery (<em>high</em>), tj. od 19 do 36;</li>
            <li>zakład na niskie numery (<em>low</em>), tj. od 1 do 18;</li>
            <li>zakład na czarny lub czerwony kolor (<em>colour</em>), gdzie <strong>0 nie ma koloru</strong>;</li>
            <li>zakład na numery parzyste, ale wypadnięcie <strong>0 przegrywa ten zakład</strong> ;</li>
            <li>zakład na numery nieparzyste;</li>
        </ul>
        <p><img src="/images/european_roulette.png" alt="" style="max-width:100%;"></p>
        <h4>Wypłata</h4>
        <p>Po zatrzymaniu się kulki krupier zabiera przegrane żetony i wypłaca nagrody za poprawne zakłady. Przez wypłatę rozumiany jest zwrot wpłaconych żetonów wraz z dodatkowymi żetonami wynikającymi z współczynnika wypłaty. Współczynnik wypłaty ma postać <strong>1:5</strong>, która oznacza, że za każdy postawiony żeton gracz otrzyma wpłacony żeton plus 5 dodatkowych, w sumie 6. Każdy rodzaj zakładu ma różny współczynnik wypłaty, co zostało przedstawione w poniższej tabeli:</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Zakład</th>
                    <th>Współczynnik wypłaty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Zakład na numery nieparzyste</td>
                    <td>1:1</td>
                </tr>
                <tr>
                    <td>Zakład na numery parzyste</td>
                    <td>1:1</td>
                </tr>
                <tr>
                    <td>Zakład na czarny lub czerwony kolor</td>
                    <td>1:1</td>
                </tr>
                <tr>
                    <td>Zakład na niskie numery</td>
                    <td>1:1</td>
                </tr>
                <tr>
                    <td>Zakład na wysokie numery</td>
                    <td>1:1</td>
                </tr>
                <tr>
                    <td>Zakład na jedną z trzech grup numerów złożonych z tuzina numerów</td>
                    <td>1:2</td>
                </tr>
                <tr>
                    <td>Zakład na jedną z trzech kolumn złożonych z tuzina numerów</td>
                    <td>1:2</td>
                </tr>
                <tr>
                    <td>Zakład na sześć sąsiadujących ze sobą numerów</td>
                    <td>1:5</td>
                </tr>
                <tr>
                    <td>Zakład na cztery sąsiadujące ze sobą numery</td>
                    <td>1:8</td>
                </tr>
                <tr>
                    <td>Zakład na trzy sąsiadujące ze sobą kolejno w linii numery</td>
                    <td>1:11</td>
                </tr>
                <tr>
                    <td>Zakład na dwa sąsiadujące ze sobą numery</td>
                    <td>1:17</td>
                </tr>
                <tr>
                    <td>Zakład na pojedyńczy numer</td>
                    <td>1:35</td>
                </tr>
            </tbody>
        </table>
        <p>Grafika pochodzi z <a href="https://commons.wikimedia.org/wiki/File:European_roulette.svg" rel="nofollow">commons.wikimedia.org</a>.</p>
        
        <h2>Uwierzytelnianie</h2>
        
        <p>Identyfikacja gracza jest realizowana na podstawie wartości, tj. <code>hashname</code> otrzymywany z zasobie tworzenia nowego gracza, przekazanej w 
            nagłówku <code>Authorization</code>.</p>
        
        <h2>Błędy</h2>
        
        <p>W przypadku gdy zakład nie istnieje zwrócony kod to 404. Gdy wysłany zostaje JSON zawierający nieprawidłowe danr zwracany kod jest równy 422 (błąd walidacji), 
            w pozostałych przypadkach kody błędów to 400 i 500.</p>
        
        <h2>Lista zasobów API</h2>
        
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <td style="white-space: nowrap; width: 1%;">Metoda HTTP</td>
                    <td style="white-space: nowrap; width: 1%;">Nazwa</td>
                    <td style="white-space: nowrap; width: 1%;">Ścieżka do zasobu</td>
                    <td style="white-space: nowrap; width: 1%;"><span title="Tylko dla zakładów">Wypłata</span></td>
                    <td style="white-space: nowrap; width: 1%;">Oczekiwany<br>kod HTTP</td>
                    <td>Opis</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-primary">GET</span></td>
                    <td>Dokumentacja</td>
                    <td>/</td>
                    <td class="text-right">-</td>
                    <td><span class="badge badge-success">200</span></td>
                    <td>Obecnie wyświetlany zasób.</td>
                </tr>
                <tr>
                    <td><span class="badge badge-info">POST</span></td>
                    <td>Tworzenie nowego gracza</td>
                    <td>/players</td>
                    <td class="text-right">-</td>
                    <td><span class="badge badge-success">201</span></td>
                    <td>
                        Tworzy nowego gracza z pulą 100 żetonów. Odpowiedź w formacie JSON:
                        <pre><code>{
    "hashname": "..."
}</code></pre>
                        Otrzymana wartość <code>hashname</code> służy do uwierzytelniania gracza.
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-primary">GET</span></td>
                    <td>Liczba żetonów</td>
                    <td><i class="fas fa-lock" title="Wymaga uwierzytelnienia"></i> /chips</td>
                    <td class="text-right">-</td>
                    <td><span class="badge badge-success">200</span></td>
                    <td>
                        Zwraca liczbę żetonów posiadanych przez gracza. Odpowiedź w formacie JSON:
                        <pre style="margin: 0;"><code>{
    "chips": 200
}</code></pre>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-info">POST</span></td>
                    <td>Wykonaj obrót koła</td>
                    <td><i class="fas fa-lock" title="Wymaga uwierzytelnienia"></i> /spin/{liczba}</td>
                    <td class="text-right">-</td>
                    <td><span class="badge badge-success">201</span></td>
                    <td>
                        Symuluje zakręcenie koła przez krupiera, gdzie <code>{liczba}</code> to liczba z zakresu od 0 do 36, wg. której
                        zostaną rozliczone zakłady.
                    </td>
                </tr>
                <?php
                   foreach ($bets as $index => $bet) {
                       /* @var $bet \bets\Bet */
                       echo '<tr>';
                       echo '<td><span class="badge badge-info">POST</span></td>';
                       echo '<td>' . $bet->getName() .  '</td>';
                       echo '<td style="white-space: nowrap;"><i class="fas fa-lock" title="Wymaga uwierzytelnienia"></i> ' . $bet->getResourcePath() .  '</td>';
                       echo '<td class="text-right">' . $bet->getPayout() .  '</td>';
                       echo '<td><span class="badge badge-success">201</span></td>';
                       echo '<td>' . $bet->getDescription() .  '</td>';
                       echo '</tr>';
                   }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>
<?php
return ob_get_clean();
