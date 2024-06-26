<?php

require_once '../config.php';
require_once '../database/init.php';

$db = new database($database);

header('Content-Type: application/json');

if($_GET['method'] == "getWeeklyStats"){
    $graph_info = $db->getOverallGuildTotals();

    $points = [];
    $weeks = [];
    $guilds = [];
    
    foreach ($graph_info['weeks'] as $week_number) {
        $weeks[] = $week_number;
        $guilds = [];
    
        foreach ($supported_guilds as $curr_guild_info) {
            if ($points[$curr_guild_info['short_name']] == null) {
                $points[$curr_guild_info['short_name']] = [];
            }
    
            $guilds[$curr_guild_info['short_name']] = $curr_guild_info['name'];
    
            $points[$curr_guild_info['short_name']][] = (int) ($graph_info['points'][$curr_guild_info['id']][$week_number] ?? 0);
        }
    }
    
    $response = [
        "weeks" => $weeks,
        "points" => $points,
        "guilds" => $guilds
    ];
    
    echo(json_encode($response));
}

if($_GET['method'] == "getHourlyStats"){
    $graph_info = $db->getHourlyGuildStats();

    $points = [];
    $guilds = [];

    foreach ($supported_guilds as $curr_guild_info) {
        if ($points[$curr_guild_info['short_name']] == null) {
            $points[$curr_guild_info['short_name']] = [];
        }

        $guilds[$curr_guild_info['short_name']] = $curr_guild_info['name'];

        $points[$curr_guild_info['short_name']] = $graph_info[$curr_guild_info['id']] ?? [];
    }
    
    $response = [
        "points" => $points,
        "guilds" => $guilds
    ];

    echo(json_encode($response));
}