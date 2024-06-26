<?php
require_once "secrets.php";

if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) { $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP']; }

$app_url = "https://scf.dssoftware.ru/";

$colors = array(
	"success" => "3329330",
	"fault" => "13369344",
	"info" => "8421504"
);

$hypixel_token = $cfg['HYPIXEL_API_KEY'];

$logo_url = "https://sky.dssoftware.ru/logo_scf.png";

$database = array(
	'login' => $cfg['DATABASE']['login'],
	'password' => $cfg['DATABASE']['password'],
	'dbname' => $cfg['DATABASE']['db'],
	'hostname' => $cfg['DATABASE']['host']
);

$bridge_accounts = [
	"NinjaBruta" => "d4fc19c373f4413099c587ed0a6b06a4", // SCF Main
	"pirieluca92" => "5267e398b3e64d4080d73b9b45dd8794", // SCF Backup
	"KizzYoutube2020" => "c6716f85bd5a40edbfb79b5dfb6eaf83", // SCL Main

	"UniversityBot" => "e3020a41a5c24597ad11a2348c46f815", // SBU Main
	"AlphaPsisBridge" => "a42c79f6f60841c38ae6ee1bf2eb7d35", // Alpha Main
	"SBDungeonsBridge" => "b119630beb324ff3a5a2408e20d59a35", // Sigma Main
	"LambdaPiBridge" => "382b64daa73d46cb81759bcd4e13ce9f", // Lambda Main

	"MastersBridge" => "87de0116d5834793a3f2ad0d99b4e8f2", // Masters Main
	"MastersJrBridge" => "4e65ce7ae36e4c64907bc525b4aab845", // Jr. Masters Main
	"DungeonsBridge" => "384248632f3942069a80327a94150f6d", // SBD Main
	"ilovepaul87" => "656035de35124c3b8d39be81a7ab482f" // SBD Alt
];

$api_keys = [
	$cfg['TOKENS']['SCF'] => [
		"name" => "SCF Guild",
        "short_name" => "SCF",
		"guild_id" => "638b9e6a8ea8c990c96e91f7",
		"scf_id" => "scf_main"
	],
	$cfg['TOKENS']['SCL'] => [
		"name" => "SCL Guild",
        "short_name" => "SCL",
		"guild_id" => "66099e8a8ea8c9d0525e1bdd",
		"scf_id" => "scl_main"
	],
    $cfg['TOKENS']['UNI'] => [
		"name" => "SB University Guild",
        "short_name" => "Uni",
		"guild_id" => "6111fcb48ea8c95240436c57",
		"scf_id" => "sbuni_main"
	],
	$cfg['TOKENS']['ALPHA'] => [
		"name" => "SB Alpha Psi Guild",
        "short_name" => "Alpha",
		"guild_id" => "604a765e8ea8c962f2bb3b7a",
		"scf_id" => "alpha_main"
	],
	$cfg['TOKENS']['SIGMA'] => [
		"name" => "SB Sigma Chi Guild",
        "short_name" => "Sigma",
		"guild_id" => "60352e858ea8c90182d34af7",
		"scf_id" => "sigma_main"
	],
	$cfg['TOKENS']['LAMBDA'] => [
		"name" => "SB Lambda Pi Guild",
        "short_name" => "Lambda",
		"guild_id" => "60a16b088ea8c9bb7f6d9052",
		"scf_id" => "lambda_main"
	],
	$cfg['TOKENS']['SBD'] => [
		"name" => "SB Dungeons",
        "short_name" => "SBD",
		"guild_id" => "65ab640e8ea8c9dca6f381d0",
		"scf_id" => "sbd_main"
	],
	$cfg['TOKENS']['MASTERS'] => [
		"name" => "SB Masters Guild",
        "short_name" => "Masters",
		"guild_id" => "570940fb0cf2d37483e106b3",
		"scf_id" => "masters_main"
	],
	$cfg['TOKENS']['MASTERSJR'] => [
		"name" => "SB Masters Jr Guild",
        "short_name" => "Masters Jr",
		"guild_id" => "6125800e8ea8c92e1833e851",
		"scf_id" => "masters_jr"
	]
];

$supported_guilds = [];

foreach($api_keys AS $cfg_supported_guild){
    $supported_guilds[$cfg_supported_guild['guild_id']] = $cfg_supported_guild;
}

?>