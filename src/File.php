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
		$value = $this->value();
        if($value === ' ') {
            $value = '';
        }
	
        return $renderer->view($this->defaultView)
            //TODO Fix it later
            ->set(url_build(preg_replace('/(_\d+)/', '', $renderer->id()), 'upload'), 'uploadController')
            ->set(url_build(preg_replace('/(_\d+)/', '', $renderer->id()), 'delete'), 'deleteController')
            ->set('?f=' . $this->param . '&e='. $this->entity . '&i=' . $this->dbObject->id, 'getParams')
            ->set($this->value(), 'value')
            ->output();
    }
}
