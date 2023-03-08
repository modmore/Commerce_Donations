<?php

$snips = array(
    'cause' => 'Show a donation widget for a specific cause.',
    'donations' => 'Show previously received donations for a specific cause.',
);

$snippets = array();
$idx = 0;

foreach ($snips as $name => $description) {
    $idx++;
    $snippets[$idx] = $modx->newObject('modSnippet');
    $snippets[$idx]->fromArray(array(
       'name' => 'commerce_donations.' . $name,
       'description' => $description . ' (Part of Commerce Donations)',
       'snippet' => getSnippetContent($sources['snippets'] . strtolower($name) . '.snippet.php')
    ));
}

return $snippets;
