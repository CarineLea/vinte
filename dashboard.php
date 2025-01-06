<?php
include "db.php"; // Include the database connection
session_start(); // Start session to access user information

// Fetch all products from the database
$products = [];
$result = mysqli_query($conn, "SELECT * FROM products");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['likes'] = 0; // Initialize likes count
        $row['comments'] = []; // Initialize comments array

        // Fetch likes count for the product
        $like_result = mysqli_query($conn, "SELECT COUNT(*) as like_count FROM likes WHERE product_id = " . $row['id']);
        if ($like_row = mysqli_fetch_assoc($like_result)) {
            $row['likes'] = $like_row['like_count'];
        }

        // Fetch comments for the product
        $comment_result = mysqli_query($conn, "SELECT * FROM comments WHERE product_id = " . $row['id']);
        while ($comment_row = mysqli_fetch_assoc($comment_result)) {
            $row['comments'][] = $comment_row;
        }

        $products[] = $row;
    }
}

// Handle likes
if (isset($_POST['like_product'])) {
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id']; // Assume user_id is stored in session
    $like_check = mysqli_query($conn, "SELECT * FROM likes  WHERE product_id = $product_id AND user_id = $user_id");
    
    if (mysqli_num_rows($like_check) == 0) {
        mysqli_query($conn, "INSERT INTO likes (product_id, user_id) VALUES ($product_id, $user_id)");
    }
    header("Location: dashboard.php"); // Redirect to avoid resubmission
    exit;
}

// Handle comments
if (isset($_POST['comment_product'])) {
    $product_id = intval($_POST['product_id']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $user_id = $_SESSION['user_id']; // Assume user_id is stored in session
    
    mysqli_query($conn, "INSERT INTO comments (product_id, user_id, comment) VALUES ($product_id, $user_id, '$comment')");
    header("Location: dashboard.php"); // Redirect to avoid resubmission
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file if needed -->
    <link rel="stylesheet" href="fontawesome-free-6.4.0-web/css/all.min.css">
</head>
<body>

<?php include "header.php"; ?>

<h1>All Products</h1>

<!-- Display all posted products -->
<?php if (!empty($products)): ?>
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Likes</th>
            <th>Comments</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100px; height: auto;"></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td>$<?php echo htmlspecialchars($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="like_product">Like</button>
                    </form>
                    <span><?php echo $product['likes'];?> Likes</span>
                </td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="text" name="comment" placeholder="Add a comment" required>
                        <button type="submit" name="comment_product">Comment</button>
                    </form>
                    <ul>
                        <?php foreach ($product['comments'] as $comment): ?>
                            <li><?php echo htmlspecialchars($comment['comment']); ?> - <small><?php echo $comment['created_at']; ?></small></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No products posted yet.</p>
<?php endif; ?>

</body>
</html>