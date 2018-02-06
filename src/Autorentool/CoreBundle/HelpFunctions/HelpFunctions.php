<?php
/**
 * Help class for main controller.
 *
 * @author Alexey Zamuraev
 * @version 0.05
 */

namespace Autorentool\CoreBundle\HelpFunctions;

use DOMDocument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use XMLReader;
use ZipArchive;

class HelpFunctions
{
    public function generateUUIDv4()
    {
        /**
         * Created by Roger Stringer.
         * Page: https://rogerstringer.com/2013/11/14/generate-uuids-php/
         * Date: 14.11.13
         */
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    function base64ToPNG($base64ImageString, $outputFileWithoutExtension, $pathWithoutEndSlash="" ) {
        //data is like:    data:image/png;base64,asdfasdfasdf
        $splited = explode(',', substr( $base64ImageString , 5 ) , 2);
        $mime=$splited[0];
        $data=$splited[1];

        $mimeSplitWithoutBase64=explode(';', $mime,2);
        $mimeSplit=explode('/', $mimeSplitWithoutBase64[0],2);

        $outputFileWithExtension = $outputFileWithoutExtension;
        if(count($mimeSplit)==2)
        {
            $outputFileWithExtension=$outputFileWithoutExtension.'.'.$mimeSplit[1];
        }
        $fileFullPath = $pathWithoutEndSlash . '/' . $outputFileWithExtension;
        file_put_contents( $fileFullPath, base64_decode($data) );

        return $outputFileWithExtension;
    }

    function saveXmlFile($fullPath, $xmlFile) {
        $fs = new Filesystem();
        try {
            $fs->dumpFile($fullPath, $xmlFile);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }

    public function validate($xml_realpath, $dtd_realpath=null) {
        $xml_lines = file($xml_realpath);
        $doc = new DOMDocument;
        if ($dtd_realpath) {
            // Inject DTD inside DOCTYPE line:
            $dtd_lines = file($dtd_realpath);
            $new_lines = array();
            foreach ($xml_lines as $x) {
                // Assume DOCTYPE SYSTEM "blah blah" format:
                if (preg_match('/DOCTYPE/', $x)) {
                    $y = preg_replace('/SYSTEM "(.*)"/',
                        " [\n" . implode("\n", $dtd_lines) . "\n]", $x);
                    $new_lines[] = $y;
                } else {
                    $new_lines[] = $x;
                }
            }
            $doc->loadXML(implode("\n", $new_lines));
        } else {
            $doc->loadXML(implode("\n", $xml_lines));
        }
        // Enable user error handling
        libxml_use_internal_errors(true);
        if (@$doc->validate()) {
            return true;
        } else {
            //var_dump(libxml_get_errors());die;
            $errors = [];
            $errorsArray = libxml_get_errors();
            foreach ($errorsArray as $error) {
                $errors[] = $error->message;
            }

            return $errors;
        }
    }

    function copyFile($source, $dest) {
        if (!file_exists($dest)){
            mkdir($dest, 0777, true);
        }
        shell_exec("cp -r $source $dest");
    }

    function deleteDir($dir)
    {
        if (substr($dir, strlen($dir)-1, 1) != '/')
            $dir .= '/';

        if ($handle = opendir($dir))
        {
            while ($obj = readdir($handle))
            {
                if ($obj != '.' && $obj != '..')
                {
                    if (is_dir($dir.$obj))
                    {
                        if (!$this->deleteDir($dir.$obj))
                            return false;
                    }
                    elseif (is_file($dir.$obj))
                    {
                        if (!unlink($dir.$obj))
                            return false;
                    }
                }
            }
            closedir($handle);

            if (!@rmdir($dir))
                return false;
            return true;
        }
        return false;
    }

    function zipFolderContent($pathToRootFolder, $zipFileWithExtention) {
        // Get real path for our folder
        $rootPath = realpath($pathToRootFolder);

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zipFileWithExtention, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }
}