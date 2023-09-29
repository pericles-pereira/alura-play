<?php

declare(strict_types=1);

namespace Alura\Mvc\Controller;

use Alura\Mvc\Entity\Video;
use Alura\Mvc\Helper\FlashMessageTrait;
use Alura\Mvc\Repository\VideoRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EditVideoController implements RequestHandlerInterface
{
    use FlashMessageTrait;
    
    public function __construct(private VideoRepository $videoRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryString = $request->getQueryParams();
        $parsedString = $request->getParsedBody();

        $id = filter_var($queryString['id'], FILTER_VALIDATE_INT);
        $url = filter_var($parsedString['url'], FILTER_VALIDATE_URL);
        $titulo = filter_var($parsedString['titulo']);

        if ($id === false || $id === null) {
            $this->addErrorMessage('ID inválido');
            
            return new Response(302, [
                'Location' => '/editar-video'
            ]);
        }

        if ($url === false) {
            $this->addErrorMessage('URL inválida');
            
            return new Response(302, [
                'Location' => '/editar-video'
            ]);
        }

        if ($titulo === false) {
            $this->addErrorMessage('Título inválido');
            
            return new Response(302, [
                'Location' => '/editar-video'
            ]);
        }

        $video = new Video($url, $titulo);
        $video->setId($id);
        $files = $request->getUploadedFiles();
        /** @var UploadedFileInterface $uploadedImage */
        $uploadedImage = $files['image'];

        if ($uploadedImage->getError() === UPLOAD_ERR_OK) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $tmpFile = $uploadedImage->getStream()->getMetadata('uri');
            $mimeType = $finfo->file($tmpFile);

            if (str_starts_with($mimeType, 'image/')) {
                $safeFileName = uniqid('upload_') . '_' . pathinfo($_FILES['image']['name'], PATHINFO_BASENAME);
                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    __DIR__ . '/../../public/img/uploads/' . $safeFileName
                );

                $this->videoRepository->removeImage($video->id);
                $video->setFilePath($safeFileName);
            }
        }

        $success = $this->videoRepository->update($video);
        
        if ($success === false) {
            $this->addErrorMessage('Erro ao editar o vídeo');
            
            return new Response(302, [
                'Location' => '/editar-video'
            ]);
        } else {
            return new Response(302, [
                'Location' => '/'
            ]);
        }
    }
}