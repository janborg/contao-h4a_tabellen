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

enum Gender: string implements TranslatableLabelInterface
{
    case MALE = 'Male';
    case FEMALE = 'Female';
    case MIXED = 'Mixed';

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'gender.label.'.$this->value,
            [],
            'handballnet',
        );
    }

    public function toString(): string
    {
        return $this->value;
    }
}
