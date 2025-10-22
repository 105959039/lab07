<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Results</title>
    <meta charset="utf-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px;
            background: #f0f2f5;
        }
        .results-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .no-results {
            background: #fff3cd;
            color: #856404;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            margin: 20px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 5px;
        }
        .back-link:hover {
            background: #007bff;
            color: white;
        }
        .search-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #0066cc;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="results-container">
        <?php
        // Database connection
        require_once("settings.php");
        
        // Check if connection was successful
        if (!$conn) {
            echo "<div class='error'>Database connection failed: " . mysqli_connect_error() . "</div>";
        } elseif (isset($_GET['model'])) {
            $model = mysqli_real_escape_string($conn, $_GET['model']);
            
            echo "<div class='search-info'>";
            echo "<strong>Searching for:</strong> '" . htmlspecialchars($model) . "'";
            echo "</div>";
            
            // CORRECTED QUERY - using just 'cars' as table name
            $sql = "SELECT * FROM cars WHERE model LIKE '%$model%' OR make LIKE '%$model%'";
            $result = mysqli_query($conn, $sql);  // Make sure this is mysqli_query, not mysql_query

            // Check if query was successful
            if (!$result) {
                echo "<div class='error'>Query error: " . mysqli_error($conn) . "</div>";
                echo "<div class='error'>SQL: " . htmlspecialchars($sql) . "</div>";
            } elseif (mysqli_num_rows($result) > 0) {
                echo "<h2>🎯 Search Results</h2>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Make</th><th>Model</th><th>Price</th><th>Year</th></tr>";
                
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['car_id'] . "</td>";
                    echo "<td>" . $row['make'] . "</td>";
                    echo "<td>" . $row['model'] . "</td>";
                    echo "<td>$" . number_format($row['price']) . "</td>";
                    echo "<td>" . $row['yom'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                echo "<p><strong>" . mysqli_num_rows($result) . " car(s) found</strong></p>";
            } else {
                echo "<div class='no-results'>";
                echo "🚫 No matching cars found for '" . htmlspecialchars($model) . "'";
                echo "</div>";
            }
        } else {
            echo "<div class='no-results'>Please enter a model to search.</div>";
        }

        // Close connection if it exists
        if (isset($conn)) {
            mysqli_close($conn);
        }
        ?>
        
        <a href="search_form.php" class="back-link">← New Search</a>
        <a href="cars.php" class="back-link">📋 View All Cars</a>
    </div>
</body>
</html>