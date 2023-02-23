<?php

declare(strict_types=1);

namespace Coderun\Common\Service;

use Symfony\Component\Filesystem\Filesystem;

use function file;
use function array_map;
use function array_flip;
use function array_key_exists;
use function sprintf;

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
     * Проверка данных в файле
     *
     * @param string  $pathFile
     * @param        $content
     *
     * @return bool
     */
    public function contentExists(string $pathFile, $content): bool
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
