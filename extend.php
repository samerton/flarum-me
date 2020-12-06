<?php
use Flarum\Extend;
use Samerton\FlarumMe\Api\Controller;

return [
    (new Extend\Routes('api'))
        ->get('/me', 'user.me', Controller\ShowMeController::class)
];
