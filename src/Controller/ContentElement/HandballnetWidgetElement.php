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
use Janborg\H4aTabellen\Model\HandballnetClubsModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: HandballnetWidgetElement::TYPE, category: 'handballnet')]
class HandballnetWidgetElement extends AbstractContentElementController
{
    public const TYPE = 'handballnet_widget';

    public function __construct(private ScopeMatcher $scopeMatcher)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->scopeMatcher->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = '## Handball.net Widget | '.$model->hn_widget_type.' | '.$model->handballnet_team_id.' ##';

            return new Response($template->parse());
        }

        $clubId = HandballnetClubsModel::findById($model->handballnet_club)->handballnet_id;

        $template->set('element_html_id', 'ce_'.$model->id);
        $template->set('element_css_classes', 'handballnet_widget');

        $template->set('teamId', $model->handballnet_team_id);
        $template->set('clubId', $clubId);
        $template->set('widget_type', $model->hn_widget_type);
        $template->set('widget_token', $model->handballnet_widget_token ?? '');

        $template->set('containerId', 'handball-'.$model->hn_widget_type.'-'.$model->id);

        return $template->getResponse();
    }
}
