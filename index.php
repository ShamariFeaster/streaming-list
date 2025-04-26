<?php
include 'main.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Streaming List</title>
    <link rel="stylesheet" href="styles.css">
    <script src="/js/TemplarJS-0.11.min.js"></script>
    <script src="/js/TemplarModel.js"></script>
    <script src="/js/TemplarCustomAttributes.js"></script>
    <script src="/js/main.js"></script>
</head>
<body>
<div class="container">
<? 


?>
    <h1>Movies and TV Shows</h1>

    <!-- Form for adding new entries -->
    <form method="POST">
        <input type="text" name="title" placeholder="Title" required>
        <select name="type">{{Media.type}}</select>
        <select name="platform">{{Media.platform}}</select>     
        <select name="parent_id">{{Media.parentSelect}}</select>
        <button type="submit" name="add_entry" class="action-button">Add</button>
    </form>

    <!-- Table for Currently Watching Items -->
    <div showIf="{{Media.currWatchingVisibile}}">
        <h2>Currently Watching</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Streaming Platform</th>
                    <th>Watched</th>
                    <th>Rating</th>
                    <th>Next Airing</th>
                </tr>
            </thead>
            <tbody>
                <tr data-apl-repeat="{{Media.currWatchingRepeat}}">
                    <td><a href="details.php?id={{id}}">{{title}}</a></td>
                    <td>{{type}}</td>
                    <td>{{streaming_platform}}</td>
                    <td>{{watched}}</td>
                    <td>{{rating}}</td>
                    <td>{{next_airing}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- New 'Up Next' Table (watched=4) -->
    <div showIf="{{Media.upNextVisibile}}">
        <h2>Up Next</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Streaming Platform</th>
                    <th>Watched</th>
                    <th>Rating</th>
                    <th>Next Airing</th>
                </tr>
            </thead>
            <tbody>
                <tr data-apl-repeat="{{Media.upNextRepeat}}">
                    <td><a href="details.php?id={{id}}">{{title}}</a></td>
                    <td>{{type}}</td>
                    <td>{{streaming_platform}}</td>
                    <td>{{watched}}</td>
                    <td>{{rating}}</td>
                    <td>{{next_airing}}</td>
                </tr>
            </tbody>
        </table>
    </div>

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

    <div class="TEST" data-apl-repeat="{{Media.allMedia}}">
        Parent : {{title}} , {{streaming_platform}}
        <div data-apl-repeat="{{$item}}.children">
            Child {{title}}  , {{streaming_platform}}
        </div>
    </div>
</div>

</body>
</html>
