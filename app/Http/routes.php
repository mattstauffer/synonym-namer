<?php
$app->get('/', function() {
    // Not using redirect() because it uses session
    return new Illuminate\Http\RedirectResponse('happy/brad');
});

$app->get('/{adjective}/{name}', function ($adjective, $name) {
    $apiKey = getenv('THESAURUS_KEY');

    $synonyms = Cache::rememberForever(
        $adjective,
        function() use ($adjective, $apiKey)
        {
            $url = sprintf("http://words.bighugelabs.com/api/2/%s/%s/json", $apiKey, urlencode($adjective));
            $result = json_decode(file_get_contents($url));
            $synonyms = $result->adjective->syn;
            // $related = $result->adjective->rel;
            // @todo join uniques between synonyms and related

            return $synonyms;
        }
    );

    $synonym = $synonyms[array_rand($synonyms)];

    return response([
        'result' => ucwords($synonym) . ' ' . ucwords($name)
    ]);
});
