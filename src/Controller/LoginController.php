<?php

namespace App\Controller;

use App\Model\MovieManager;
use App\Model\UserManager;

/**
 * Class LoginController
 * @package App\Controller
 * @property MovieManager
 * @property UserManager
 */
class LoginController extends AbstractController
{
    /**
     * visualisation de la page de connexion
     * @return string
     */
    public function login(): string
    {
        $movieManager = new MovieManager();
        $movies = $movieManager->selectAll();

        $userTab = ['email' => []];
        $userTab['password'] = [];
        $errors = ['email' => []];
        $errors['password'] = [];
        $user['email'] = [];
        $user['WS_password'] = [];
        function checkdata($data): string
        {
            $data = trim($data);
            $data = htmlspecialchars($data);
            $data = htmlentities($data);
            return $data;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['email']) | empty(trim($_POST['email']))) {
                $errors['email'] = "L'email est obligatoire";
            } else {
                $userTab['email'] = checkdata($_POST['email']);
            }
            if (!isset($_POST['password']) | empty(trim($_POST['password']))) {
                $errors['password'] = "Le mot de passe est obligatoire";
            } else {
                $userTab['password'] = checkdata($_POST['password']);
            }
            if (empty($errors['email']) && empty($errors['password'])) {
                $userManager = new UserManager();
                $email = $_POST['email'];
                $user = $userManager->selectOneByEmail($email);
                $_SESSION['user'] = $user;

                if ($user === false) {
                    $errors['connexion'] = "Votre email ou votre mot de passe n'est pas valide.";
                } else {
                    if (
                        $_POST['email'] === $user['email']
                        && password_verify($_POST['password'], $user['password'])
                    ) {
                        $_SESSION['user_id'] = $user['id'];

                        header('Location:/');
                    } else {
                        $errors['connexion'] = "Votre email ou votre mot de passe n'est pas valide.";
                    }
                }
            }
        }
        return $this->twig->render('login.html.twig', ['error' => $errors, 'user' => $userTab, 'movies' => $movies]);
    }

    /**
     * function pour se déconnecter
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['user_id']);
        header('location: /');
    }
}
