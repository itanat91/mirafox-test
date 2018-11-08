<?php
/**
 * Created by PhpStorm.
 * User: Танат
 * Date: 07.11.2018
 * Time: 23:05
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    $teamsPower = calculatePower(['first_team' => $firstTeamData, 'second_team' => $secondTeamData], $averageGoals);
    // логично, что общее кол-во забитых мячей должно быть равно общему кол-ву пропущенных мячей. однако, это не так. беру в расчет кол-во забитых мячей
    $predictedScoresFirstTeam = $teamsPower['first_team']['attack_power'] * $teamsPower['second_team']['defence_power'] * $averageGoals['scored'];
    $predictedScoresSecondTeam = $teamsPower['second_team']['attack_power'] * $teamsPower['first_team']['defence_power'] * $averageGoals['scored'];
    for ($goals = 0; $goals < 10; $goals++) {
        $poisonFirstTeam = round(poissonDistribution($goals, $predictedScoresFirstTeam) * 100);
        $poisonSecondTeam = round(poissonDistribution($goals, $predictedScoresSecondTeam) * 100);
        for ($probability = 0; $probability < $poisonFirstTeam; $probability++) {
            $probableGoalsFirstTeam[] = $goals;
        }
        for ($probability = 0; $probability < $poisonSecondTeam; $probability++) {
            $probableGoalsSecondTeam[] = $goals;
        }
    }

    return [
        $probableGoalsFirstTeam[array_rand($probableGoalsFirstTeam)],
        $probableGoalsSecondTeam[array_rand($probableGoalsSecondTeam)],
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

/**
 * @param array $data
 * @param array $averageGoals
 * @return array
 */
function calculatePower(array $data, array $averageGoals): array
{
    $result = [];

    foreach ($data as $team => $teamData) {
        $result[$team]['attack_power'] = $teamData['goals']['scored'] / $teamData['games'] / $averageGoals['scored'];
        $result[$team]['defence_power'] = $teamData['goals']['skipped'] / $teamData['games'] / $averageGoals['skipped'];
    }

    return $result;
}

/**
 * @param int $goals
 * @param float $teamPower
 * @return float
 */
function poissonDistribution(int $goals, float $teamPower): float {
    return (exp(-$teamPower) * $teamPower ** $goals) / factorial($goals);
}

/**
 * Не использую функцию из пакета gmp
 * @param int $number
 * @return int
 */
function factorial(int $number): int {
    return $number === 0 ? 1 : $number * factorial($number - 1);
}