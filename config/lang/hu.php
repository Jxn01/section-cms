<?php
// Hungarian dictionary.
// Strings are split into part-files under config/lang/hu/ and merged
// here. Any key missing from a part-file falls back to English.

$messages = [];
foreach (glob(__DIR__ . '/hu/*.php') as $part) {
    $messages = array_merge($messages, require $part);
}
return $messages;
