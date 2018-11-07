<?php
/**
 * Created by PhpStorm.
 * User: Танат
 * Date: 08.11.2018
 * Time: 23:40
 */

require_once "index.php";
require_once "data.php";
$title = 'Предсказывание счета в футбольном матче';

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
    </head>
    <body>
        <table border="1">
            <caption><?= $title ?></caption>
            <tr>
                <th>Матч</th>
                <th>Вероятный исход</th>
            </tr>
                <?php
                    foreach ($data as $firstTeamId => $datum1) {
                        foreach ($data as $secondTeamId => $datum2) {
                            if ($firstTeamId == $secondTeamId) {
                                continue;
                            }
                            $score = match($firstTeamId, $secondTeamId);
                ?>
                <tr>
                    <td><?= $datum1['name'] . '-' . $datum2['name'] ?></td>
                    <td><?= $score[0] . '-' . $score[1] ?></td>
                </tr>
                <?php
                        }
                    }
                ?>
        </table>
    </body>
</html>