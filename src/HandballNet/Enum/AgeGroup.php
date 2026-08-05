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

enum AgeGroup: string implements TranslatableLabelInterface
{
    case MEN = 'Men';
    case WOMEN = 'Women';
    case AYOUTH = 'AYouth';
    case BYOUTH = 'BYouth';
    case CYOUTH = 'CYouth';
    case DYOUTH = 'DYouth';
    case EYOUTH = 'EYouth';
    case FYOUTH = 'FYouth';
    case MINIS = 'Minis';
    case SENIORS = 'Seniors';

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'agegroup.label.'.$this->value,
            [],
            'handballnet',
        );
    }

    public function toString(): string
    {
        return $this->value;
    }
}
