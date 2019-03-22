<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        Image::configure(array('driver' => 'imagick'));
    }

    public function upload(UploadedFile $file, $customName = null, $customSize = [])
    {       
        $fileName = (is_null($customName)) ? md5(uniqid()).'.'.$file->guessExtension() : $customName.'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);

            $this->saveThumbnail($fileName);
            $this->customSizes($fileName, $customSize);

        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    private function saveThumbnail($fileName){
        $fileArray = explode('.', $fileName);

        $fName = $fileArray[0];
        $fExt = $fileArray[1];
        
        $image = Image::make($this->getTargetDirectory().'/'.$fileName)
            ->fit(300, 200, function ($constraint) {
            }, 'center')
            ->save($this->getTargetDirectory().'/'.$fName.'_thumb.'.$fExt);
    }

    private function customSizes($fileName, $sizes = []){
        $fileArray = explode('.', $fileName);
        $fName = $fileArray[0];
        $fExt = $fileArray[1];

        if(count($sizes) > 0){
            forEach($sizes as $size){

                switch($size){
                    case "post_highlight":
                        Image::make($this->getTargetDirectory().'/'.$fileName)
                        ->resize(690, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->save($this->getTargetDirectory().'/'.$fName.'_'.$size.'.'.$fExt);
                        break;
                    default:
                        break;
                }
            }
        }
    }
}