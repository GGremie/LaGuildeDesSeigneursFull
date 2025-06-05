<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Gender extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('feminin', [$this, 'feminin']),
            new TwigFunction('gender', [$this, 'gender']),
        ];
    }
    
    public function feminin(string $kind, string $text): string
    {
        if ('Dame' === $kind || 'Tourmenteuse' === $kind) {
            $text = 'un' === $text ? 'une' : $text;
            $text = 'fort' === $text ? 'forte' : $text;
        }
        return $text;
    }

    public function gender(string $kind): string
    {
        $url = 'Seigneur' === $kind || 'Ennemi' === $kind ? 'https://run.as/skwkgk' : 'https://run.as/6h9mzj';
        return '<img height="24" src="' . $url . '">';
    }
}