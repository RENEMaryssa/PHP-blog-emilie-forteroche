<h2>Page de Monitoring</h2>

<!-- Styles spécifiques au monitoring -->
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

    /* Colonne triée */
    table.adminArticle th.sorted {
        background-color: rgba(37, 94, 51, 0.8); /* vert sombre */
        color: white;
    }

    /* Liens dans les th */
    table.adminArticle th a {
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        position: relative; /* pour l'infobulle */
    }

    /* Flèche indicatrice pour toutes les colonnes triables */
    table.adminArticle th a::after {
        content: ' ⇅';
        font-size: 0.8em;
        color: white;
    }

    /* Si la colonne est triée, afficher flèche ascendante ou descendante */
    table.adminArticle th.sorted a::after {
        content: ' <?= $order === "asc" ? "↑" : "↓" ?>';
        color: white;
    }

    /* Infobulle au survol */
    table.adminArticle th a:hover::before {
        content: 'Cliquer pour trier';
        position: absolute;
        top: -25px;
        left: 0;
        background-color: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7em;
        white-space: nowrap;
        pointer-events: none;
    }

    table.adminArticle tbody tr:nth-child(odd) { background-color: rgba(255,255,255,0.1); }
    table.adminArticle tbody tr:nth-child(even) { background-color: rgba(0,0,0,0.05); }
    table.adminArticle tbody tr:hover { background-color: var(--commentColor); color: white; }

    a.submit {
        padding: 5px 10px;
        font-weight: bold;
        color: white;
        background-color: var(--commentColor);
        border: none;
        border-radius: 5px;
        text-decoration: none;
    }
    a.submit:hover { background-color: var(--commentPaleColor); color: white; }
</style>

<?php
$tables = [
    'articles' => ['getId','getTitle','getContent','getViews','getDateCreation','getDateUpdate'],
    'comments' => ['getId','getIdArticle','getPseudo','getContent','getDateCreation']
];

$labels = [
    'articles' => ['ID','Titre','Contenu','Vues','Date création','Date modification'],
    'comments' => ['ID','Article ID','Pseudo','Contenu','Date']
];

// Fonction pour générer les liens de tri
function thLink($tableName, $colName, $label, $table, $sort, $order) {
    $next = ($colName === $sort && $tableName === $table && $order === 'asc') ? 'desc' : 'asc';
    $class = ($colName === $sort && $tableName === $table) ? 'sorted' : '';
    return "<th class=\"$class\"><a href=\"?action=showMonitoring&table=$tableName&sort=$colName&order=$next\">$label</a></th>";
}

// Fonction pour générer les lignes
function renderRows($data, $methods) {
    foreach ($data as $item) {
        echo "<tr>";
        foreach ($methods as $method) {
            if (!method_exists($item, $method)) continue;
            $val = $item->$method();
            if ($val instanceof DateTime) $val = $val->format('d/m/Y');
            if (is_string($val) && strlen($val) > 200) $val = substr($val,0,200).'…';
            echo "<td>".htmlspecialchars((string)$val)."</td>";
        }
        echo "</tr>";
    }
}
?>

<?php foreach (['articles','comments'] as $tableName): ?>
<section>
    <h2><?= ucfirst($tableName) ?></h2>
    <table class="adminArticle">
        <thead>
            <tr>
                <?php foreach ($tables[$tableName] as $i => $methodName): 
                    $col = str_starts_with($methodName,'get') ? lcfirst(substr($methodName,2)) : $methodName;
                ?>
                    <?= thLink($tableName, $col, $labels[$tableName][$i], $table, $sort, $order) ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php renderRows($$tableName, $tables[$tableName]); ?>
        </tbody>
    </table>
</section>
<?php endforeach; ?>

<div style="text-align: right; margin-top: 20px;">
    <a class="submit" href="index.php?action=admin">Retour</a>
</div>
