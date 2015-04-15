<?php

$app->get('/{adjective}/brad', function ($adjective) {
    $apiKey = getenv('THESAURUS_KEY');
    $cacheTtl = 0; // Forever cache, because synonym lists are likely never going to change

    $synonyms = Cache::remember(
        $adjective,
        $cacheTtl,
        function() use ($adjective, $apiKey)
        {
            $url = sprintf("http://words.bighugelabs.com/api/2/%s/%s/json", $apiKey, urlencode($adjective));
            $result = json_decode(file_get_contents($url));
            $synonyms = $result->adjective->syn;
            $related = $result->adjective->rel;

            // @todo join uniques between synonyms and related

            return $synonyms;
        }
    );

    $synonym = $synonyms[array_rand($synonyms)];

    return response([
        'result' => ucwords($synonym) . ' Brad'
    ]);
});
