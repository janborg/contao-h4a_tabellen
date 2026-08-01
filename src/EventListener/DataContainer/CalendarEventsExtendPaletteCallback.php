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

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

class CalendarEventsExtendPaletteCallback
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    #[AsCallback(table: 'tl_calendar_events', target: 'config.onload')]
    public function __invoke(DataContainer|null $dc = null): void
    {
        if (null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            return;
        }

        $objCalendarEvent = CalendarEventsModel::findById($dc->id);
        $objCalendar = CalendarModel::findById($objCalendarEvent->pid);

        if ($objCalendar->handballnet_imported) {
            PaletteManipulator::create()
                ->addLegend('game_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
                ->addField('homeTeam_name,awayTeam_name,homeTeam_id,awayTeam_id,homeGoals,awayGoals,homeGoalsHalf,awayGoalsHalf,handballnet_state,hn_resultComplete', 'game_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette('default', 'tl_calendar_events')
            ;

            PaletteManipulator::create()
                ->addLegend('handballnet_legend', 'game_legend', PaletteManipulator::POSITION_AFTER)
                ->addField('handballnet_game_id,handballnet_season,handballnet_tournament_name,handballnet_tournament_id,handballnet_phase_name,handballnet_phase_id,handballnet_round_name,handballnet_round_id,handballnet_field_id', 'handballnet_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette('default', 'tl_calendar_events')
            ;
        }
    }
}
