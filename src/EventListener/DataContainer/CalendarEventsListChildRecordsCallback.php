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

use Contao\Calendar;
use Contao\CalendarModel;
use Contao\Config;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Date;
use Symfony\Component\HttpFoundation\RequestStack;

class CalendarEventsListChildRecordsCallback
{
    private CalendarModel $objCalendar;

    public function __construct(private RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param array<mixed> $arrRow
     *
     * @Callback(table="tl_calendar_events", target="list.sorting.child_record")
     */
    public function __invoke($arrRow): string
    {
        $span = Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

        if ($span > 0) {
            $date = Date::parse(Config::get($arrRow['addTime'] ? 'datimFormat' : 'dateFormat'), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].Date::parse(Config::get($arrRow['addTime'] ? 'datimFormat' : 'dateFormat'), $arrRow['endTime']);
        } elseif ($arrRow['startTime'] === $arrRow['endTime']) {
            $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.Date::parse(Config::get('timeFormat'), $arrRow['startTime']) : '');
        } else {
            $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.Date::parse(Config::get('timeFormat'), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].Date::parse(Config::get('timeFormat'), $arrRow['endTime']) : '');
        }

        $result = ' ';
        // Show result in listview only, when existing
        if ('1' === $arrRow['h4a_resultComplete']) {
            $result = $arrRow['gHomeGoals'].' : '.$arrRow['gGuestGoals'].' ('.$arrRow['gHomeGoals_1'].' : '.$arrRow['gGuestGoals_1'].')';
        }

        // different listview with result for calendars, that are updated via h4a

        if ('delete' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            $this->objCalendar = CalendarModel::findById($this->requestStack->getCurrentRequest()->query->get('id'));

            if ('1' === $this->objCalendar->h4a_imported) {
                return '<div class="tl_content_left"><span style="padding-right:3px">['.$date.']</span>'.$arrRow['title'].' <span style="color:#999;padding-left:3px">'.$result.'</span> </div>';
            }
        }

        return '<div class="tl_content_left">'.$arrRow['title'].' <span style="color:#999;padding-left:3px">['.$date.']</span></div>';
    }
}
