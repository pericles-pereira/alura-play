<?php

declare(strict_types=1);

namespace Alura\Mvc\Controller;

use Alura\Mvc\Entity\Video;
use Alura\Mvc\Repository\VideoRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NewJsonVideoController implements RequestHandlerInterface
{
    public function __construct(private VideoRepository $videoRepository)
    {
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $videoData = $request->getParsedBody();
        $video = new Video($videoData['url'], $videoData['title']);
        $this->videoRepository->add($video);

        return new Response(201, body: http_response_code(201));
    }
}
