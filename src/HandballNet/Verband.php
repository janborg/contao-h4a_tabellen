<?php

declare (strict_types = 1);

namespace Janborg\H4aTabellen\HandballNet;

use Symfony\Component\Translation\TranslatableMessage;
use Contao\CoreBundle\Translation\TranslatableLabelInterface;

enum Verband: string implements TranslatableLabelInterface
{
    case BADEN = 'baden';
    case HAMBURG = 'hamburg';
    case PFALZ = 'pfalz';
    case RHEINHESSEN = 'rheinhessen';
    case SAAR = 'saar';
    case SCHLESWIGHOLSTEIN = 'schleswig-holstein';
    case SUEDBADEN = 'suedbaden';
    case WESTFALEN = 'westfalen';
    case WUERTTEMBERG = 'wuerttemberg';
    case BAYERN = 'bhv';
    case BRANDENBURG = 'hvbr';
    case BERLIN = 'hvberlin';
    case HESSEN = 'hhv';
    case MECKLENBURG = 'hvmv';
    case NIEDERSACHSEN = 'hvn';
    case RHEINLAND = 'hvr';
    case SACHSEN = 'hvs';
    case THUERINGEN = 'thv';
    case NORDRHEIN = 'hnr';

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'verband.label.' . $this->value,
            [],
            'handballnet',
        );
    }
}