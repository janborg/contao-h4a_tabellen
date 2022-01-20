<?php

declare(strict_types=1);

namespace Janborg\H4aTabellen\EventListener\DataContainer;

use Contao\CalendarModel;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

class H4aCalendarEventsExtendPaletteCallback
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Callback(table="tl_calendar_events", target="config.onload")
     */
    public function __invoke(DataContainer $dc = null): void
    {
        if (null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            return;
        }

        $objCalendarEvent = CalendarEventsModel::findById($dc->id);
        $objCalendar = CalendarModel::findById($objCalendarEvent->pid);

        if ('1' === $objCalendar->h4a_imported) {
            PaletteManipulator::create()
                ->addLegend('h4a_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
                ->addField('gHomeTeam,gGuestTeam,gClassID,gClassName,h4a_season,gGameNo', 'h4a_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette('default', 'tl_calendar_events')
            ;

            PaletteManipulator::create()
                ->addLegend('gymnasium_legend', 'h4a_legend', PaletteManipulator::POSITION_AFTER)
                ->addField('gGymnasiumNo,gGymnasiumName,gGymnasiumStreet,gGymnasiumTown,gGymnasiumPostal', 'gymnasium_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette('default', 'tl_calendar_events')
            ;

            PaletteManipulator::create()
                ->addLegend('result_legend', 'gymnasium_legend', PaletteManipulator::POSITION_AFTER)
                ->addField('gHomeGoals,gGuestGoals,gHomeGoals_1,gGuestGoals_1,sGID,gComment,h4a_resultComplete', 'result_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette('default', 'tl_calendar_events')
            ;
        }

    }
}