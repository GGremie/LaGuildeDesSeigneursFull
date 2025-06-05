<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Puissance extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('puissance', [$this, 'puissance']),
        ];
    }
    public function puissance(array $character): int
    {
        $puissances = [
            'Dame' => 1.5,
            'Tourmenteuse' => 1.4,
            'Seigneur' => 1.3,
            'Tourmenteur' => 1.2,
        ];
        return $character['strength'] * $character['intelligence'] * $puissances[$character['kind']];
    }
}