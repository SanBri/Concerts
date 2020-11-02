<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $concerts_images_directory;
    private $slugger; 
    
    public function __construct($concerts_images_directory, SluggerInterface $slugger) {
        $this->concerts_images_directory = $concerts_images_directory;
        $this->slugger = $slugger;
    } 

    public function getTargetDirectory() {
        return $this->concerts_images_directory;
    }

    public function uploadedImageCheck(UploadedFile $file) {
        
        $fileSize = $file->getSize();
        $maxSize = 300000;
        $fileExtension = $file->guessExtension();
        $extensions = array('png', 'gif', 'jpg', 'jpeg');
        if ($fileSize > $maxSize) {
            $fileSizeInKo = $maxSize / 1000;
            $resp = "La taille du fichier est trop élevée (Taille maximum : " . $fileSizeInKo . "Ko)";
        } else if (!in_array($fileExtension, $extensions)) {
            $resp = "Le format du fichier n'est pas valide ! (Les formats acceptés sont : .png, .gif, .jpg et .jpeg)";
        } else {
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = $this->slugger->slug($originalFileName); // "Sluge" le nom du fichier en "format URL"
            $fileNewName = $safeFileName.'-'.uniqid().'.'.$fileExtension; // ID unique pour éviter similarité
            $file->move($this->getTargetDirectory(), $fileNewName);
            $resp = array(true, $fileNewName);
        }
        return $resp;
    }
}