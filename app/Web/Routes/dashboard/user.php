<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

use Twig\TwigFunction;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Roles\RolesHelper;
use MythicalSystemsFramework\User\Social\Likes;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;

global $router, $event;

$router->add('/user/(.*)/like', function ($uuid): void {
    global $router, $event, $renderer;
    $template = 'user/profile.twig';

    /*
     * The requirement for each template
     */
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token'],$renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    Engine::registerAlerts($renderer, $template);
    $uuid_current_user = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if ($uuid == '') {
        exit(header('location: /dashboard?e=user_not_found'));
    }
    $token = UserDataHandler::getTokenUUID($uuid);
    if ($token == '') {
        exit(header('location: /dashboard?e=user_not_found'));
    }
    if (!UserDataHandler::isUserValid($token)) {
        exit(header('location: /dashboard?e=user_not_found'));
    }

    if (Likes::hasLiked($uuid_current_user, $uuid)) {
        exit(header('location: /user/' . $uuid . '/profile?e=already_liked'));
    }
    if ($uuid == $uuid_current_user) {
        exit(header('location: /user/' . $uuid . '/profile?e=like_yourself'));
    }
    Likes::addLike($uuid_current_user, $uuid);
    exit(header('location: /user/' . $uuid . '/profile?s=liked'));

});

$router->add('/user/(.*)/dislike', function ($uuid): void {
    global $router, $event, $renderer;
    $template = 'user/profile.twig';
    /*
     * The requirement for each template
     */
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token'],$renderer);

    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    Engine::registerAlerts($renderer, $template);
    $uuid_current_user = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if ($uuid == '') {
        exit(header('location: /dashboard?e=user_not_found'));
    }
    $token = UserDataHandler::getTokenUUID($uuid);
    if ($token == '') {
        exit(header('location: /dashboard?e=user_not_found'));
    }
    if (!UserDataHandler::isUserValid($token)) {
        exit(header('location: /dashboard?e=user_not_found'));
    }

    if (!Likes::hasLiked($uuid_current_user, $uuid)) {
        exit(header('location: /user/' . $uuid . '/profile?e=not_liked'));
    }
    Likes::removeLike($uuid_current_user, $uuid);
    exit(header('location: /user/' . $uuid . '/profile?s=disliked'));

});

$router->add('/user/(.*)/profile', function ($uuid): void {
    global $router, $event, $renderer;
    $template = 'user/profile.twig';
    /*
     * The requirement for each template
     */
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token'],$renderer);

    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid_current_user = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if ($uuid == '') {
        exit(header('location: /dashboard?e=user_not_found'));
    }
    $token = UserDataHandler::getTokenUUID($uuid);
    if ($token == '') {
        exit(header('location: /dashboard?e=user_not_found'));
    }
    if (!UserDataHandler::isUserValid($token)) {
        exit(header('location: /dashboard?e=user_not_found'));
    }

    $renderer->addGlobal('other_user_uuid', $uuid);
    $renderer->addFunction(new TwigFunction('other_user', function ($uuid, $info, $isEncrypted) {
        if ($uuid == '') {
            return null;
        }
        $token = UserDataHandler::getTokenUUID($uuid);
        if ($token == '') {
            return null;
        }
        if (!UserDataHandler::isUserValid($token)) {
            return null;
        }

        return UserDataHandler::getSpecificUserData($token, $info, $isEncrypted);
    }));
    $renderer->addGlobal('other_user_role', RolesHelper::getRoleName(UserDataHandler::getSpecificUserData($token, 'role', false)));
    $renderer->addGlobal('other_user_likes', Likes::getLikesCount($uuid));

    $has_liked = Likes::hasLiked($uuid_current_user, $uuid);
    $renderer->addGlobal('has_liked', $has_liked);

    Engine::registerAlerts($renderer, $template);

    exit($renderer->render($template));
});
