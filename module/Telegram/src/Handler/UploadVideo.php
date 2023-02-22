<?php

declare(strict_types=1);

namespace Coderun\Telegram\Handler;

use Coderun\Telegram\ValueObject\Authorization;
use Coderun\Common\ValueObject\Video;
use Coderun\Telegram\Service\UploadVideo as UploadVideoService;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class UploadVideo
 *
 * @package Coderun\Telegram\Handler
 */
class UploadVideo
{
    /** @var Authorization  */
    protected Authorization $authorization;
    /** @var UploadVideoService  */
    protected UploadVideoService $service;
    
    
    /**
     * @param Authorization      $authorization
     * @param UploadVideoService $service
     */
    public function __construct(Authorization $authorization, UploadVideoService $service)
    {
        $this->authorization = $authorization;
        $this->service = $service;
    }
    
    /**
     * @param Video $video
     *
     * @return array
     * @throws TransportExceptionInterface
     */
    public function upload(Video $video): array
    {
        $response = $this->service->upload($video, $this->authorization);
        $content = $response->toArray();
        $response->cancel();
        return $content ?? [];
    }
    
}