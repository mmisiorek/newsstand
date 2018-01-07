<?php

namespace AppBundle\Twig\Extension;

/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 22:51
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var \AppBundle\Service\FileManager\FileManagerInterface
     */
    private $fileManager;

    public function __construct(\AppBundle\Service\FileManager\FileManagerInterface $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('pathToUrl', array($this, 'pathToUrl')),
            new \Twig_SimpleFilter('wordCount', array($this, 'wordCount')),
            new \Twig_SimpleFilter('first120Words', array($this, 'first120Words'))
        );
    }

    public function pathToUrl($path)
    {
        return $this->fileManager->getUrlFromPath($path);
    }

    public function wordCount($text)
    {
        $exploded = preg_split("/(\n| )/", $text);
        $exploded = array_filter($exploded, function($word) {
            return strlen(trim($word)) > 0;
        });

        return count($exploded);
    }

    public function first120Words($text)
    {
        $exploded = preg_split("/(\n| )/", $text);
        $counter = 0;
        $exploded = array_filter($exploded, function($word) use(&$counter) {
            if(strlen(trim($word))) {
                $counter++;
                return $counter <= 120;
            }

            return false;
        });

        $newText = implode(" ", $exploded);
        for($i = 0; $i < strlen($newText); $i++) {
            if($text[$i] == "\n") {
                $newText[$i] = "\n";
            }
        }

        return $newText;
    }
}