<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 21:03
 */

namespace AppBundle\Service\FileManager;


use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileManagerInterface
{
    /**
     * It returns the path where the file is located
     *
     * @param UploadedFile $file
     * @return string
     */
    public function copyNewsImageFile(UploadedFile $file);

    /**
     * For a given path (i.e. /var/html/docs/project/web/file) it returns a URL
     *
     * @param string $path
     * @return string
     */
    public function getUrlFromPath(string $path);
}