<?php
include "db.php"; // Include the database connection

session_start(); // Start a session to access user information (if needed)

// Check if the uploads directory exists, if not, create it
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true); // Create the directory with appropriate permissions
}

// Handle the form submission for adding or editing a product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Initialize variables
    $image_path = ""; // Variable to hold the image path
    $uploadOk = 1; // Flag for upload status
    $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : null;

    // If an image is uploaded, handle the upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] != UPLOAD_ERR_NO_FILE) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $error_message = "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (limit to 2MB)
        if ($_FILES["image"]["size"] > 2000000) {
            $error_message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Try to upload the file if everything is ok
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file; // Set the image path if upload is successful
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Prepare SQL to insert or update article information
    if ($edit_id) {
        // If editing, update the product in the database
        $sql = "UPDATE products SET name='$name', description='$description', price='$price', category='$category'";
        // Only add the image field if a new image was uploaded
        if ($image_path) {
            $sql .= ", image='$image_path'";
        }
        $sql .= " WHERE id=$edit_id";
    } else {
        // If adding a new product, include image
        $sql = "INSERT INTO products (name, description, price, category, image) VALUES ('$name', '$description', '$price', '$category', '$image_path')";
    }

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php"); // Redirect to your dashboard page
        exit; // Ensure no further code is executed
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: sell.php"); // Redirect to refresh the product list
        exit;
    } else {
        $error_message = "Error deleting product: " . mysqli_error($conn);
    }
}

// Fetch all products from the database
$products = [];
$result = mysqli_query($conn, "SELECT * FROM products");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// Handle product editing
$product_to_edit = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_result = mysqli_query($conn, "SELECT * FROM products WHERE id = $edit_id");
    if ($edit_result) {
        $product_to_edit = mysqli_fetch_assoc($edit_result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Article</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file if needed -->
</head>
<body>
    
    <?php include "header.php"; ?>
    
    <form action="" method="POST" enctype="multipart/form-data"> <!-- Specify action as current file -->
        <h1><?php echo isset($product_to_edit) ? 'Edit Your Article' : 'Sell Your Article'; ?></h1>
        <input type="hidden" name="edit_id" value="<?php echo isset($product_to_edit) ? $product_to_edit['id'] : ''; ?>">
        <table>
            <tr>
                <td><label for="name">Article Name</label></td>
                <td><input type="text" id="name" name="name" placeholder="Enter article name" required value="<?php echo isset($product_to_edit) ? htmlspecialchars($product_to_edit['name']) : ''; ?>"></td>
            </tr>
            <tr>
                <td><label for="description">Description</label></td>
                <td><textarea id="description" name="description" placeholder="Enter article description" required><?php echo isset($product_to_edit) ? htmlspecialchars($product_to_edit['description']) : ''; ?></textarea></td>
            </tr>
            <tr>
                <td><label for="price">Price</label></td>
                <td><input type="number" id="price" name="price" placeholder="Enter price" required step="0.01" value="<?php echo isset($product_to_edit) ? htmlspecialchars($product_to_edit['price']) : ''; ?>"></td>
            </tr>
            <tr>
                <td><label for="category">Category</label></td>
                <td>
                    <select id="category" name="category" required>
                        <option value="" disabled <?php echo !isset($product_to_edit) ? 'selected' : ''; ?>>Select a category</option>
                        <option value="Electronics" <?php echo isset($product_to_edit) && $product_to_edit['category'] == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                        <option value="Clothing" <?php echo isset($product_to_edit) && $product_to_edit['category'] == 'Clothing' ? 'selected' : ''; ?>>Clothing</option>
                        <option value="Home & Garden" <?php echo isset($product_to_edit) && $product_to_edit['category'] == 'Home & Garden' ? 'selected' : ''; ?>>Home & Garden</option>
                        <option value="Toys" <?php echo isset($product_to_edit) && $product_to_edit['category'] == 'Toys' ? 'selected' : ''; ?>>Toys</option>
                        <option value="Sports" <?php echo isset($product_to_edit) && $product_to_edit['category'] == 'Sports' ? 'selected' : ''; ?>>Sports</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="image">Product Image</label></td>
                <td>
                    <input type="file" id="image" name="image" accept="image/*">
                    <?php if (isset($product_to_edit) && $product_to_edit['image']): ?>
                        <p>Current Image: <img src="<?php echo htmlspecialchars($product_to_edit['image']); ?>" alt="<?php echo htmlspecialchars($product_to_edit['name']); ?>" style="width: 100px; height: auto;"></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <center><button type="submit"><?php echo isset($product_to_edit) ? 'Update' : 'Post'; ?></button></center>
    </form>

    <!-- Display error messages -->
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Display all posted products -->
    <h2>Posted Products</h2>
    <?php if (!empty($products)): ?>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100px; height: auto;"></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td>$<?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td>
                        <a href="?edit=<?php echo $product['id']; ?>">Edit</a> | 
                        <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No products posted yet.</p>
    <?php endif; ?>

</body>
</html> 