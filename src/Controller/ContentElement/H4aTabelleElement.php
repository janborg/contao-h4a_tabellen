<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(type=H4aTabelleElement::TYPE,
 *   category="handball4all",
 *   template="ce_h4a_tabelle",
 * )
 */
class H4aTabelleElement extends AbstractContentElementController
{

    public const TYPE = 'h4a_tabelle';

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $arrResult = Helper::getJsonTabelle($model->h4a_liga_ID);
        $lastUpdate = time();

        $template->teams = $arrResult['dataList'];
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse();
    }
}
