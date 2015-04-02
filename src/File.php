<?php
namespace samsoncms\input\file;

use samsoncms\input\Field;

/**
 * File SamsonCMS input field
 * @author Vitaly Iegorov <egorov@samsonos.com>
 * @author Maxim Omelchenko <omelchenko@samsonos.com>
 */
class File extends Field
{
    /** {@inheritdoc} */
    public function view($renderer, $saveHandler = '')
    {
        return $renderer->view($this->defaultView)
            ->set('uploadController', url_build($renderer->id(), 'upload'))
            ->set('deleteController', url_build($renderer->id(), 'delete'))
            ->set('getParams', '?f=' . $this->param . '&e='. $this->entity . '&i=' . $this->dbObject->id)
            ->set('value', $this->value())
            ->output();
    }
}
