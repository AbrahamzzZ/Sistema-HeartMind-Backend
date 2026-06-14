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

    public function subirVideo(
        string $rutaArchivo
    ): array {

        $resultado = $this->cloudinary
            ->uploadApi()
            ->upload(
                $rutaArchivo,
                [
                    'resource_type' => 'video',
                    'folder' => 'heartmind'
                ]
            );

        return [
            'secure_url' => $resultado['secure_url'],
            'public_id' => $resultado['public_id']
        ];
    }

    public function subirDocumento(
        string $rutaArchivo,
        string $nombreOriginal
    ): array {

        $resultado = $this->cloudinary
            ->uploadApi()
            ->upload(
                $rutaArchivo,
                [
                    'resource_type' => 'auto',
                    'folder' => 'heartmind',
                    'use_filename' => true,
                    'filename_override' => pathinfo(
                        $nombreOriginal,
                        PATHINFO_FILENAME
                    )
                ]
            );

        return [
            'secure_url' => $resultado['secure_url'],
            'public_id' => $resultado['public_id']
        ];
    }

    public function eliminarArchivo(
        string $publicId,
        string $tipo
    ): bool {

        $resourceType =
            $tipo === 'video'
                ? 'video'
                : 'raw';

        $resultado = $this->cloudinary
            ->uploadApi()
            ->destroy(
                $publicId,
                [
                    'resource_type' => $resourceType
                ]
            );

        return ($resultado['result'] ?? '') === 'ok';
    }
}
