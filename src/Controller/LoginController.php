<?php

declare(strict_types=1);

namespace Alura\Mvc\Controller;

use Alura\Mvc\Helper\FlashMessageTrait;
use Alura\Mvc\Repository\UserRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginController implements RequestHandlerInterface
{   
    use FlashMessageTrait;

    public function __construct(private UserRepository $userRepository, private \PDO $pdo)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedString = $request->getParsedBody();

        $password = filter_var($parsedString['password']);
        $email = filter_var($parsedString['email'], FILTER_VALIDATE_EMAIL);
        
        if ($email === false) {
            password_verify('not equal', password_hash('protecting myself from hackers', PASSWORD_ARGON2ID));
            
            $this->addErrorMessage('Usuário ou senha inválidos');

            return new Response(302, [
                'Location' => '/login'
            ]);
        }

        $user = $this->userRepository->findUserByEmail($email);

        $correctPassword = password_verify($password, $user->password ?? '');
        
        if (!$correctPassword) {
            $this->addErrorMessage('Usuário ou senha inválidos');

            return new Response(302, [
                'Location' => '/login'
            ]);
        }

        if (password_needs_rehash($user->password, PASSWORD_ARGON2ID)) {
            $this->userRepository->userRehash($user, $password);
        }

        $_SESSION['logado'] = true;
        
        return new Response(302, [
            'Location' => '/'
        ]);
    }
}