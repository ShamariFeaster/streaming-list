![Screenshot](movies-shows_site.png)

This is a refactor of the original project to a MVC-based design. This allows the UI
to be much more flexible to be be modified. It also, IMO, simplifies the codebase. There's also some improvements to the usibility via some self-documenting code, particularly in config.php


## Streaming List Tracker

A simple PHP/MySQL app to keep track of movies, shows, franchises, or anything you're currently watching.

### Setup

1. Create a database called `streaming_list` and import the schema from `schema.sql`.

2. Edit `config.php` and fill in your database connection info.

3. Place all the PHP files in the same directory (like `index.php`, `details.php`, etc). Make sure `styles.css` is there too.

That’s it. You should be able to open `index.php` in your browser and start adding stuff.

If you run into any issues, double check your DB config or file permissions.

### Docker
To run the app with Docker, use the provided `Dockerfile` and `docker-compose.yml` which set up PHP with Apache and a MySQL database:

`docker compose up --build`

---

### How to Use It

- Use the form at the top of the page to add a new movie, show, series, or franchise.

- You can group things together using the **parent** dropdown.  
  For example:
  - Add “Breaking Bad” with no parent.
  - Then add individual seasons and set “Breaking Bad” as the parent.

- Click any title to view or edit its details — like rating, watched status, notes, or when the next episode airs.

- The **Currently Watching** and **Up Next** sections will update based on your watched status.

- Sort the tables by clicking column headers, and use the filter buttons to narrow things down by platform or status.

- Edit both `index.php` and `details.php` when modifying/adding streaming sources.

---
