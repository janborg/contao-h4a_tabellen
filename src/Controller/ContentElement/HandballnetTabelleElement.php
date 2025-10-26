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
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Janborg\H4aTabellen\HandballnetApiClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: HandballnetTabelleElement::TYPE, category: 'handballnet', template: 'handballnet_tabelle')]
class HandballnetTabelleElement extends AbstractContentElementController
{
    public const TYPE = 'handballnet_tabelle';

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
            $template->wildcard = 'Handballnet Tabelle (Team-ID: '.$model->handballnet_tournament_id.')';

            return new Response($template->parse());
        }

        try {
            $data = json_decode($this->handballnetApiClient->getTournamentTableData($model->handballnet_tournament_id, true), true);
            // Timestamp in Sekunden umrechnen
            if (isset($data['data']['updatedAt'])) {
                $data['data']['updatedAtFormatted'] = date('d.m.Y H:i', (int)($data['data']['updatedAt'] / 1000));
            }
            if (isset($data['data']['refUpdatedAt'])) {
            $data['data']['refUpdatedAtFormatted'] = date('d.m.Y H:i', (int)($data['data']['refUpdatedAt'] / 1000));
        }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $data['data'] = [];
        }

        // Base-Template Variablen setzen
        $template->set('element_html_id', 'ce_'.$model->id);
        $template->set('element_css_classes', 'ce_handballnet_tabelle');

        // Deine Daten
        $template->set('tabelleData', $data['data']);
        $template->set('myTeam', $model->my_team_name);

        return $template->getResponse();
    }
}
