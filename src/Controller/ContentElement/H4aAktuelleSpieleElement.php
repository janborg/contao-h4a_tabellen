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
 * @ContentElement(type=H4aAktuelleSpieleElement::TYPE,
 *   category="handball4all",
 *   template="ce_h4a_aktuellespiele",
 * )
 */
class H4aAktuelleSpieleElement extends AbstractContentElementController
{
    public const TYPE = 'h4a_aktuellespiele';

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
            $template->wildcard = '## H4a Gamescores ##';

            return new Response($template->parse());
        }

        $arrResult = Helper::getJsonVerein($model->h4a_verein_ID);
        $lastUpdate = time();

        $template->spiele = $arrResult['dataList'];
        $template->myTeam = $model->my_team_name;
        $template->lastUpdate = $lastUpdate;

        return $template->getResponse();
    }
}
