<?php

use Cloudinary\Cloudinary;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUD_NAME'],
                'api_key' => $_ENV['API_KEY'],
                'api_secret' => $_ENV['API_SECRET']
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    public function subirVideo(string $rutaArchivo): string
    {
        $resultado = $this->cloudinary
            ->uploadApi()
            ->upload(
                $rutaArchivo,
                [
                    'resource_type' => 'video',
                    'folder' => 'heartmind'
                ]
            );

        return $resultado['secure_url'];
    }
}