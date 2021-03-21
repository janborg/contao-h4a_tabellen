<?php

/*
 * This file is part of contao-h4a_tabellen.
 * (c) Jan Lünborg
 * @license MIT
 */

namespace Janborg\H4aTabellen\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Template;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement("h4a_tabelle",
 *   category="texts",
 *   template="ce_h4a_tabelle",
 * )
 */
class H4aTabelleElement extends AbstractContentElementController
{
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $arrResult = Helper::getJsonTabelle($model->h4a_liga_ID);
        $lastUpdate = time();

        $template->teams = $arrResult['dataList'];
        $template->class = 'ce_h4a_tabelle';
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse();
    }
}
