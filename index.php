<?php

use Abraham\TwitterOAuth\TwitterOAuth;

require_once("vendor/autoload.php");
set_time_limit(-1);

const CONSUMER_KEY = "AYv4f7zrnDV18BnUS7x2u5aPT";
const CONSUMER_SECRET = "Neum3IMa20Z8Xp0U7JeXzI5xvP6hZ7n78sKyGjzZgqk8et5EEm";
const ACCESS_TOKEN = "773257771265458177-GttMCDsYoGXCqMT4XBHBrdBoiEFYSfw";
const ACCES_SECRET = "thzpRU7HzgUqmQCT9uqTQeQbQ1SvVAMeA1EEBjm0uIB6B";
CONST STATUS_ID = "1150407582307246080";

makeFree();


function makePremium()
{
    $nextResults = 1;
    $results = [];
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCES_SECRET);
    $param = [
        //"count" => 1000,
        "query" => "to:bomelino",
        //"sinceId" => "1150407582307246080"
    ];
    while (!is_null($nextResults)) {
        $result = (array)$connection->get("search/tweets", $param);
        $results[] = $result;
        if (isset($result["errors"])) {
            echo "Error from Twitter: " . $result["errors"][0]->message;
            break;
        }
        if (isset($result["error"])) {
            echo "Error from Twitter: " . $result["error"]->message;
            break;
        }
        if (isset($result["next"])) {
            $param["next"] = $result["next"];
        } else {
            $nextResults = null;
        }
    }
    echo "<ol>";
    foreach ($results as $bundle) {
        foreach ($bundle["statuses"] as $tweet) {
            if ($tweet->in_reply_to_status_id != STATUS_ID) {
                continue;
            }
            $tweetId = $tweet->id;
            if (isset($tweet->extended_tweet)) {
                $text = $tweet->extended_tweet->full_text;
            } else {
                $text = $tweet->text;
            }
            echo '<li><a href="https://twitter.com/statuses/' . $tweetId . '">' . $text . '</a></li>';
        }
    }
    echo "</ol>";
}
function makeFree()
{
    $nextResults = 1;
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCES_SECRET);
    $param = [
        "count" => "1000",
        "q" => "to:bomelino",
        "sinceId" => "1150407582307246080",
        "tweet_mode" => "extended",
    ];
    while (!is_null($nextResults)) {
        $result = (array)$connection->get("search/tweets", $param);
        $results[] = $result;
        if (isset($result["errors"])) {
            echo "Error from Twitter: " . $result["errors"][0]->message;
            break;
        }
        if (isset($result["error"])) {
            echo "Error from Twitter: " . $result["error"]->message;
            break;
        }
        if (isset($result["search_metadata"]->next_results)) {
            $nextResult = $result["search_metadata"]->next_results;
            $nextResult = substr($nextResult, 1);
            parse_str($nextResult, $param);
            $param["tweet_mode"]="extended";
            $param["count"]="1000";
        } else {
            $nextResults = null;
        }
    }
    $results = array_reverse($results);
    echo "<ol>";
    foreach ($results as $bundle) {
        $statuses = array_reverse($bundle["statuses"]);
        foreach ($statuses as $tweet) {
            if ($tweet->in_reply_to_status_id != STATUS_ID) {
                continue;
            }
            $createdAt = new \DateTime($tweet->created_at);
            $user = $tweet->user;
            $tweetId = $tweet->id;
            $text = substr($tweet->full_text,9);
            echo '<li>'
                . $text .
                    ' - <a href="https://twitter.com/statuses/' . $tweetId . '">'.$user->name.' ( '. $createdAt->format("d.m.Y H:i") .' )</a> 
                </li>';
        }
    }
    echo "</ol>";
}
?>