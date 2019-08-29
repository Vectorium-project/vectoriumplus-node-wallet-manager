<?php
/**
 * Bitcoin Status Page
 *
 * @category File
 * @package  BitcoinStatus
 * @author   Craig Watson <craig@cwatson.org>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/craigwatson/bitcoind-status
 */

//GET WALLET DATA

//curl function

function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

$get_balance = "http://127.0.0.1/api.php?request=" . urlencode('getbalance');
$node_balance = file_get_contents_curl($get_balance);

$get_info = "http://127.0.0.1/api.php?request=" . urlencode('getinfo');
$json_info_data = json_decode(file_get_contents_curl($get_info), TRUE);
$staking = $json_info_data["stake"];
$wallet_protocol = $json_info_data["protocolversion"];
$total_balance = $node_balance + $staking;

$get_difficulty = "http://127.0.0.1/api.php?request=" . urlencode('getdifficulty');
$json_difficulty_data = json_decode(file_get_contents_curl($get_difficulty), TRUE);
$pos_difficulty = $json_difficulty_data["proof-of-stake"];

$get_receiving_address = "http://127.0.0.1/api.php?request=" . urlencode('getaccountaddress ""');
$node_address = str_replace('"', '',file_get_contents_curl($get_receiving_address));

$get_staking_report = "http://127.0.0.1/api.php?request=" . urlencode('getstakereport');
$json_staking = file_get_contents_curl($get_staking_report);
$json_staking_data = json_decode($json_staking,TRUE);
$stake_24h = $json_staking_data["Last 24H"];
$stake_7d = $json_staking_data["Last 7 Days"];
$stake_30d = $json_staking_data["Last 30 Days"];
$stake_365d = $json_staking_data["Last 365 Days"];

$get_staking_info = "http://127.0.0.1/api.php?request=" . urlencode('getstakinginfo');
$json_staking_info_data = json_decode(file_get_contents_curl($get_staking_info), TRUE);

$staking_weight = $json_staking_info_data["weight"];
$staking_weight_network = $json_staking_info_data["netstakeweight"];

$staking_enabled = $json_staking_info_data["enabled"];
if ($staking_enabled == true){
   //staking enabled, ask for disable
   $staking_enabled = "DISABLE STAKING";
}else{
   //staking disabled, ask for enabling
   $staking_enabled = "ENABLE STAKING";	
}


//MASTER CONFIGURATION ARRAY

$config = array(
  // RPC
  'rpc_user'                  => 'user',
  'rpc_pass'                  => 'password',
  'rpc_host'                  => 'localhost',
  'rpc_port'                  => '3002',
  'rpc_ssl'                   => false,
  'rpc_ssl_ca'                => null,

  // WALLET DATA AND STAKING STATUS

  'node_balance'  	      => $node_balance,
  'total_balance'  	      => $total_balance,
  'node_protocol'  	      => $wallet_protocol,
  'pos_difficulty'  	      => $pos_difficulty,  
  'node_staking_enabled'      => $staking_enabled,
  'node_stake_24h'  	      => $stake_24h,
  'node_stake_7d'  	      => $stake_7d,
  'node_stake_30d'  	      => $stake_30d,	
  'node_stake_365d'  	      => $stake_365d,
  'node_staking_weight'       => $staking_weight,	
  'node_network_weight'       => $staking_weight_network,		
  'node_currently_at_stake'   => $staking,

  // Donations
  'display_donation_text'     => true,
  'donation_address'          => $node_address,
  'donation_amount'           => '0.001',

  // Peers
  'display_peer_info'         => true,
  'display_peer_port'         => true,
  'hide_dark_peers'           => true,
  'ignore_unknown_ping'       => false,
  'peers_to_ignore'           => array(),

  // Cache
  'cache_geo_data'            => true,
  'geo_cache_file'            => '/var/tmp/VectoriumPlusd-geolocation.cache',
  'geo_cache_time'            => 604800,
  'use_cache'                 => true,
  'cache_file'                => '/var/tmp/VectoriumPlusd-status.cache',
  'max_cache_time'            => 300,
  'nocache_whitelist'         => array('127.0.0.1'),

  // Geolocation
  'geolocate_peer_ip'         => true,
  'display_ip_location'       => true,

  // UI
  'display_ip'                => true,
  'display_free_disk_space'   => true,
  'display_testnet'           => true,
  'display_version'           => true,
  'display_github_ribbon'     => false,
  'display_max_height'        => true,
  'use_bitcoind_ip'           => true,
  'intro_text'                => 'Self Hosted VectoriumPlus Full Node',
  'title_text'                => 'Vectorium Plus Node Status',
  'display_bitnodes_info'     => false,
  'display_chart'             => true,
  'display_peer_chart'        => true,
  'node_links'                => array(),

  // Stats
  'stats_whitelist'           => array('127.0.0.1'),
  'stats_file'                => '/var/tmp/VectoriumPlusd-status.data',
  'stats_max_age'             => '604800',
  'stats_min_data_points'     => 5,

  // Node Count
  'peercount_whitelist'       => array('127.0.0.1'),
  'peercount_file'            => '/var/tmp/VectoriumPlusd-peers.data',
  'peercount_max_age'         => '2592000',
  'peercount_min_data_points' => 10,
  'peercount_extra_nodes'     => array(),

  // Uptime
  'display_bitcoind_uptime'   => true,
  'bitcoind_process_name'     => 'VectoriumPlusd',

  // System
  'date_format'               => 'H:i:s T, j F Y ',
  'disk_space_mount_point'    => '.',
  'timezone'                  => null,
  'stylesheet'                => 'v2-light.css',
  'debug'                     => true,
  'admin_email'               => 'admin@example.com',
);
