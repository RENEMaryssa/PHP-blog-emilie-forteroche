<h2>Page de Monitoring</h2>


<!-- Styles spécifiques au monitoring adaptés au CSS global -->
<style>
    table.adminArticle {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        background-color: var(--commentPaleColor);
        color: white;
    }

    table.adminArticle th,
    table.adminArticle td {
        border: 2px solid var(--backgroundColor);
        padding: 8px;
        text-align: left;
    }

    table.adminArticle th {
        background-color: var(--commentColor);
        color: white;
    }

    table.adminArticle tbody tr:nth-child(odd) {
        background-color: rgba(255, 255, 255, 0.1);
        /* fond clair */
    }

    table.adminArticle tbody tr:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.05);
        /* un peu plus foncé pour contraste */
    }

    table.adminArticle tbody tr:hover {
        background-color: var(--commentColor);
        color: white;
    }

    a.submit {
        padding: 5px 10px;
        font-weight: bold;
        color: white;
        background-color: var(--commentColor);
        border: none;
        border-radius: 5px;
        text-decoration: none;
    }

    a.submit:hover {
        background-color: var(--commentPaleColor);
        color: white;
    }
</style>

<!-- Articles -->
<section>
    <h2>Articles</h2>
    <table class="adminArticle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Contenu</th>
                <th>Vues</th>
                <th>Date création</th>
                <th>Date modification</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?= $article->getId() ?></td>
                    <td><?= htmlspecialchars($article->getTitle()) ?></td>
                    <td><?= htmlspecialchars($article->getContent(200)) ?></td>
                    <td><?= $article->getViews() ?></td>
                    <td><?= $article->getDateCreation()->format('d/m/Y') ?></td>
                    <td><?= $article->getDateUpdate() ? $article->getDateUpdate()->format('d/m/Y') : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!-- Commentaires -->
<section>
    <h2>Commentaires</h2>
    <table class="adminArticle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Article ID</th>
                <th>Pseudo</th>
                <th>Contenu</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?= $comment->getId() ?></td>
                    <td><?= $comment->getIdArticle() ?></td>
                    <td><?= htmlspecialchars($comment->getPseudo()) ?></td>
                    <td><?= htmlspecialchars($comment->getContent()) ?></td>
                    <td><?= $comment->getDateCreation()->format('d/m/Y') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<a class="submit" href="index.php?action=admin">Retour</a>