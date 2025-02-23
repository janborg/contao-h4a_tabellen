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

enum Provider: string implements TranslatableLabelInterface
{
    case HANDBALL4ALL = 'handball4all';
    case NULIGA = 'nuliga';
    case SPORTRADAR = 'sportradar';

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'provider.label.'.$this->value,
            [],
            'handballnet',
        );
    }
}
