<?php

namespace App\Services;

use App\Entity\ArticleImage;
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
    private FilesystemOperator $articleFileSystem;
    private SluggerInterface $slugger;

    public function __construct(
        FilesystemOperator $articleFileSystem,
        SluggerInterface $slugger
    ) {
        $this->articleFileSystem = $articleFileSystem;
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
        $this->articleFileSystem->writeStream($fileName, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $fileName;
    }

    /**
     * Копирует файлы, добавляя им в название постфикс copy (номер копии)
     * Возвращает строку - имя файла
     */
    public function copyFile(string $fileName, string $filePath): string
    {
        $fileArr = explode('.', $fileName);
        $ext = $fileArr[count($fileArr) - 1];
        unset($fileArr[count($fileArr) - 1]);
        $newFileName = implode('.', $fileArr) . '-copy' .  '(1)' . ".$ext";
        $pattern = '/copy\((\d+?)\)/';
        if (preg_match($pattern, $fileArr[1], $copyNum)) {
            $copyNum[1]++;
            $newFileName = preg_replace(
                $pattern,
                "copy($copyNum[1])",
                implode('.', $fileArr))
                . ".$ext";
        }

        copy($filePath . '/' . $fileName, $filePath . '/' . $newFileName);

        return $newFileName;
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

    /**
     * Копирует много файлов и возвращает массив их имен
     */
    public function copyMany(array $fileNames, string $filePath): array
    {
        return array_map(
            function ($fileName) use ($filePath) {
                return $this->copyFile($fileName, $filePath);
            },
            $fileNames
        );
    }

    /**
     * Копирует много файлов из объектов изобрадений для статей и возвращает массив их имен
     */
    public function copyManyForArticles(array $fileNames, string $filePath): array
    {
        return $this->copyMany(
            array_map(
                function ($fileName) {
                    return $fileName instanceof ArticleImage
                        ?
                        $fileName->getName()
                        :
                        $fileName;
                },
                $fileNames
            ),
            $filePath
        );
    }
}
