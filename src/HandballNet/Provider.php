<?php

declare (strict_types = 1);

namespace Janborg\H4aTabellen\HandballNet;

use Symfony\Component\Translation\TranslatableMessage;
use Contao\CoreBundle\Translation\TranslatableLabelInterface;

enum Provider: string implements TranslatableLabelInterface
    {
        case HANDBALL4ALL = 'handball4all';
        case NULIGA = 'nuliga';
        case SPORTRADAR = 'sportradar';
    

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'provider.label.' . $this->value,
            [],
            'handballnet',
        );
    }
}