<?php
$argv = $_SERVER['argv'];
$command = $argv[1];
$username = $argv[2];
$url = "https://api.github.com/users/$username/events/public";

function fetchApi($url) {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MyApp');

    $response = curl_exec($ch);

    if ($response === false) {
        echo "Curl Error: " . curl_error($ch) . "\n";
        curl_close($ch);
        return null;
    }

    $events = json_decode($response, true);

    $filteredEvents = array_filter($events, function($event) {
    return in_array($event['type'], ['PushEvent']);
    });

    return array_values($filteredEvents);
}
function getPushEvent($filteredEvents) {
    if (!empty($filteredEvents)) {
      foreach ($filteredEvents as $event) {
        $type = $event['type'];

        if(in_array($type, ['PushEvent'])){
            $activityType = "Pushed";
        }

        $pushId = $event['payload']['push_id'];
        $user = $event['actor']['login'];
        $repo = $event['repo']['url'];
        $size = $event['payload']['size'];
        $dateTime = $event['created_at'];

        $result = "ID: $pushId - User: $user, $activityType $size commit to $repo at $dateTime\n";
        echo $result;
      }
    } else{
        echo "No PushEvent or CreateEvent found.\n";
        exit(1);
    }
}

switch ($command) {
    case "fetch":
        $userData = fetchApi($url);
        getPushEvent($userData);
        break;

        default:
        echo "Unknown Command.\n";
}
