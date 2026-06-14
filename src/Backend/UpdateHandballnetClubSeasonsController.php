<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Backend;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Input;
use Contao\Message;
use Janborg\H4aTabellen\HandballnetApiClient;
use Janborg\H4aTabellen\Model\HandballnetClubsModel;
use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UpdateHandballnetClubSeasonsController extends Backend
{
    public function __construct(
        private HandballnetApiClient $handballnetApiClient,
        private UrlGeneratorInterface $urlGenerator,
        private int $new_seasons = 0,
        private int $existing_seasons = 0,
        private int $active_clubs = 0,
    ) {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }

    public function updateClubSeasonsForClub(): void
    {
        $id = Input::get('id');

        $club = HandballnetClubsModel::findById($id);

        try {
            $data = json_decode($this->handballnetApiClient->getClubTeamsData($club->handballnet_id, '2025'), true);
        } catch (\Exception $e) {
            Message::addError($e->getMessage());
            return;
        }

        $clubSeasons = $data['meta']['facets']['0']['values'];

        $this->processClubSeasons($clubSeasons, $club);

        $this->getSummaryMessages();

        $this->redirect($this->urlGenerator->generate('contao_backend', ['do' => 'handballnet_teams', 'table' => 'tl_hn_seasons', 'id' => $id]));
    }

    public function updateClubSeasons(): void
    {
        $objClubs = HandballnetClubsModel::findBy(
            ['is_active = ?'],
            [true],
        );

        if (null === $objClubs) {
            Message::addError('Es sind keine aktiven Clubs vorhanden.');
            $this->redirect($this->getReferer());
        }

        $this->active_clubs = $objClubs->count();

        foreach ($objClubs as $club) {
            try {
                $data = json_decode($this->handballnetApiClient->getClubTeamsData($club->handballnet_id, '2025'), true);
            } catch (\Exception $e) {
                Message::addError($e->getMessage());
                continue;
            }

            $clubSeasons = $data['meta']['facets']['0']['values'];

            $this->processClubSeasons($clubSeasons, $club);
        }

        $this->getSummaryMessages();

        $this->redirect($this->urlGenerator->generate('contao_backend', ['do' => 'handballnet_teams']));
    }

    private function processClubSeasons(array $clubSeasons, HandballnetClubsModel $club): void
    {
        foreach ($clubSeasons as $season) {
            $handballnetSeason = HandballnetSeasonsModel::findBy(
                ['handballnet_club_id=?', 'season_id=?'],
                [$club->id, $season['id']],
            );

            if ($handballnetSeason) {
                // update existing Season
                $handballnetSeasonsModel = $handballnetSeason;
                ++$this->existing_seasons;
            } else {
                // create new Season
                $handballnetSeasonsModel = new HandballnetSeasonsModel();
                $handballnetSeasonsModel->is_active = true;
                $handballnetSeasonsModel->tstamp = time();
                $handballnetSeasonsModel->pid = $club->id;

                ++$this->new_seasons;
            }

            $handballnetSeasonsModel->season_id = $season['id'];
            $handballnetSeasonsModel->season_name = $season['name'];
            $handballnetSeasonsModel->handballnet_club_id = $club->id;
            $handballnetSeasonsModel->club_name = $club->name;

            $handballnetSeasonsModel->save();
        }
    }

    private function getSummaryMessages(): void
    {
        if ($this->active_clubs > 0) {
            Message::addConfirmation($this->active_clubs . ' aktive Club(s) gefunden und aktualisiert.');
        }

        if ($this->new_seasons > 0) {
            Message::addConfirmation($this->new_seasons . ' neue Season(s) erstellt.');
        }
        if ($this->existing_seasons > 0) {
            Message::addInfo($this->existing_seasons . ' existierende Season(s) aktualisiert.');
        }
    }
}
