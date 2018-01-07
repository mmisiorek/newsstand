<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 21:17
 */

namespace AppBundle\Service\FileManager;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;

class FileManager implements FileManagerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ContainerInterface $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
    }

    public function getUrlFromPath(string $path)
    {
        $filePath = $this->container->getParameter('filepath');
        $absoluteUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();

        return str_replace($this->getWebDirectory($filePath), sprintf('%s/', $absoluteUrl), $path);
    }

    public function copyNewsImageFile(UploadedFile $file)
    {
        $filePath = $this->container->getParameter('filepath')."/";

        if(!file_exists($filePath)) {
            mkdir($filePath);
        }

        $filePath .= "/newsImages";

        if(!file_exists($filePath)) {
            mkdir($filePath);
        }

        $randomFileName = $this->getRandomNameForPath($filePath, $file->getClientOriginalExtension());
        $file->move($filePath, $randomFileName);

        return sprintf("%s/%s", $filePath, $randomFileName);
    }

    private function getRandomNameForPath($filePath, $extension)
    {
        do {
            if(!empty($extension)) {
                $fileName = sprintf("%s.%s", $this->generateRandomString(), $extension);
            } else {
                $fileName = $this->generateRandomString();
            }
        } while(file_exists(sprintf('%s/%s', $filePath, $fileName)));

        return $fileName;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getWebDirectory($path)
    {
        while(basename($path) !== 'web') {
            $path = dirname($path);
        }

        return $path;
    }
}