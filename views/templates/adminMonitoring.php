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
    table.adminArticle th.sorted { background-color: rgba(37,94,51,0.8); color: white; }
    table.adminArticle th a { cursor: pointer; text-decoration: none; color: inherit; position: relative; }
    table.adminArticle th a::after { content: ' ⇅'; font-size: 0.8em; color: white; }
    table.adminArticle th.sorted a::after { content: ' <?= $order==="asc"?"↑":"↓" ?>'; color: white; }
    table.adminArticle th a:hover::before {
        content: 'Cliquer pour trier'; position: absolute; top: -25px; left: 0;
        background-color: rgba(0,0,0,0.7); color: white; padding: 2px 6px;
        border-radius: 3px; font-size: 0.7em; white-space: nowrap; pointer-events: none;
    }
    table.adminArticle tbody tr:nth-child(odd) { background-color: rgba(255,255,255,0.1); }
    table.adminArticle tbody tr:nth-child(even) { background-color: rgba(0,0,0,0.05); }
    table.adminArticle tbody tr:hover { background-color: var(--commentColor); color: white; }
    a.submit {
        padding: 5px 10px; font-weight: bold; color: white; background-color: var(--commentColor);
        border: none; border-radius: 5px; text-decoration: none;
    }
    a.submit:hover { background-color: var(--commentPaleColor); color: white; }

    .pagination { margin-top: 10px; }
    .pagination a { margin: 0 5px; text-decoration: none; color: var(--commentColor); font-weight: bold; }
    .pagination a.current { text-decoration: underline; color: var(--titleColor); }

    .searchBox { margin-bottom: 15px; }
    .searchBox input { padding: 5px; width: 250px; }
</style>

<?php
$tables = [
    'articles' => ['getTitle','getContent','getViews','getDateCreation','getDateUpdate'],
    'comments' => ['getPseudo','getContent','getDateCreation']
];

$labels = [
    'articles' => ['Titre','Contenu','Vues','Date création','Date modification'],
    'comments' => ['Pseudo','Contenu','Date']
];

function thLink($tableName, $colName, $label, $table, $sort, $order) {
    $next = ($colName === $sort && $tableName === $table && $order === 'asc') ? 'desc' : 'asc';
    $class = ($colName === $sort && $tableName === $table) ? 'sorted' : '';
    return "<th class=\"$class\"><a href=\"?action=showMonitoring&table=$tableName&sort=$colName&order=$next\">$label</a></th>";
}

function renderRows($data, $methods, $type='articles') {
    foreach ($data as $item) {
        echo "<tr>";
        foreach ($methods as $method) {
            if (!method_exists($item, $method)) continue;
            $val = $item->$method();
            if ($val instanceof DateTime) $val = $val->format('d/m/Y');
            if (is_string($val) && strlen($val) > 200) $val = substr($val,0,200).'…';
            echo "<td>".htmlspecialchars((string)$val)."</td>";
        }
        if ($type==='comments') {
            echo "<td>
                <a href='?action=deleteComment&id=".$item->getId()."' 
                   class='submit' 
                   onclick=\"return confirm('Supprimer ce commentaire ?')\">Supprimer</a>
            </td>";
        }
        echo "</tr>";
    }
}

// --- FILTRAGE ---
$searchPseudo = $_GET['searchPseudo'] ?? '';
$filteredComments = $comments ?? [];
if ($searchPseudo !== '') {
    $filteredComments = array_filter($comments ?? [], function($c) use($searchPseudo){
        return stripos($c->getPseudo(), $searchPseudo) !== false;
    });
}

// --- PAGINATION ---
$perPage = 10;
$page = $_GET['page'] ?? 1;
$totalComments = count($filteredComments);
$totalPages = ceil($totalComments / $perPage);
$start = ($page-1)*$perPage;
$commentsPage = array_slice($filteredComments, $start, $perPage);
?>

<?php foreach (['articles','comments'] as $tableName): ?>
<section>
    <h2><?= ucfirst($tableName) ?></h2>

    <?php if($tableName==='comments'): ?>
        <div class="searchBox">
            <form method="get">
                <input type="hidden" name="action" value="showMonitoring">
                <input type="hidden" name="table" value="comments">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
                <input type="text" name="searchPseudo" placeholder="Filtrer par pseudo..." value="<?= htmlspecialchars($searchPseudo) ?>">
                <input type="submit" value="Filtrer" class="submit">
            </form>
        </div>
    <?php endif; ?>

    <table class="adminArticle">
        <thead>
            <tr>
                <?php foreach ($tables[$tableName] as $i=>$methodName):
                    $col = str_starts_with($methodName,'get') ? lcfirst(substr($methodName,3)) : $methodName;
                    echo thLink($tableName,$col,$labels[$tableName][$i],$table,$sort,$order);
                endforeach;
                if($tableName==='comments') echo '<th>Action</th>'; ?>
            </tr>
        </thead>
        <tbody>
            <?php
                if($tableName==='comments') renderRows($commentsPage, $tables[$tableName], 'comments');
                else renderRows($$tableName, $tables[$tableName]);
            ?>
        </tbody>
    </table>

    <?php if($tableName==='comments' && $totalPages>1): ?>
        <div class="pagination">
            <?php for($p=1;$p<=$totalPages;$p++): ?>
                <a href="?action=showMonitoring&table=comments&page=<?= $p ?>&sort=<?= $sort ?>&order=<?= $order ?>&searchPseudo=<?= urlencode($searchPseudo) ?>" class="<?= $p==$page?'current':'' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>
<?php endforeach; ?>

<div style="text-align:right; margin-top:20px;">
    <a class="submit" href="index.php?action=admin">Retour</a>
</div>
