<?php

/**
 * Contrôleur de la partie admin.
 */

class AdminController
{

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin(): void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected(): void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm(): void
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser(): void
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser(): void
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle(): void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Affiche la page de monitoring.
     * @return void
     */
    public function showMonitoring(): void
    {
        $this->checkIfUserIsConnected();

        // Managers
        $articleManager = new ArticleManager();
        $commentManager = new CommentManager();

        // Données brutes
        $articles = $articleManager->getAllArticles();
        $comments = $commentManager->getAllComments();

        // Paramètres de tri
        $table = $_GET['table'] ?? 'articles';
        $sort  = $_GET['sort'] ?? ($table === 'articles' ? 'title' : 'pseudo');
        $order = $_GET['order'] ?? 'asc';

        // Fonction générique de tri
        $sortData = function (&$data, $sort, $order, $type = 'article') {
            usort($data, function ($a, $b) use ($sort, $order, $type) {
                switch ($type) {
                    case 'article':
                        $valA = match ($sort) {
                            'title' => $a->getTitle(),
                            'views' => $a->getViews(),
                            'dateCreation' => $a->getDateCreation()->getTimestamp(),
                            'dateUpdate' => $a->getDateUpdate()?->getTimestamp() ?? 0,
                            default => $a->getId(),
                        };
                        $valB = match ($sort) {
                            'title' => $b->getTitle(),
                            'views' => $b->getViews(),
                            'dateCreation' => $b->getDateCreation()->getTimestamp(),
                            'dateUpdate' => $b->getDateUpdate()?->getTimestamp() ?? 0,
                            default => $b->getId(),
                        };
                        break;

                    case 'comment':
                        $valA = match ($sort) {
                            'pseudo' => $a->getPseudo(),
                            'dateCreation' => $a->getDateCreation()->getTimestamp(),
                            default => $a->getId(),
                        };
                        $valB = match ($sort) {
                            'pseudo' => $b->getPseudo(),
                            'dateCreation' => $b->getDateCreation()->getTimestamp(),
                            default => $b->getId(),
                        };
                        break;

                    default:
                        $valA = $a->getId();
                        $valB = $b->getId();
                }
                return $order === 'asc' ? $valA <=> $valB : $valB <=> $valA;
            });
        };

        // Appliquer le tri
        if ($table === 'articles') $sortData($articles, $sort, $order, 'article');
        if ($table === 'comments') $sortData($comments, $sort, $order, 'comment');

        // Pagination pour les commentaires
        $perPage = 10;
        $page = $_GET['page'] ?? 1;
        $totalComments = count($comments);
        $totalPages = ceil($totalComments / $perPage);
        $start = ($page - 1) * $perPage;
        $commentsPage = array_slice($comments, $start, $perPage);

        // Appel de la vue
        $view = new View("Monitoring Admin");
        $view->render("adminMonitoring", [
            'articles' => $articles,
            'comments' => $comments,         // liste complète si besoin
            'commentsPage' => $commentsPage, // 🔥 page courante
            'totalPages' => $totalPages,
            'page' => $page,
            'table' => $table,
            'sort' => $sort,
            'order' => $order
        ]);
    }

    /**
     * Supprime un commentaire.
     * @return void
     */
    public function deleteComment(): void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request('id', -1);
        if ($id == -1) {
            throw new Exception("Aucun commentaire sélectionné pour la suppression.");
        }

        // Créer un objet Comment avec l'id
        $comment = new Comment(['id' => $id]);

        $commentManager = new CommentManager();
        $success = $commentManager->deleteComment($comment);

        if (!$success) {
            throw new Exception("Impossible de supprimer le commentaire.");
        }

        // Rediriger vers la page de monitoring sur les commentaires
        Utils::redirect("showMonitoring&table=comments");
    }
}
