<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\EventListener\DataContainer;

use Contao\CalendarModel;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use Janborg\H4aTabellen\Model\HandballnetTeamsModel;
use Symfony\Component\HttpFoundation\RequestStack;

class CalendarCheckSeasonDataCallback
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * @Callback(table="tl_calendar", target="config.onload")
     */
    public function __invoke(DataContainer|null $dc = null): void
    {
        if (null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            return;
        }

        $objCalendar = CalendarModel::findById($dc->id);

        if ('1' === $objCalendar->h4a_imported && isset($objCalendar->h4a_seasons)) {
            $seasons = StringUtil::deserialize($objCalendar->h4a_seasons, true);

            foreach ($seasons as &$season) {
                $team = HandballnetTeamsModel::findby(
                    ['team_id=?'],
                    [$season['h4a_team']],
                );
                if (null !== $team) {
                    $season['provider'] = $team->provider;
                    $season['verband'] = $team->verband;
                    $season['liga_shortname'] = $team->liga_shortname;
                    $season['my_team_name'] = $team->my_team_name;
                    $season['handballnet_id'] = $team->handballnet_id;
                }
            }
            $objCalendar->h4a_seasons = serialize($seasons);
            $objCalendar->save();
        }
    }
}
