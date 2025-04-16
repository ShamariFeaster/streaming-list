<?php
include 'config.php'; // Include the configuration file for database connection

// Fetch media details based on ID
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
$stmt->execute([$id]);
$media = $stmt->fetch(PDO::FETCH_ASSOC);

// Convert next_airing from DB format to YYYY-MM-DD for <input type='date'>
$next_airing_value = '';
if (!empty($media['next_airing'])) {
    $next_airing_value = date('Y-m-d', strtotime($media['next_airing']));
}

// Fetch all parent options for the dropdown, ordered alphabetically
$all_parents_stmt = $pdo->prepare("SELECT * FROM media WHERE parent_id IS NULL ORDER BY title ASC");
$all_parents_stmt->execute();
$all_parents = $all_parents_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch child entries for the current media
$children_stmt = $pdo->prepare("SELECT * FROM media WHERE parent_id = ? ORDER BY title ASC");
$children_stmt->execute([$id]);
$children_list = $children_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to save changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    $title       = $_POST['title'];
    $type        = $_POST['type'];
    $platform    = $_POST['platform'];
    $watched     = $_POST['watched'];
    $rating      = $_POST['rating'];
    $parent_id   = !empty($_POST['parent_id']) ? $_POST['parent_id'] : NULL;

    // Convert blank "next_airing" field to NULL
    $next_airing = !empty($_POST['next_airing'])
        ? date('Y-m-d', strtotime($_POST['next_airing']))
        : NULL;
    
    $notes = $_POST['notes'];

    $update_stmt = $pdo->prepare("
        UPDATE media
        SET 
            title              = ?, 
            type               = ?, 
            streaming_platform = ?, 
            watched            = ?, 
            rating             = ?, 
            parent_id          = ?, 
            next_airing        = ?, 
            notes              = ?
        WHERE id = ?
    ");
    $update_stmt->execute([
        $title,
        $type,
        $platform,
        $watched,
        $rating,
        $parent_id,
        $next_airing,
        $notes,
        $id
    ]);

    header('Location: details.php?id=' . $id);
    exit;
}

// Function to display watched status
function display_watched_status($watched) {
    switch ($watched) {
        case 1: return 'Watched';
        case 2: return 'Rewatching';
        case 3: return 'Watching';
        case 4: return 'Up Next'; // NEW CASE
        default: return 'Not Watched';
    }
}

// Function to generate star ratings
function display_stars($rating) {
    if ($rating > 0) {
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    } else {
        return '-';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Details - <?= htmlspecialchars($media['title']) ?></title>
    <style>
        /* SOLARIZED DARK THEME, inline for details.php */

        /* Base background is #002b36, with text #fdf6e3 */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #002b36; /* Solarized Dark Base */
            color: #fdf6e3;           /* Light text */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .container {
            /* ~80% container width, with max width so it doesn’t get huge */
            width: 80%;
            max-width: 800px;
            background-color: #073642; /* Slightly lighter dark color */
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #b58900; /* Solarized Yellow for heading */
            font-size: 2em;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            flex-direction: column; /* vertical stack for form controls */
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column; /* label above input */
            margin-bottom: 10px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #93a1a1; /* Lighter grayish color */
        }
        /* text fields, selects, textareas */
        form input[type="text"],
        form input[type="date"],
        form textarea,
        form select {
            padding: 10px;
            border: 1px solid #586e75; /* Solarized base01 */
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #002b36; /* darkest background */
            color: #fdf6e3;
            width: 90%;   /* ~90% width of container */
        }
        form textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-buttons {
            display: flex;
            justify-content: space-between;
        }
        .form-buttons button,
        .form-buttons a.button-link {
            padding: 10px 20px;
            background-color: #268bd2; /* Solarized Blue */
            color: #fdf6e3;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .form-buttons button:hover,
        .form-buttons a.button-link:hover {
            background-color: #2aa198; /* Solarized Cyan */
        }

        /* Table styling in solarized dark */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #073642; /* slightly lighter dark */
        }
        table, th, td {
            border: 1px solid #586e75; /* base01 color */
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #268bd2; /* Solarized Blue */
            color: #fdf6e3;           /* Light text */
        }
        td a {
            color: #b58900; /* solarized yellow for links in table */
            text-decoration: none;
        }
        td a:hover {
            color: #cb4b16; /* solarized orange */
        }
    </style>
</head>
<body>
<div class="container">
    <h1><?= htmlspecialchars($media['title']) ?></h1>

    <form method="POST">
        <div class="form-group">
            <label for="title">Title:</label>
            <input 
                type="text"
                name="title"
                value="<?= htmlspecialchars($media['title']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="type">Type:</label>
            <select name="type">
                <option value="movie"     <?= ($media['type'] == 'movie')     ? 'selected' : '' ?>>Movie</option>
                <option value="tv_show"   <?= ($media['type'] == 'tv_show')   ? 'selected' : '' ?>>TV Show</option>
                <option value="franchise" <?= ($media['type'] == 'franchise') ? 'selected' : '' ?>>Franchise</option>
                <option value="series"    <?= ($media['type'] == 'series')    ? 'selected' : '' ?>>Series</option>
            </select>
        </div>

        <div class="form-group">
            <label for="platform">Platform:</label>
            <select name="platform">
                <option value="AMC+"             <?= ($media['streaming_platform'] == 'AMC+')            ? 'selected' : '' ?>>AMC+</option>
                <option value="AppleTV"          <?= ($media['streaming_platform'] == 'AppleTV')         ? 'selected' : '' ?>>AppleTV</option>
                <option value="Britbox"          <?= ($media['streaming_platform'] == 'Britbox')         ? 'selected' : '' ?>>Britbox</option>
                <option value="Discovery+"       <?= ($media['streaming_platform'] == 'Discovery+')      ? 'selected' : '' ?>>Discovery+</option>
                <option value="Hulu"             <?= ($media['streaming_platform'] == 'Hulu')            ? 'selected' : '' ?>>Hulu</option>
                <option value="MAX"              <?= ($media['streaming_platform'] == 'MAX')             ? 'selected' : '' ?>>MAX</option>
                <option value="MGM+"             <?= ($media['streaming_platform'] == 'MGM+')            ? 'selected' : '' ?>>MGM+</option>
                <option value="Netflix"          <?= ($media['streaming_platform'] == 'Netflix')         ? 'selected' : '' ?>>Netflix</option>
                <option value="Paramount+"       <?= ($media['streaming_platform'] == 'Paramount+')      ? 'selected' : '' ?>>Paramount+</option>
                <option value="PBS Masterpiece"  <?= ($media['streaming_platform'] == 'PBS Masterpiece') ? 'selected' : '' ?>>PBS Masterpiece</option>
                <option value="Peacock"          <?= ($media['streaming_platform'] == 'Peacock')         ? 'selected' : '' ?>>Peacock</option>
                <option value="Prime"            <?= ($media['streaming_platform'] == 'Prime')           ? 'selected' : '' ?>>Prime</option>
                <option value="Roku"             <?= ($media['streaming_platform'] == 'Roku')            ? 'selected' : '' ?>>Roku</option>
                <option value="Showtime"         <?= ($media['streaming_platform'] == 'Showtime')        ? 'selected' : '' ?>>Showtime</option>
                <option value="Tubi"             <?= ($media['streaming_platform'] == 'Tubi')            ? 'selected' : '' ?>>Tubi</option>
            </select>
        </div>

        <div class="form-group">
            <label for="watched">Watched Status:</label>
            <select name="watched">
                <option value="0" <?= ($media['watched'] == 0) ? 'selected' : '' ?>>Not Watched</option>
                <option value="1" <?= ($media['watched'] == 1) ? 'selected' : '' ?>>Watched</option>
                <option value="2" <?= ($media['watched'] == 2) ? 'selected' : '' ?>>Rewatching</option>
                <option value="3" <?= ($media['watched'] == 3) ? 'selected' : '' ?>>Watching</option>
                <option value="4" <?= ($media['watched'] == 4) ? 'selected' : '' ?>>Up Next</option>
            </select>
        </div>

        <div class="form-group">
            <label for="rating">Rating:</label>
            <select name="rating">
                <option value="0" <?= ($media['rating'] == 0) ? 'selected' : '' ?>>0 stars</option>
                <option value="1" <?= ($media['rating'] == 1) ? 'selected' : '' ?>>1 star</option>
                <option value="2" <?= ($media['rating'] == 2) ? 'selected' : '' ?>>2 stars</option>
                <option value="3" <?= ($media['rating'] == 3) ? 'selected' : '' ?>>3 stars</option>
                <option value="4" <?= ($media['rating'] == 4) ? 'selected' : '' ?>>4 stars</option>
                <option value="5" <?= ($media['rating'] == 5) ? 'selected' : '' ?>>5 stars</option>
            </select>
        </div>

        <div class="form-group">
            <label for="parent_id">Parent:</label>
            <select name="parent_id">
                <option value="">No Parent</option>
                <?php foreach ($all_parents as $parent): ?>
                    <option value="<?= $parent['id'] ?>"
                        <?= ($media['parent_id'] == $parent['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($parent['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="next_airing">Next Airing:</label>
            <input
                type="date"
                name="next_airing"
                id="next_airing"
                value="<?= htmlspecialchars($next_airing_value) ?>"
            >
        </div>

        <div class="form-group">
            <label for="notes">Notes:</label>
            <textarea name="notes"><?= htmlspecialchars($media['notes']) ?></textarea>
        </div>

        <div class="form-buttons">
            <button type="submit" name="save_changes">Save Changes</button>
            <a href="index.php" class="button-link">Cancel</a>
        </div>
    </form>

    <!-- Display child entries if they exist -->
    <?php if (count($children_list) > 0): ?>
        <h2>Children</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Platform</th>
                    <th>Watched</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($children_list as $child): ?>
                    <tr>
                        <td>
                            <a href="details.php?id=<?= $child['id'] ?>">
                                <?= htmlspecialchars($child['title']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($child['type']) ?></td>
                        <td><?= htmlspecialchars($child['streaming_platform']) ?></td>
                        <td><?= display_watched_status($child['watched']) ?></td>
                        <td><?= display_stars($child['rating']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
