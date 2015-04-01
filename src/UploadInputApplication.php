<?php
/**
 * Created by Maxim Omelchenko <omelchenko@samsonos.com>
 * on 31.03.2015 at 19:23
 */

namespace samsoncms\input\file;

use samsonphp\upload\Upload;

class UploadInputApplication extends \samsoncms\input\InputApplication
{
    protected $id = 'samson_cms_input_file';

    /**
     * Create field class instance
     *
     * @param string|\samson\activerecord\dbRecord $entity Class name or object
     * @param string|null $param $entity class field
     * @param int $identifier Identifier to find and create $entity instance
     * @param \samson\activerecord\dbQuery|null $dbQuery Database object
     * @return self
     */
    public function createField($entity, $param = null, $identifier = null, $dbQuery = null)
    {
        $this->field = new File($entity, $param, $identifier, $dbQuery);
        return $this;
    }

    /** Upload file controller */
    public function __async_upload()
    {
        s()->async(true);

        /** @var \samsonphp\fs\FileService $fsModule */
        $fsModule = m('fs');

        // Create object for uploading file to server
        $upload = new Upload(array(), $_GET['i']);

        // Uploading file to server;
        $upload->upload($file_path);

        // Call scale if it is loaded
        if (class_exists('\samson\scale\ScaleController', false) && $this->isImage($fsModule->extension($file_path))) {
            /** @var \samson\scale\ScaleController $scale */
            $scale = m('scale');
            $scale->resize($upload->fullPath(), $upload->name(), $upload->uploadDir);
        }

        // TODO: Work with upload real and URL paths
        $urlPath = $upload->path().$upload->name();

        /** @var \samsoncms\input\Field $field Save path to file in DB */
        $this->createField($_GET['e'], $_GET['f'], $_GET['i']);
        $this->field->save($urlPath);

        // Return upload object for further usage
        return array('status' => 1, 'path' => $urlPath);
    }

    /** Delete file controller */
    public function __async_delete()
    {
        s()->async(true);

        /** @var \samsonphp\fs\FileService $fsModule */
        $fsModule = m('fs');

        /** @var \samsoncms\input\Field $field */
        $this->createField($_GET['e'], $_GET['f'], $_GET['i']);

        // Build uploaded file path
        $file = $this->field->getValue();

        // Delete thumbnails
        if (class_exists('\samson\scale\ScaleController', false) && $this->isImage($fsModule->extension($file))) {

            /** @var string $path Path to file */
            $path = '';

            // Get file path
            preg_match('/.*\//', $file, $path);
            $path = $path[0];

            // Get image src
            $src = substr($file, strlen($path));

            /** @var \samson\scale\ScaleController $scale */
            $scale = m('scale');

            foreach (array_keys($scale->thumnails_sizes) as $folder) {
                // Form image path for scale module
                $imageScalePath = $path . $folder . '/' . $src;
                if ($fsModule->exists($imageScalePath)) {
                    $fsModule->delete($imageScalePath);
                }
            }
        }

        $fsModule->delete($file);
        // Save empty field value
        $this->field->save('');

        return array('status'=>true);
    }

    private function isImage($extension)
    {
        return ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif');
    }

    /** @see \samson\core\iModuleViewable::toView() */
    public function toView($prefix = NULL, array $restricted = array())
    {
        $params = $this->field->getObjectData();
        $this->view($this->defaultView)
            ->set('uploadController', url_build($this->id, 'upload'))
            ->set('deleteController', url_build($this->id, 'delete'))
            ->set('getParams', '?f=' . $params['param'] . '&e='. $params['entity'] . '&i=' . $params['dbObject']->id)
            ->set('value', $params['value']);

        //$this->set('empty_text', 'Выберите текст');
        // Call parent rendering routine
//        return parent::toView($prefix, $restricted);
        return array($prefix . 'html' => $this->output());
    }
}
