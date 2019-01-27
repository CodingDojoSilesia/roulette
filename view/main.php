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
    <style type="text/css">
        body { padding: 32px; }
        main { max-width: 1024px; margin: 0 auto; }
    </style>
</head>
<body>
    <main role="main">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <td style="white-space: nowrap; width: 1%;">Metoda HTTP</td>
                    <td style="white-space: nowrap; width: 1%;">Nazwa</td>
                    <td style="white-space: nowrap; width: 1%;">Ścieżka do zasobu</td>
                    <td style="white-space: nowrap; width: 1%;">Wypłata<br><span class="text-muted">(tylko dla zakładów)</span></td>
                    <td>Opis</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-primary">GET</span></td>
                    <td>Dokumentacja</td>
                    <td>/</td>
                    <td></td>
                    <td>Obecnie wyświetlany zasób.</td>
                </tr>
                <?php
                   foreach ($bets as $index => $bet) {
                       /* @var $bet \bets\Bet */
                       echo '<tr>';
                       echo '<td><span class="badge badge-info">POST</span></td>';
                       echo '<td>' . $bet->getName() .  '</td>';
                       echo '<td style="white-space: nowrap;">' . $bet->getResourcePath() .  '</td>';
                       echo '<td>' . $bet->getPayout() .  '</td>';
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
