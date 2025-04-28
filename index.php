<?php
include 'main.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Streaming List</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="/lib/twitter-bootstrap-5.3.5.css">
    <script src="/js/TemplarJS-0.11.min.js"></script>
    <script src="/js/TemplarModel.js"></script>
    <script src="/js/Components/ItemTable.js"></script>
    <script src="/js/Components/ColumnHeader.js"></script>
    <script src="/js/TemplarCustomAttributes.js"></script>
    <script src="/js/main.js"></script>
</head>
<body>
<div class="app-container">
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
    <ItemTable isVisible="{{Media.upNextVisibile}}" rowSource="{{Media.upNextRepeat}}">
        Currently Watching
    </ItemTable>
    
    <!-- Table for Currently Watching Items -->
    <ItemTable isVisible="{{Media.currWatchingVisibile}}" rowSource="{{Media.currWatchingRepeat}}">
        Up Next
    </ItemTable>

    <!-- Search and Platform Filters -->
    <div class="filter-row">
	<input type="text" id="searchBox" placeholder="Search titles..." onkeyup="filterTable()">
    </div>
    <div class="filter-buttons">
        <button class="streaming-filter" onclick="resetFilters()">All</button>
        
        <button data-apl-repeat="{{Media.platform}}" 
                class="streaming-filter" 
                onclick="applyFilter('{{text}}')">{{text}}</button>      
    </div>

     <!-- Table to display the list of movies and TV shows -->
    <div>
        <h2>All Media</h2>
        <div id="item-table-wrapper" class="container">
            <ColumnHeader></ColumnHeader>
            <div class="table-body">
                <div id="item-repeater-parent" class="row" data-apl-repeat="{{Media.allMedia}}">

                    <div class="col-4"><a href="details.php?id={{id}}">{{title}}</a>
                    <button showIf="{{hasChildren}}" class="toggle-children" onclick="toggleChildren({{id}})">
                        Show/Hide Children
                    </button>

                    </div>
                    <div class="col-1">{{type}}</div>
                    <div class="col-2">{{streaming_platform}}</div>
                    <div class="col-1">{{watched}}</div>
                    <div class="col-2">{{rating}}</div>
                    <div class="col-2">{{next_airing}}</div>

                    <div showIf="{{showChildren}}" class="row" data-apl-repeat="{{$item}}.children">
                        <div class="col-4"><a href="details.php?id={{id}}">{{title}}</a></div>
                        <div class="col-1">{{type}}</div>
                        <div class="col-2">{{streaming_platform}}</div>
                        <div class="col-1">{{watched}}</div>
                        <div class="col-2">{{rating}}</div>
                        <div class="col-2">{{next_airing}}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    
</div>

</body>
</html>
