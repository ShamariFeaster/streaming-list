<?php
include 'config.php';  // Include the configuration file for database connection

// Set the default sort field to 'title' and direction to 'asc'
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$sort_direction = (isset($_GET['dir']) && $_GET['dir'] === 'desc') ? 'desc' : 'asc';

// Validate & sanitize the sort field
$allowed_fields = ['title', 'type', 'streaming_platform', 'watched', 'rating', 'date_added', 'next_airing'];
if (!in_array($sort_field, $allowed_fields)) {
    $sort_field = 'title';
}

// Fetch parent entries
$parent_stmt = $pdo->prepare("SELECT * FROM media WHERE parent_id IS NULL ORDER BY $sort_field $sort_direction");
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
    ORDER BY $sort_field $sort_direction
");
$currently_watching_stmt->execute();
$currently_watching_list = $currently_watching_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch "Up Next" items (watched=4)
$up_next_stmt = $pdo->prepare("
    SELECT * FROM media
    WHERE watched = 4
    ORDER BY $sort_field $sort_direction
");
$up_next_stmt->execute();
$up_next_list = $up_next_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all parent options for the dropdown
$all_parents_stmt = $pdo->prepare("SELECT * FROM media WHERE parent_id IS NULL ORDER BY title ASC");
$all_parents_stmt->execute();
$all_parents = $all_parents_stmt->fetchAll(PDO::FETCH_ASSOC);

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Streaming List</title>
    <link rel="stylesheet" href="styles.css">

    <script>
        function filterTable() {
            let searchBox = document.getElementById('searchBox').value.toLowerCase();
            let table = document.getElementById('mainTable');
            let rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) { // skip header row
                let cells = rows[i].getElementsByTagName('td');
                let match = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        let textValue = cells[j].textContent || cells[j].innerText;
                        if (textValue.toLowerCase().indexOf(searchBox) > -1) {
                            match = true;
                            break;
                        }
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        }

        function applyFilter(platform = '', watched = '') {
            let table = document.getElementById('mainTable');
            let rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let platformCell = rows[i].getElementsByTagName('td')[2];
                let watchedCell  = rows[i].getElementsByTagName('td')[3];

                if (platformCell && watchedCell) {
                    let platformMatch = (platform === '' || platformCell.textContent === platform);
                    let watchedMatch  = (watched  === '' || watchedCell.textContent === watched);

                    rows[i].style.display = (platformMatch && watchedMatch) ? '' : 'none';
                }
            }
        }

        function resetFilters() {
            let table = document.getElementById('mainTable');
            let rows = table.getElementsByTagName('tr');
            document.getElementById('searchBox').value = '';

            for (let i = 1; i < rows.length; i++) {
                rows[i].style.display = '';
            }
        }

        function toggleChildren(parentId) {
            let children = document.getElementsByClassName('child-of-' + parentId);
            for (let i = 0; i < children.length; i++) {
                children[i].style.display = 
                    (children[i].style.display === 'none' || children[i].style.display === '') 
                    ? 'table-row' : 'none';
            }
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Movies and TV Shows</h1>

    <!-- Form for adding new entries -->
    <form method="POST">
        <input type="text" name="title" placeholder="Title" required>
        <select name="type">
            <option value="movie">Movie</option>
            <option value="tv_show">TV Show</option>
            <option value="franchise">Franchise</option>
            <option value="series">Series</option>
        </select>
        <select name="platform">
            <option value="AMC+">AMC+</option>
            <option value="AppleTV">AppleTV</option>
            <option value="Britbox">Britbox</option>
            <option value="Discovery+">Discovery+</option>
            <option value="Hulu">Hulu</option>
            <option value="MAX">MAX</option>
            <option value="MGM+">MGM+</option>
            <option value="Netflix">Netflix</option>
            <option value="Paramount+">Paramount+</option>
            <option value="PBS Masterpiece">PBS Masterpiece</option>
            <option value="Peacock">Peacock</option>
            <option value="Prime">Prime</option>
            <option value="Roku">Roku</option>
            <option value="Showtime">Showtime</option>
            <option value="Tubi">Tubi</option>
        </select>
        <select name="parent_id">
            <option value="">No Parent</option>
            <?php foreach ($all_parents as $parent): ?>
                <option value="<?= $parent['id'] ?>">
                    <?= htmlspecialchars($parent['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add_entry" class="action-button">Add</button>
    </form>

    <!-- Table for Currently Watching Items -->
    <?php if (count($currently_watching_list) > 0): ?>
        <h2>Currently Watching</h2>
        <table>
            <thead>
                <tr>
                    <th><a href="<?= get_sort_link('title', $sort_field, $sort_direction) ?>">Title</a></th>
                    <th><a href="<?= get_sort_link('type', $sort_field, $sort_direction) ?>">Type</a></th>
                    <th><a href="<?= get_sort_link('streaming_platform', $sort_field, $sort_direction) ?>">Streaming Platform</a></th>
                    <th><a href="<?= get_sort_link('watched', $sort_field, $sort_direction) ?>">Watched</a></th>
                    <th><a href="<?= get_sort_link('rating', $sort_field, $sort_direction) ?>">Rating</a></th>
                    <th><a href="<?= get_sort_link('next_airing', $sort_field, $sort_direction) ?>">Next Airing</a></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($currently_watching_list as $media): ?>
                    <tr>
                        <td><a href="details.php?id=<?= $media['id'] ?>"><?= htmlspecialchars($media['title']) ?></a></td>
                        <td><?= htmlspecialchars($media['type']) ?></td>
                        <td><?= htmlspecialchars($media['streaming_platform']) ?></td>
                        <td><?= display_watched_status($media['watched']) ?></td>
                        <td><?= display_stars($media['rating']) ?></td>
                        <td>
                            <?= !empty($media['next_airing'])
                                ? date('Y-m-d', strtotime($media['next_airing']))
                                : '-'
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- New 'Up Next' Table (watched=4) -->
    <?php if (count($up_next_list) > 0): ?>
        <h2>Up Next</h2>
        <table>
            <thead>
                <tr>
                    <th><a href="<?= get_sort_link('title', $sort_field, $sort_direction) ?>">Title</a></th>
                    <th><a href="<?= get_sort_link('type', $sort_field, $sort_direction) ?>">Type</a></th>
                    <th><a href="<?= get_sort_link('streaming_platform', $sort_field, $sort_direction) ?>">Streaming Platform</a></th>
                    <th><a href="<?= get_sort_link('watched', $sort_field, $sort_direction) ?>">Watched</a></th>
                    <th><a href="<?= get_sort_link('rating', $sort_field, $sort_direction) ?>">Rating</a></th>
                    <th><a href="<?= get_sort_link('next_airing', $sort_field, $sort_direction) ?>">Next Airing</a></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($up_next_list as $media): ?>
                    <tr>
                        <td>
                            <a href="details.php?id=<?= $media['id'] ?>">
                                <?= htmlspecialchars($media['title']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($media['type']) ?></td>
                        <td><?= htmlspecialchars($media['streaming_platform']) ?></td>
                        <td><?= display_watched_status($media['watched']) ?></td>
                        <td><?= display_stars($media['rating']) ?></td>
                        <td>
                            <?= !empty($media['next_airing'])
                                ? date('Y-m-d', strtotime($media['next_airing']))
                                : '-'
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Search and Platform Filters -->
    <div class="filter-row">
	<input type="text" id="searchBox" placeholder="Search titles..." onkeyup="filterTable()">
    </div>
    <div class="filter-buttons">
        <button class="streaming-filter" onclick="resetFilters()">All</button>
        <button class="streaming-filter" onclick="applyFilter('AMC+')">AMC+</button>
        <button class="streaming-filter" onclick="applyFilter('AppleTV')">AppleTV</button>
        <button class="streaming-filter" onclick="applyFilter('Britbox')">Britbox</button>
        <button class="streaming-filter" onclick="applyFilter('Discovery+')">Discovery+</button>
        <button class="streaming-filter" onclick="applyFilter('Hulu')">Hulu</button>
        <button class="streaming-filter" onclick="applyFilter('MAX')">MAX</button>
        <button class="streaming-filter" onclick="applyFilter('MGM+')">MGM+</button>
        <button class="streaming-filter" onclick="applyFilter('Netflix')">Netflix</button>
        <button class="streaming-filter" onclick="applyFilter('Paramount+')">Paramount+</button>
        <button class="streaming-filter" onclick="applyFilter('PBS Masterpiece')">PBS Masterpiece</button>
        <button class="streaming-filter" onclick="applyFilter('Peacock')">Peacock</button>
        <button class="streaming-filter" onclick="applyFilter('Prime')">Prime</button>
        <button class="streaming-filter" onclick="applyFilter('Roku')">Roku</button>
        <button class="streaming-filter" onclick="applyFilter('Showtime')">Showtime</button>
        <button class="streaming-filter" onclick="applyFilter('Tubi')">Tubi</button>
        <button class="streaming-filter" onclick="applyFilter('', 'Not Watched')">Not Watched</button>
    </div>

    <!-- Table to display the list of movies and TV shows -->
    <h2>All Media</h2>
    <table id="mainTable">
        <thead>
            <tr>
                <th><a href="<?= get_sort_link('title', $sort_field, $sort_direction) ?>">Title</a></th>
                <th><a href="<?= get_sort_link('type', $sort_field, $sort_direction) ?>">Type</a></th>
                <th><a href="<?= get_sort_link('streaming_platform', $sort_field, $sort_direction) ?>">Streaming Platform</a></th>
                <th><a href="<?= get_sort_link('watched', $sort_field, $sort_direction) ?>">Watched</a></th>
                <th><a href="<?= get_sort_link('rating', $sort_field, $sort_direction) ?>">Rating</a></th>
                <th><a href="<?= get_sort_link('date_added', $sort_field, $sort_direction) ?>">Date Added</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parent_list as $parent): ?>
                <tr>
                    <td>
                        <a href="details.php?id=<?= $parent['id'] ?>">
                            <?= htmlspecialchars($parent['title']) ?>
                        </a>
                        <?php if (isset($children_by_parent[$parent['id']])): ?>
                            <button class="toggle-children" onclick="toggleChildren(<?= $parent['id'] ?>)">
                                Show/Hide Children
                            </button>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($parent['type']) ?></td>
                    <td><?= htmlspecialchars($parent['streaming_platform']) ?></td>
                    <td><?= display_watched_status($parent['watched']) ?></td>
                    <td><?= display_stars($parent['rating']) ?></td>
                    <td><?= htmlspecialchars($parent['date_added']) ?></td>
                </tr>
                <?php if (isset($children_by_parent[$parent['id']])): ?>
                    <?php foreach ($children_by_parent[$parent['id']] as $child): ?>
                        <tr class="child-of-<?= $parent['id'] ?>" style="display: none;">
                            <td>
                                <a href="details.php?id=<?= $child['id'] ?>">
                                    <?= htmlspecialchars($child['title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($child['type']) ?></td>
                            <td><?= htmlspecialchars($child['streaming_platform']) ?></td>
                            <td><?= display_watched_status($child['watched']) ?></td>
                            <td><?= display_stars($child['rating']) ?></td>
                            <td><?= htmlspecialchars($child['date_added']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
