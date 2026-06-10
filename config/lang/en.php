<?php
// English dictionary (default locale / fallback).
// Strings are split into part-files under config/lang/en/ and merged
// here. Add new keys to the appropriate part-file, not this loader.

$messages = [];
foreach (glob(__DIR__ . '/en/*.php') as $part) {
    $messages = array_merge($messages, require $part);
}
return $messages;
