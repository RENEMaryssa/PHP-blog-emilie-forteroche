<h1>Page de Monitoring</h1>

<h2>Articles</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Auteur</th>
        <th>Vues</th>
        <th>Date création</th>
        <th>Date modification</th>
    </tr>
    <?php foreach($articles as $article): ?>
    <tr>
        <td><?= $article->getId() ?></td>
        <td><?= htmlspecialchars($article->getTitle()) ?></td>
        <td><?= $article->getIdUser() ?></td>
        <td><?= $article->getViews() ?></td>
        <td><?= $article->getDateCreation()->format('d/m/Y H:i') ?></td>
        <td><?= $article->getDateUpdate() ? $article->getDateUpdate()->format('d/m/Y H:i') : '-' ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Commentaires</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Article ID</th>
        <th>Pseudo</th>
        <th>Contenu</th>
        <th>Date</th>
    </tr>
    <?php foreach($comments as $comment): ?>
    <tr>
        <td><?= $comment->getId() ?></td>
        <td><?= $comment->getIdArticle() ?></td>
        <td><?= htmlspecialchars($comment->getPseudo()) ?></td>
        <td><?= htmlspecialchars($comment->getContent()) ?></td>
        <td><?= $comment->getDateCreation()->format('d/m/Y H:i') ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Utilisateurs</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Pseudo</th>
        <th>Email</th>
    </tr>
    <?php foreach($users as $user): ?>
    <tr>
        <td><?= $user->getId() ?></td>
        <td><?= htmlspecialchars($user->getPseudo()) ?></td>
        <td><?= htmlspecialchars($user->getEmail()) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
