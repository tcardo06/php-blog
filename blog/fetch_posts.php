<?php
require '../db_connection.php';

// Initialize the base query
$query = "
    SELECT p.id, p.title, p.content, p.created_at, u.username, GROUP_CONCAT(t.name SEPARATOR ', ') AS tags
    FROM posts p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN post_tags pt ON p.id = pt.post_id
    LEFT JOIN tags t ON pt.tag_id = t.id
    WHERE 1=1
";

// Initialize an array to hold the parameters
$params = [];
$types = '';

// Check for title parameter
if (!empty($_GET['title'])) {
    $title = '%' . $_GET['title'] . '%';
    $query .= " AND p.title LIKE ?";
    $params[] = $title;
    $types .= 's';
}

// Check for author parameter
if (!empty($_GET['author'])) {
    $author = $_GET['author'];
    $query .= " AND u.username = ?";
    $params[] = $author;
    $types .= 's';
}

// Check for tag parameter
if (!empty($_GET['tag'])) {
    $tag = $_GET['tag'];
    $query .= " AND t.name = ?";
    $params[] = $tag;
    $types .= 's';
}

$query .= " GROUP BY p.id ORDER BY p.created_at DESC";

// Prepare the statement
$stmt = $conn->prepare($query);

// Check if the statement preparation was successful
if (!$stmt) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}

// Bind the parameters dynamically
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute the statement
if (!$stmt->execute()) {
    echo "Error executing statement: " . $stmt->error;
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
        echo '<div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <a href="post.php?id=' . htmlspecialchars($post['id']) . '" class="card-header-link" style="text-decoration: none; color: inherit;">
                            ' . htmlspecialchars($post['title']) . '
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Posté par ' . htmlspecialchars($post['username']) . ' le ' . (new DateTime($post['created_at']))->format('d/m/Y H:i') . '
                        </p>
                        <p class="tags" style="font-size: 1.0em; color: #777;">
                            ' . htmlspecialchars($post['tags']) . '
                        </p>
                        <p>
                            ' . (strlen($post['content']) > 100 ? substr(htmlspecialchars($post['content']), 0, 90) . '...' : htmlspecialchars($post['content'])) . '
                        </p>
                        <a href="post.php?id=' . htmlspecialchars($post['id']) . '" class="btn btn-primary">En savoir plus</a>
                    </div>
                </div>
            </div>';
    }
} else {
    echo '<div class="col-12">
            <p class="text-center">No posts found.</p>
          </div>';
}
?>
