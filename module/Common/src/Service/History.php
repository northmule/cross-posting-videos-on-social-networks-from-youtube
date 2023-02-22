<?php

declare(strict_types=1);

namespace Coderun\Common\Service;

use Symfony\Component\Filesystem\Filesystem;

use function file;
use function array_map;
use function array_flip;
use function array_key_exists;
use function strval;

/**
 * Class History
 *
 * @package Coderun\Common\Service
 */
class History
{
    /** @var Filesystem  */
    protected Filesystem $filesystem;
    
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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
        $this->filesystem->appendToFile($pathFile, ($content.PHP_EOL));
    }
    
    /**
     * Проверка данных в файле
     *
     * @param string $pathFile
     * @param        $content
     *
     * @return bool
     */
    public function contentExists(string $pathFile, $content): bool
    {
        if (!$this->filesystem->exists($pathFile)) {
            return false;
        }
        $db = file($pathFile);
        $db = array_map('trim', $db);
        $history = array_flip($db);
        return array_key_exists($content, $history);
    }
    
}