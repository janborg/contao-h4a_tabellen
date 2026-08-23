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
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: HandballnetTabelleElement::TYPE, category: 'handballnet')]
class HandballnetTabelleElement extends AbstractContentElementController
{
    public const TYPE = 'handballnet_tabelle';

    public function __construct(
        private ScopeMatcher $scopeMatcher,
        private HandballnetApiClient $handballnetApiClient,
        private readonly LoggerInterface|null $logger,
        private readonly int $cacheTtl,
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
            $json = $this->handballnetApiClient->getTournamentTableData($model->handballnet_tournament_id, true);

            if (null === $json) {
                throw new \RuntimeException('Handball.net API returned no data.');
            }

            $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);

            // Timestamp in Sekunden umrechnen
            if (isset($data['data']['updatedAt'])) {
                $data['data']['updatedAtFormatted'] = date('d.m.Y H:i', (int) ($data['data']['updatedAt'] / 1000));
            }
            if (isset($data['data']['refUpdatedAt'])) {
                $data['data']['refUpdatedAtFormatted'] = date('d.m.Y H:i', (int) ($data['data']['refUpdatedAt'] / 1000));
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $data = ['data' => []];
        }

        $team = HandballnetTeamsModel::findOneByHandballnet_tournament_id($model->handballnet_tournament_id);

        // Base-Template Variablen setzen
        $template->set('element_html_id', 'ce_'.$model->id);
        $template->set('element_css_classes', 'ce_handballnet_tabelle');

        // Deine Daten
        $template->set('tabelleData', $data['data']);
        $template->set('handballnetTeam', $team->row());
        $template->set('myTeam', $team->my_team_name);

        return $template->getResponse()->setSharedMaxAge($this->cacheTtl);
    }
}
