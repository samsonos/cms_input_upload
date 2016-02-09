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
            ->set('uploadController', url_build(preg_replace('/(_\d+)/', '', $renderer->id()), 'upload'))
            ->set('deleteController', url_build(preg_replace('/(_\d+)/', '', $renderer->id()), 'delete'))
            ->set('getParams', '?f=' . $this->param . '&e='. $this->entity . '&i=' . $this->dbObject->id)
            ->set('value', $this->value())
            ->output();
    }
}
