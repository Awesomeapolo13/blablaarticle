<?php

namespace App\Services;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Сервис загрузки файлов
 */
class FileUploader
{
    private FilesystemOperator $filesystem;
    private SluggerInterface $slugger;

    public function __construct(
        FilesystemOperator $filesystem,
        SluggerInterface $slugger
    ) {
        $this->filesystem = $filesystem;
        $this->slugger = $slugger;
    }

    /**
     * Загружаем переданный файл в файловую систему статей
     *
     * @param File $file - файл
     * @return string - имя файла после сохранения
     * @throws FilesystemException
     */
    public function uploadFile(File $file): string
    {
        $fileName = $this->slugger
            ->slug(pathinfo(
                $file instanceof UploadedFile
                    ?
                    $file->getClientOriginalName()
                    :
                    $file->getFilename(),
                PATHINFO_FILENAME
            ))
            ->append('-' . uniqid("", true))
            ->append('.' . $file->guessExtension())
            ->toString()
        ;

        $stream = fopen($file->getPathname(), 'r');
        $this->filesystem->writeStream($fileName, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $fileName;
    }

    /**
     * @param array $files - массив файлов загружаемых для статьи
     * @return string[] - массив имен файлов
     * @throws FilesystemException
     */
    public function uploadManyFiles(array $files): array
    {
        return array_map(
            function ($file) {
                return $this->uploadFile($file);
            },
            $files
        );
    }
}
