<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\HandballNet;

use Contao\CoreBundle\Translation\TranslatableLabelInterface;
use Symfony\Component\Translation\TranslatableMessage;

enum Verband: string implements TranslatableLabelInterface
{
    case BADEN = 'baden';
    case BADEN_WUERTTENBERG = 'baden-wuerttemberg';
    case HAMBURG = 'hamburg';
    case PFALZ = 'pfalz';
    case RHEINHESSEN = 'rheinhessen';
    case SAAR = 'saar';
    case SCHLESWIGHOLSTEIN = 'schleswig-holstein';
    case SUEDBADEN = 'suedbaden';
    case WESTFALEN = 'westfalen';
    case WUERTTEMBERG = 'wuerttemberg';
    // case BAYERN = 'bhv';
    // case BRANDENBURG = 'hvbr';
    // case BERLIN = 'hvberlin';
    // case HESSEN = 'hhv';
    // case MECKLENBURG = 'hvmv';
    // case NIEDERSACHSEN = 'hvn';
    // case RHEINLAND = 'hvr';
    // case SACHSEN = 'hvs';
    // case THUERINGEN = 'thv';
    // case NORDRHEIN = 'hnr';

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'verband.label.'.$this->value,
            [],
            'handballnet',
        );
    }
}
