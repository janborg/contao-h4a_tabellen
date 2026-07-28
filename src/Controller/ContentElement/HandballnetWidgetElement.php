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

        // Base-Template Variablen setzen
        $template->set('element_html_id', 'ce_'.$model->id);
        $template->set('element_css_classes', 'handballnet_widget');

        $template->teamId = $model->handballnet_team_id;
        $template->widget_type = $model->hn_widget_type;

        $template->containerId = 'handball-'.$model->hn_widget_type.'-'.$model->id;

        return $template->getResponse();
    }
}
