<?php

declare(strict_types=1);

namespace Coderun\Common\Service;

use Symfony\Component\Filesystem\Filesystem;

use function file;
use function array_map;
use function array_flip;
use function array_key_exists;
use function sprintf;
use function file_get_contents;

/**
 * Class History
 *
 * @package Coderun\Common\Service
 */
class History
{
    /** @var Filesystem  */
    protected Filesystem $filesystem;
    /** @var string  */
    protected string $dirPath;

    /**
     * @param Filesystem $filesystem
     * @param string     $dirPath
     */
    public function __construct(Filesystem $filesystem, string $dirPath)
    {
        $this->filesystem = $filesystem;
        $this->dirPath = $dirPath;
    }

    /**
     * Сохраняет данные в файл
     *
     * @param string $pathFile
     * @param string $content
     *
     * @return void
     */
    public function save(string $pathFile, string $content)
    {
        $this->filesystem->appendToFile(sprintf('%s/%s', $this->dirPath, $pathFile), ($content . PHP_EOL));
    }

    /**
     * @param string $pathFile
     *
     * @return string
     */
    public function getFileContent(string $pathFile): string
    {
        return file_get_contents(sprintf('%s/%s', $this->dirPath, $pathFile));
    }

    /**
     * @param string $pathFile
     *
     * @return bool
     */
    public function fileExists(string $pathFile): bool
    {
        return $this->filesystem->exists(sprintf('%s/%s', $this->dirPath, $pathFile));
    }
    
    /**
     * @param string $pathFile
     *
     * @return int
     */
    public function contentCount(string $pathFile): int
    {
        if (!$this->filesystem->exists(sprintf('%s/%s', $this->dirPath, $pathFile))) {
            return 0;
        }
        $db = file(sprintf('%s/%s', $this->dirPath, $pathFile));
        $db = array_map('trim', $db);
        return count(array_filter($db));
    }

    /**
     * Проверка данных в файле
     *
     * @param string $pathFile
     * @param string $content
     *
     * @return bool
     */
    public function contentExists(string $pathFile, string $content): bool
    {
        if (!$this->filesystem->exists(sprintf('%s/%s', $this->dirPath, $pathFile))) {
            return false;
        }
        $db = file(sprintf('%s/%s', $this->dirPath, $pathFile));
        $db = array_map('trim', $db);
        $history = array_flip($db);
        return array_key_exists($content, $history);
    }
}
