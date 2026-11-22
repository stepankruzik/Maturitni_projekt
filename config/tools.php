<?php

return [
    'editor' => [
        'slug' => 'editor',
        'name' => 'Online editor fotek',
        'title' => 'Online editor fotek',
        'description' => 'Upravujte fotografie online: oříznutí, filtry, text, kreslení a export.',
        'h1' => 'Editor fotek',
        'requires_ai_credits' => false,
        'default_mode' => 'general',
    ],
    'odstraneni-pozadi' => [
        'slug' => 'odstraneni-pozadi',
        'name' => 'Odstranění pozadí',
        'title' => 'Odstranění pozadí z fotky online',
        'description' => 'Odstraňte pozadí z fotografie a připravte ji k dalším úpravám.',
        'h1' => 'Odstranění pozadí z fotky',
        'requires_ai_credits' => false,
        'default_mode' => 'background',
    ],
    'prukazova-fotka' => [
        'slug' => 'prukazova-fotka',
        'name' => 'Průkazová fotka',
        'title' => 'Vytvoření průkazové fotky online',
        'description' => 'Připravte fotografii pro občanský průkaz, pas nebo jiné dokumenty.',
        'h1' => 'Vytvoření průkazové fotky',
        'requires_ai_credits' => true,
        'default_mode' => 'passport',
    ],
    'zvetseni-rozliseni' => [
        'slug' => 'zvetseni-rozliseni',
        'name' => 'Zvětšení rozlišení',
        'title' => 'Zvětšení rozlišení fotografie online',
        'description' => 'Zvětšete rozlišení fotografie a upravte ji před exportem.',
        'h1' => 'Zvětšení rozlišení fotografie',
        'requires_ai_credits' => true,
        'default_mode' => 'resolution',
    ],
];
