<?php

declare(strict_types=1);

namespace Alura\Mvc\Controller;

use Alura\Mvc\Helper\FlashMessageTrait;
use Alura\Mvc\Repository\VideoRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RemoveImageController implements RequestHandlerInterface
{
    use FlashMessageTrait;
    
    public function __construct(private VideoRepository $repository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryString = $request->getQueryParams();

        $id = filter_var($queryString['id'], FILTER_VALIDATE_INT);

        if ($id === null || $id === false) {
            $this->addErrorMessage('ID inválido');
            
            return new Response(302, [
                'Location' => '/'
            ]);
        }

        $success = $this->repository->removeImage($id);
        if ($success === false) {
            $this->addErrorMessage('Falha ao remover imagem');
            
            return new Response(302, [
                'Location' => '/'
            ]);
        } else {
            return new Response(302, [
                'Location' => '/'
            ]);
        }
    }

}