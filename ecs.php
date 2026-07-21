<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withPaths([__DIR__.'/src'])
    ->withConfiguredRule(HeaderCommentFixer::class, [
        'header' => "This file is part of contao-h4a_tabellen.\n\n(c) Jan Lünborg\n\n@license MIT",    
        ]
    );
