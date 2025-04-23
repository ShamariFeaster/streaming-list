<?php
 // Include the configuration file for database connection
include 'config.php'; 

// Set the default sort field to 'title' and direction to 'asc'
//$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'title';
//$sort_direction = (isset($_GET['dir']) && $_GET['dir'] === 'desc') ? 'desc' : 'asc';

// Validate & sanitize the sort field
// $allowed_fields = ['title', 'type', 'streaming_platform', 'watched', 'rating', 'date_added', 'next_airing'];
// if (!in_array($sort_field, $allowed_fields)) {
//     $sort_field = 'title';
// }

// Fetch parent entries
$parent_stmt = $pdo->prepare("SELECT * FROM media WHERE parent_id IS NULL ORDER BY title asc");
$parent_stmt->execute();
$parent_list = $parent_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch child entries
$children_stmt = $pdo->prepare("SELECT * FROM media WHERE parent_id IS NOT NULL ORDER BY title ASC");
$children_stmt->execute();
$children_by_parent = [];
while ($child = $children_stmt->fetch(PDO::FETCH_ASSOC)) {
    $children_by_parent[$child['parent_id']][] = $child;
}







// Fetch currently watching items (watched IN (2,3))
$currently_watching_stmt = $pdo->prepare("
    SELECT * FROM media 
    WHERE watched IN (2, 3)
    ORDER BY title asc
");
$currently_watching_stmt->execute();
$currently_watching_list = $currently_watching_stmt->fetchAll(PDO::FETCH_ASSOC);
$currWatchingJson = json_encode($currently_watching_list);

// Fetch "Up Next" items (watched=4)
//title and asc could be parameterized
$up_next_stmt = $pdo->prepare("
    SELECT * FROM media
    WHERE watched = 4
    ORDER BY title asc
");
$up_next_stmt->execute();
$up_next_list = $up_next_stmt->fetchAll(PDO::FETCH_ASSOC);
$upNextJson = json_encode($up_next_list);

// Fetch all parent options for the dropdown
$all_parents_stmt = $pdo->prepare("SELECT id as 'value', title as 'text' FROM media WHERE parent_id IS NULL ORDER BY title ASC");
$all_parents_stmt->execute();
$all_parents = $all_parents_stmt->fetchAll(PDO::FETCH_ASSOC);
$all_parents[] = ['text' => 'No Parent', 'value' => '' ];
$parentJson = json_encode($all_parents);

?>
<script>
//all this to be passed into front-end data model in /js/TemplarModel.js
var _currWatchingData = <?=$currWatchingJson;?>;
_currWatchingVisibile = (currWatchingData.length > 0);
var _upNextData = <?=$upNextJson;?>;
_upNextVisibile = (upNextData.length > 0);
var parentSelectData = <?=$parentJson;?>;
</script>

<?
// Handle form submission for adding new entries
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_entry'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $platform = $_POST['platform'];
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : NULL;

    $stmt = $pdo->prepare('
        INSERT INTO media (title, type, streaming_platform, parent_id) 
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$title, $type, $platform, $parent_id]);

    header('Location: index.php');
    exit;
}

// Handle marking as watched
if (isset($_GET['mark_watched'])) {
    $id = $_GET['mark_watched'];
    $stmt = $pdo->prepare('UPDATE media SET watched = 1 WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: index.php');
    exit;
}

// Sort direction toggling
function get_sort_link($field, $current_field, $current_direction) {
    $next_direction = ($current_field === $field && $current_direction === 'asc') ? 'desc' : 'asc';
    return "?sort=$field&dir=$next_direction";
}

// Generate star ratings
function display_stars($rating) {
    return $rating > 0
        ? str_repeat('★', $rating) . str_repeat('☆', 5 - $rating)
        : '-';
}

// Display watched status
function display_watched_status($watched) {
    switch ($watched) {
        case 1: return 'Watched';
        case 2: return 'Rewatching';
        case 3: return 'Watching';
        case 4: return 'Up Next';
        default: return 'Not Watched';
    }
}

?>