<?php

declare(strict_types=1);

namespace Coderun\Youtube\ContentAdapter;

use Coderun\Common\ValueObject\Video;

interface AdapterInterface
{
    
    /**
     * Видео контент для передачи в сиврис ВК, телега и т.д
     *
     * @param Video $video
     *
     * @return string|null
     */
    public function getContent(Video $video): ?string;
    
    /**
     * @param Video $video
     *
     * @return string|null
     */
    public function getUrl(Video $video): ?string;
    
    /**
     * @param Video $video
     *
     * @return string|null
     */
    public function getLocalPath(Video $video): ?string;
}