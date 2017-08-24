<?php
//copied from facebook dev docs https://developers.facebook.com/docs/games/gamesonfacebook/login#usingsignedreques
require_once('./config.php');

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

function parse_signed_request($signed_request) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // decode the data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    // confirm the signature
    $expected_sig = hash_hmac('sha256', $payload, APP_SECRET, $raw = true);
    if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
    }

    return $data;
}
