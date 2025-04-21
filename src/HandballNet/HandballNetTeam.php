<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\HandballNet;

class HandballNetTeam
{
    public string $team_name;

    public string $team_url;

    public string $team_id;

    public string $liga_name;

    public string $liga_id;

    public string $liga_short_name;

    public Provider $provider;

    public Verband $verband;
}
