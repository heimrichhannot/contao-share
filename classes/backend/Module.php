<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Share\Backend;


class Module extends \Backend
{

    /**
     * Get all share print templates
     * @param \DataContainer $dc
     *
     * @return array
     */
    public function getPrintSoloTemplates(\DataContainer $dc)
    {
        return \Controller::getTemplateGroup('share_print');
    }

}