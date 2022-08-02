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
 * @ContentElement(type=H4aAktuelleSpieleElement::TYPE,
 *   category="handball4all",
 *   template="ce_h4a_aktuellespiele",
 * )
 */
class H4aAktuelleSpieleElement extends AbstractContentElementController
{

    public const TYPE = 'h4a_aktuellespiele';

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response|null
    {
        $arrResult = Helper::getJsonVerein($model->h4a_verein_ID);
        $lastUpdate = time();

        $template->spiele = $arrResult['dataList'];
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse();
    }
}
