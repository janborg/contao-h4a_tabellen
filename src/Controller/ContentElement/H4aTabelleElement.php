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

    /**
     * @var ScopeMatcher
     */
    private $scopeMatcher;

    public function __construct(ScopeMatcher $scopeMatcher)
    {
        $this->scopeMatcher = $scopeMatcher;
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->scopeMatcher->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = '## H4a Tabelle ##';

            return new Response($template->parse());
        }

        $arrResult = Helper::getJsonTabelle($model->h4a_liga_ID);
        $lastUpdate = time();

        $template->teams = $arrResult['dataList'];
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse();
    }
}
