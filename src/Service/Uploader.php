<?php

namespace App\Service;

use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Uploader
{
    private $cloudinary;

    public function __construct(string $uploadDsn)
    {
        $this->cloudinary = new Cloudinary($uploadDsn);
    }

    public function upload(string $filename)
    {
        try {
            $result = $this->cloudinary->uploadApi()->upload($filename);
        } catch (ApiError $exception) {

            throw new FileException($exception->getMessage());
        }

        return $result['secure_url'];
    }
}