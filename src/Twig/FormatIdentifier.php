<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatIdentifier extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_identifier', [$this, 'formatIdentifier']),
        ];
    }
    public function formatIdentifier(string $identifier): string
    {
        return strtoupper(implode('-', str_split($identifier, 4)));
    }
}