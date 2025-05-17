<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Event;

use Janborg\H4aTabellen\Model\H4aSeasonModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched each time a new result is upddated from h4a.
 */
final class HandballnetTeamCreatedEvent extends Event
{
    public function __construct(
        public readonly HandballnetTeamsModel $handballNetTeam,
        public readonly H4aSeasonModel $season,
    ) {
    }

    public function getHandballnetTeam(): HandballnetTeamsModel
    {
        return $this->handballNetTeam;
    }

    public function getH4aSeason(): H4aSeasonModel
    {
        return $this->season;
    }
}
