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
 * @ContentElement(type=H4aAktuelleSpieleElement::TYPE,
 *   category="handball4all",
 *   template="ce_h4a_aktuellespiele",
 * )
 */
class H4aAktuelleSpieleElement extends AbstractContentElementController
{
    public const TYPE = 'h4a_aktuellespiele';

    public function __construct(private ScopeMatcher $scopeMatcher)
    {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        if ($this->scopeMatcher->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = 'H4a Aktuelle Spiele (Verein-ID: '.$model->h4a_verein_ID.')';

            return new Response($template->parse());
        }

        $arrResult = Helper::getJsonVerein($model->h4a_verein_ID);
        $lastUpdate = time();

        $template->spiele = $arrResult['dataList'];
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse()->setSharedMaxAge(System::getContainer()->getParameter('janborg_h4a_tabellen.AktuelleSpieleCacheTime'));
    }
}
