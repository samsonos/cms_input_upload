<?php
/**
 * Created by Maxim Omelchenko <omelchenko@samsonos.com>
 * on 31.03.2015 at 19:23
 */

namespace samsoncms\input\file;

use samson\activerecord\dbQuery;
use samsonphp\upload\Upload;

/**
 * SamsonCMS file input module
 * @author Maxim Omelchenko <omelchenko@samsonos.com>
 */
class Application extends \samsoncms\input\Application
{
    /** @var int Field type number */
    public static $type = 1;

    /** @var string SamsonCMS field class */
    protected $fieldClass = '\samsoncms\input\file\File';

    /** Upload file controller */
    public function __async_upload()
    {
        /** @var \samsonphp\fs\FileService $fsModule */
        $fsModule = $this->system->module('fs');

        // Create object for uploading file to server
        $upload = new Upload(array(), $_GET['i']);

        // Uploading file to server;
        $upload->upload($file_path);

        // Call scale if it is loaded
        if (class_exists('\samson\scale\ScaleController', false) && $this->isImage($fsModule->extension($file_path))) {
            /** @var \samson\scale\ScaleController $scale */
            $scale = $this->system->module('scale');
            $scale->resize($upload->fullPath(), $upload->name(), $upload->uploadDir);
        }

        // TODO: Work with upload real and URL paths
        $urlPath = $upload->path().$upload->name();

        /** @var \samsoncms\input\Field $field Save path to file in DB */
        $this->createField(new dbQuery(), $_GET['e'], $_GET['f'], $_GET['i']);
        $this->field->save($urlPath);

        // Return upload object for further usage
        return array('status' => 1, 'path' => $urlPath);
    }

    /** Delete file controller */
    public function __async_delete()
    {
        /** @var \samsonphp\fs\FileService $fsModule */
        $fsModule = $this->system->module('fs');

        /** @var \samsoncms\input\Field $field */
        $this->createField(new dbQuery(), $_GET['e'], $_GET['f'], $_GET['i']);

        // Build uploaded file path
        $file = $this->field->value();

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

        if ($fsModule->exists($file)) {
            $fsModule->delete($file);
        }
        
        // TODO Save empty field value
        $this->field->save(' ');

        return array('status'=>true);
    }

    private function isImage($extension)
    {
        return ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif');
    }
}
