<?php

/**
 * Convert json from tabenew.com.br response to basic RSS feed
 *
 * @author Walisson Aguirra <walisson.aguirra@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0 GNU Public License
 */

// Parse the JSON feed
$userName  = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$limit     = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT) ?? 4;
$baseUrl   = 'https://www.tabnews.com.br';
$postsJson = file_get_contents("{$baseUrl}/api/v1/contents/{$userName}");
$posts     = json_decode($postsJson);

// Create the RSS feed object
$rss = new SimpleXMLElement('<rss version="1.0"></rss>');

// Add the channel element to the RSS feed
$channel = $rss->addChild('channel');

// Add the channel data to the RSS feed
$channel->addChild('title', 'TabNews');
$channel->addChild('description', 'Conteúdos para quem trabalha com Programação e Tecnologia');
$channel->addChild('link', "{$baseUrl}/api/v1/contents/{$userName}");

// Add the items to the RSS feed
$count = 0;
foreach ($posts as $post) {
    if ($post->parent_id) continue;
    if ($count++ == $limit) break;

    $rssItem = $channel->addChild('item');
    $rssItem->addChild('title', $post->title);
    $rssItem->addChild('link', "{$baseUrl}/$userName/{$post->slug}");
}

// Output the RSS feed as a string
header('Content-Type: text/xml; charset=utf-8');
header('access-control-allow-methods: GET');
header('access-control-allow-origin: *');
echo $rss->asXML();
