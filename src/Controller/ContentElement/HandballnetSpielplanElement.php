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

#[AsContentElement(type: HandballnetSpielplanElement::TYPE, category: 'handballnet')]
class HandballnetSpielplanElement extends AbstractContentElementController
{
    public const TYPE = 'handballnet_spielplan';

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
            $template->wildcard = 'Handballnet Spielplan (Team-ID: '.$model->handballnet_team_id.')';

            return new Response($template->parse());
        }

        try {
            $json = $this->handballnetApiClient->getTeamScheduleData($model->handballnet_team_id, true);

            if (null === $json) {
                throw new \RuntimeException('Handball.net API returned no data.');
            }

            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $data = ['data' => [], 'meta' => ['lastUpdated' => 0]];
        }

        $team = HandballnetTeamsModel::findOneByHandballnet_team_id($model->handballnet_team_id);

        // Base-Template Variablen setzen
        $template->set('element_html_id', 'ce_'.$model->id);
        $template->set('element_css_classes', 'ce_handballnet_spielplan');

        // Deine Daten
        $template->set('spielplanData', $data['data']);
        $template->set('handballnetTeam', $team->row());
        $template->set('myTeam', $team->my_team_name);

        // Timestamp in Sekunden umrechnen
        $template->set('lastUpdated', date('d.m.Y H:i', (int) ($data['meta']['lastUpdated'] / 1000)));

        return $template->getResponse()->setSharedMaxAge($this->cacheTtl);
    }
}
