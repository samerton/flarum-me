<?php
namespace Samerton\FlarumMe\Api\Controller;

use Flarum\Http\AccessToken;
use Flarum\Http\CookieFactory;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\Exception\NotAuthenticatedException;
use Flarum\User\UserRepository;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowMeController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = UserSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['groups'];

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var CookieFactory
     */
    protected $cookie;

    /**
     * @param UserRepository $users
     * @param CookieFactory $cookie
     */
    public function __construct(UserRepository $users, CookieFactory $cookie)
    {
        $this->users = $users;
        $this->cookie = $cookie;
    }

    /**
     * {@inheritdoc}
     * @throws NotAuthenticatedException
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $session = Arr::get($request->getCookieParams(), $this->cookie->getName('session'));
        $remember = Arr::get($request->getCookieParams(), $this->cookie->getName('remember'));

        if ($session) {
            return $request->getAttribute('actor');
        }

        if (!$session && $remember) {
            $token = AccessToken::find($remember);

            return $this->users->findOrFail($token->user_id);
        }

        throw new NotAuthenticatedException;
    }
}
