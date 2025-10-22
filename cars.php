<!DOCTYPE html>
<html lang="en">
<head>
    <title>Car Database Management</title>
    <meta charset="utf-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px;
            background: #f0f2f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .nav-menu {
            text-align: center;
            margin: 20px 0;
        }
        .nav-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .nav-btn:hover {
            background: #0056b3;
        }
        .search-box {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .quick-search {
            margin: 15px 0;
        }
        .quick-search a {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 15px;
            margin: 5px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
        }
        .quick-search a:hover {
            background: #218838;
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
        tr:hover {
            background-color: #f5f5f5;
        }
        .stats {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .stat-item {
            display: inline-block;
            margin: 0 20px;
            font-weight: bold;
            color: #495057;
        }
        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
            font-size: 18px;
        }
        .price-high {
            color: #28a745;
            font-weight: bold;
        }
        .price-low {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 Car Database Management</h1>
        
        <!-- Navigation Menu -->
        <div class="nav-menu">
            <a href="cars.php" class="nav-btn">📋 All Cars</a>
            <a href="search_form.php" class="nav-btn">🔍 Advanced Search</a>
        </div>

        <!-- Quick Search Box -->
        <div class="search-box">
            <h3>Quick Search</h3>
            <form method="GET" action="search_result.php" style="margin: 15px 0;">
                <input type="text" name="model" placeholder="Enter car model..." 
                       style="padding: 10px; width: 300px; border: 2px solid #ddd; border-radius: 5px;">
                <input type="submit" value="Search" 
                       style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            </form>
            
            <div class="quick-search">
                <strong>Quick Filters:</strong>
                <a href="search_result.php?model=BMW">BMW</a>
                <a href="search_result.php?model=Toyota">Toyota</a>
                <a href="search_result.php?model=Ford">Ford</a>
                <a href="search_result.php?model=Holden">Holden</a>
                <a href="search_result.php?model=X3">X3</a>
                <a href="search_result.php?model=Corolla">Corolla</a>
            </div>
        </div>

        <?php
        // Database connection
        require_once "settings.php";
        $dbconn = @mysqli_connect($host, $user, $pwd, $sql_db);
        
        if ($dbconn) {
            // Get statistics
            $stats_query = "SELECT 
                COUNT(*) as total_cars,
                AVG(price) as avg_price,
                MIN(price) as min_price,
                MAX(price) as max_price,
                MIN(yom) as oldest_year,
                MAX(yom) as newest_year
                FROM cars";
            $stats_result = mysqli_query($dbconn, $stats_query);
            $stats = mysqli_fetch_assoc($stats_result);
            
            // Display statistics
            echo "<div class='stats'>";
            echo "<div class='stat-item'>Total Cars: <span style='color:#007bff;'>" . $stats['total_cars'] . "</span></div>";
            echo "<div class='stat-item'>Average Price: <span class='price-high'>$" . number_format($stats['avg_price']) . "</span></div>";
            echo "<div class='stat-item'>Price Range: <span class='price-low'>$" . number_format($stats['min_price']) . "</span> - <span class='price-high'>$" . number_format($stats['max_price']) . "</span></div>";
            echo "<div class='stat-item'>Years: " . $stats['oldest_year'] . " - " . $stats['newest_year'] . "</div>";
            echo "</div>";

            // Main query to get all cars
            $query = "SELECT * FROM cars ORDER BY make, model";
            $result = mysqli_query($dbconn, $query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                echo "<h2>All Cars in Database</h2>";
                echo "<table>";
                echo "<tr>
                        <th>Car ID</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Price</th>
                        <th>Year</th>
                        <th>Actions</th>
                      </tr>";
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $price_class = ($row['price'] > 25000) ? 'price-high' : 'price-low';
                    
                    echo "<tr>";
                    echo "<td>" . $row['car_id'] . "</td>";
                    echo "<td><strong>" . $row['make'] . "</strong></td>";
                    echo "<td>" . $row['model'] . "</td>";
                    echo "<td class='" . $price_class . "'>$" . number_format($row['price']) . "</td>";
                    echo "<td>" . $row['yom'] . "</td>";
                    echo "<td>
                            <a href='search_result.php?model=" . urlencode($row['model']) . "' style='color: #007bff; text-decoration: none;'>🔍</a>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='no-data'>There are no cars to display.</div>";
            }
            
            mysqli_close($dbconn);
        } else {
            echo "<div class='no-data'>Unable to connect to the database.</div>";
        }
        ?>
        
        <!-- Footer Navigation -->
        <div class="nav-menu">
            <a href="search_form.php" class="nav-btn">🔍 Advanced Search</a>
            <a href="cars.php" class="nav-btn">🔄 Refresh List</a>
        </div>
    </div>
</body>
</html>