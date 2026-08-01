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

use Symfony\Component\Translation\TranslatableMessage;

enum GameState: string
{
    case PRE = 'Pre';
    case LIVE = 'Live';
    case POST = 'Post';

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'provider.label.' . $this->value,
            [],
            'handballnet',
        );
    }

    public function toString(): string
    {
        return $this->value;
    }
}
