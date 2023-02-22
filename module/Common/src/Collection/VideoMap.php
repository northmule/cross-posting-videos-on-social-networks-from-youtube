<?php

declare(strict_types=1);

namespace Coderun\Common\Collection;

use Coderun\Common\ValueObject\Video;
use Ramsey\Collection\Map\TypedMap;

/**
 * Class VideoMap
 *
 * @package Coderun\Youtube\Collection
 *
 * @extends TypedMap<string, Video>
 */
class VideoMap extends TypedMap
{
    /**
     * {@inheritDoc}
     *
     * @param array<string, Video> $videos
     */
    public function __construct(array $videos = [])
    {
        parent::__construct('string', Video::class, $videos);
    }
}
