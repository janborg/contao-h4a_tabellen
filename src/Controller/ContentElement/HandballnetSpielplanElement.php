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
use Contao\BackendTemplate;
use Psr\Log\LoggerInterface;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Janborg\H4aTabellen\HandballnetApiClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;


#[AsContentElement(type: HandballnetSpielplanElement::TYPE, category: 'handballnet', template: 'handballnet_spielplan')]
class HandballnetSpielplanElement extends AbstractContentElementController
{
    public const TYPE = 'handballnet_spielplan';

    public function __construct(
        private ScopeMatcher $scopeMatcher,
        private HandballnetApiClient $handballnetApiClient,
        private readonly LoggerInterface|null $logger,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->scopeMatcher->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = 'Handballnet Spielplan (Team-ID: '.$model->handballnet_id.')';

            return new Response($template->parse());
        }

        try {
            $data = json_decode($this->handballnetApiClient->getTeamScheduleData($model->handballnet_id, true), true);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        
        // Base-Template Variablen setzen
        $template->set('element_html_id', 'ce_' . $model->id);
        $template->set('element_css_classes', 'ce_handballnet_spielplan');
        
        // Deine Daten
        $template->set('spielplanData', $data['data']);
        $template->set('myTeam', $model->my_team_name);
        return $template->getResponse();
    }
}
