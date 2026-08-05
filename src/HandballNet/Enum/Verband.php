<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\HandballNet\Enum;

use Contao\CoreBundle\Translation\TranslatableLabelInterface;
use Symfony\Component\Translation\TranslatableMessage;

enum Verband: string implements TranslatableLabelInterface
{
    case BADEN = 'baden';
    case BADEN_WUERTTENBERG = 'baden-wuerttemberg';
    case OL_HAMBURG_SCHLESWIG_HOLSTEIN = 'ol-hamburg-schleswig-holstein';
    case HAMBURG = 'hamburg';
    case PFALZ = 'pfalz';
    case RHEINHESSEN = 'rheinhessen';
    case SAAR = 'saar';
    case SCHLESWIGHOLSTEIN = 'schleswig-holstein';
    case SUEDBADEN = 'suedbaden';
    case WESTFALEN = 'westfalen';
    case WUERTTEMBERG = 'wuerttemberg';
    case OL_BADEN_WUERTTENBERG = 'ol-baden-wuerttemberg';
    case BAYERN = 'bhv';
    case BRANDENBURG = 'hvbr';
    case BERLIN = 'hvberlin';
    case HESSEN = 'hhv';
    case MECKLENBURG = 'hvmv';
    case NIEDERSACHSEN = 'hvn';
    case RHEINLAND = 'hvr';
    case SACHSEN = 'hvs';
    case SACHSEN_ANHALT = 'hvsa';
    case THUERINGEN = 'thv';
    case NORDRHEIN = 'hnr';
    case DHBDATA = 'dhbdata';

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'verband.label.'.$this->value,
            [],
            'handballnet',
        );
    }

    public function toString(): string
    {
        return $this->value;
    }
}
