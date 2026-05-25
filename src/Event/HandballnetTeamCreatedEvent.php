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

use Janborg\H4aTabellen\Model\HandballnetSeasonsModel;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched each time a new result is upddated from handballnet.
 */
final class HandballnetTeamCreatedEvent extends Event
{
    public function __construct(
        public readonly HandballnetTeamsModel $handballNetTeam,
        public readonly HandballnetSeasonsModel $season,
    ) {
    }

    public function getHandballnetTeam(): HandballnetTeamsModel
    {
        return $this->handballNetTeam;
    }

    public function getHandballnetSeason(): HandballnetSeasonsModel
    {
        return $this->season;
    }
}
