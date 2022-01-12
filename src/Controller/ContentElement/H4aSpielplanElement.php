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
use Contao\Template;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement("h4a_spiele",
 *   category="handball4all",
 *   template="ce_h4a_spiele",
 * )
 */
class H4aSpielplanElement extends AbstractContentElementController
{
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $arrResult = Helper::getJsonSpielplan($model->h4a_team_ID);
        $lastUpdate = time();

        $template->spiele = $arrResult['dataList'];
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse();
    }
}
