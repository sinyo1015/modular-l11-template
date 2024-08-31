<?php

$registries = [
    
];

foreach ($registries as $registry) {
    require_once __DIR__ . "/" . $registry;
}
