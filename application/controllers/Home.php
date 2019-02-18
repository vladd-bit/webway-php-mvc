<?php

namespace Application\Controllers;

use Application\Config\WebConfig;
use Application\Core\Router;
use Application\Core\View;
use Application\Models\UserAccount;
use Application\Models\UserAccountModel;
use Application\Models\ViewModels\Account\UserAccountViewModel;
use Application\Utils\HashGenerator;

class HomeController extends \Application\Core\Controller
{
    public function index()
    {
        if(Authentication::isAuthorized())
        {
            Router::redirect('/home/dashboard');
        }

        $view = new View();
        $view->render('home/index.php');
    }

    public function login($parameters)
    {
        $viewData = array();

        foreach($parameters as $parameter)
        {
            if(isset($_POST[$parameter]))
            {
                $viewData[$parameter] = $_POST[$parameter];
            }
            else
            {
                Router::redirect('/home/index');
            }
        }

        $userAccount = UserAccountModel::getUserByName($viewData['username']);

        if($userAccount)
        {
            $userAccount = new UserAccount($userAccount);

            $sessionKey = HashGenerator::randomizedShaByteHash();

            $validPassword = HashGenerator::validateHash(base64_decode($userAccount->getPasswordSalt()),
                                                         $viewData['password'],
                                                         base64_decode($userAccount->getPasswordHash()));

            if($validPassword)
            {
                $userAccount->setSessionKey($sessionKey);
                $userAccount->setLastLogin(date("Y-m-d H:i:s"));

                $updateAccountSession = UserAccountModel::updateUserSessionLastLogin($userAccount);

                if($updateAccountSession == false)
                {
                    http_response_code(404);
                }

                $expiryTime = time() + WebConfig::DEFAULT_SESSION_LIFETIME;

                $_SESSION['identityUsername'] = $userAccount->getUsername();
                $_SESSION['identityEmail'] = $userAccount->getEmail();
                $_SESSION['userSessionId'] = $sessionKey;
                $_SESSION['userSessionExpiryTime'] = $expiryTime;

                Router::redirect('/home/dashboard');
            }
        }

        Router::redirect('/home/index');
    }

    public function logout()
    {
        $_SESSION['identityUsername'] = null;
        $_SESSION['identityEmail'] = null;
        $_SESSION['userSessionId'] = null;
        $_SESSION['userSessionExpiryTime'] = null;

        Router::redirect('/home/index');
    }

    public function dashboard()
    {
        if(Authentication::isAuthorized())
        {
            $userAccount = new UserAccount(UserAccountModel::getUserByName($_SESSION['identityUsername']));

            if(isset($userAccount))
            {
                $userAccountViewModel = new UserAccountViewModel();
                $userAccountViewModel->setUsername($userAccount->getUsername());
                $userAccountViewModel->setEmail($userAccount->getEmail());

                $view = new View();
                $view->render('home/dashboard.php', $userAccountViewModel);
            }
        }
        else
        {
            Router::redirect('/home/index');
        }
    }
}
