<?php
/**
 * Created by PhpStorm.
 * User: Танат
 * Date: 07.11.2018
 * Time: 23:05
 */

require_once "data.php";

/**
 * @param int $firstTeamId
 * @param int $secondTeamId
 * @return array
 */
function match(int $firstTeamId, int $secondTeamId): array
{
    global $data;
    $firstTeamData = $data[$firstTeamId];
    $secondTeamData = $data[$secondTeamId];
    $averageGoals = getAverageGoals($data);
    $firstTeamAttackPower = $firstTeamData['goals']['scored'] / $firstTeamData['games'] / $averageGoals['scored'];
    $firstTeamDefencePower = $firstTeamData['goals']['skipped'] / $firstTeamData['games'] / $averageGoals['skipped'];
    $secondTeamAttackPower = $secondTeamData['goals']['scored'] / $secondTeamData['games'] / $averageGoals['scored'];
    $secondTeamDefencePower = $secondTeamData['goals']['skipped'] / $secondTeamData['games'] / $averageGoals['skipped'];

    // логично, что общее кол-во забитых мячей должно быть равно общему кол-ву пропущенных мячей. однако, это не так. беру в расчет кол-во забитых мячей
    return [
        $firstTeamAttackPower * $secondTeamDefencePower * $averageGoals['scored'],
        $secondTeamAttackPower * $firstTeamDefencePower * $averageGoals['scored'],
    ];
}

/**
 * @param array $teams
 * @return array
 */
function getAverageGoals(array $teams): array
{
    $totalScoredGoals = $totalSkippedGoals = $totalGames = 0;
    foreach ($teams as $team) {
        $totalScoredGoals += $team['goals']['scored'];
        $totalSkippedGoals += $team['goals']['skipped'];
        $totalGames += $team['games'];
    }

    return [
        'scored' => $totalScoredGoals / $totalGames,
        'skipped' => $totalSkippedGoals / $totalGames,
    ];
}