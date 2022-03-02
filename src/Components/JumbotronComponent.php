<?php
namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('jumbotron')]
class JumbotronComponent
{
    public string $title;
    public string $subtitle;
    public string $classes = "";
}