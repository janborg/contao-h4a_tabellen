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

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\System;
use Contao\Template;
use Janborg\H4aTabellen\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(type=H4aSpielplanElement::TYPE,
 *   category="handball4all",
 *   template="ce_h4a_spiele",
 * )
 */
class H4aSpielplanElement extends AbstractContentElementController
{
    public const TYPE = 'h4a_spiele';

    public function __construct(private ScopeMatcher $scopeMatcher)
    {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        if ($this->scopeMatcher->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = 'H4a Spielplan (Team-ID: '.$model->h4a_team_ID.')';

            return new Response($template->parse());
        }

        $arrResult = Helper::getJsonSpielplan($model->h4a_team_ID);
        $lastUpdate = time();

        $template->spiele = $arrResult['dataList'];
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse()->setSharedMaxAge(System::getContainer()->getParameter('janborg_h4a_tabellen.SpielplanCacheTime'));
    }
}
