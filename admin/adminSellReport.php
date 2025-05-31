<?php
if(!isset($_SESSION)){
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if(isset($_SESSION['is_admin_login'])){
    $adminEmail = $_SESSION['admin_email'];
} else {
    echo "<script> location.href='../index.php'; </script>";
}

// Default date range (last 30 days)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

// Process filters if submitted
if(isset($_GET['generate_report'])) {
    $start_date = $_GET['start_date'] ?? $start_date;
    $end_date = $_GET['end_date'] ?? $end_date;
}

// Get summary data
$summary_query = $conn->prepare("SELECT 
    COUNT(*) as total_orders,
    SUM(course_price) as total_revenue,
    AVG(course_price) as avg_order_value
    FROM courseorder 
    WHERE status = 'completed' 
    AND order_date BETWEEN ? AND ?");
$summary_query->bind_param("ss", $start_date, $end_date);
$summary_query->execute();
$summary_result = $summary_query->get_result();
$summary = $summary_result->fetch_assoc();
$summary_query->close();

// Get sales by course
$course_sales_query = $conn->prepare("SELECT 
    c.course_id,
    c.course_name,
    COUNT(co.order_id) as sales_count,
    SUM(co.course_price) as total_revenue
    FROM courseorder co
    JOIN course c ON co.course_id = c.course_id
    WHERE co.status = 'completed'
    AND co.order_date BETWEEN ? AND ?
    GROUP BY c.course_id
    ORDER BY total_revenue DESC");
$course_sales_query->bind_param("ss", $start_date, $end_date);
$course_sales_query->execute();
$course_sales = $course_sales_query->get_result();
$course_sales_query->close();

// Get daily sales data for chart
$daily_sales_query = $conn->prepare("SELECT 
    DATE(order_date) as sale_date,
    COUNT(*) as order_count,
    SUM(course_price) as daily_revenue
    FROM courseorder
    WHERE status = 'completed'
    AND order_date BETWEEN ? AND ?
    GROUP BY DATE(order_date)
    ORDER BY sale_date");
$daily_sales_query->bind_param("ss", $start_date, $end_date);
$daily_sales_query->execute();
$daily_sales = $daily_sales_query->get_result();
$daily_sales_query->close();
?>

<style>
    .report-container {
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .report-header {
        color: #2c3e50;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }
    
    .summary-card {
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .summary-card h5 {
        color: #6c757d;
        font-size: 1rem;
    }
    
    .summary-card h3 {
        color: #4e73df;
        font-weight: 600;
    }
    
    .filter-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .chart-container {
        height: 300px;
        margin-bottom: 30px;
    }
    
    .sales-table th {
        background-color: #4e73df;
        color: white;
    }
    
    .print-btn {
        margin-left: 10px;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background-color: white;
            font-size: 12pt;
        }
        
        .report-container {
            box-shadow: none;
            padding: 0;
        }
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="report-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="report-header">Sales Report</h3>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-primary print-btn">
                    <i class="fas fa-print me-2"></i>Print Report
                </button>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section no-print">
            <form method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?php echo htmlspecialchars($start_date); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?php echo htmlspecialchars($end_date); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" name="generate_report" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Date Range Display -->
        <div class="alert alert-info mb-4">
            Showing report from <strong><?php echo date('F j, Y', strtotime($start_date)); ?></strong> 
            to <strong><?php echo date('F j, Y', strtotime($end_date)); ?></strong>
        </div>
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="summary-card">
                    <h5>Total Orders</h5>
                    <h3><?php echo $summary['total_orders'] ?? 0; ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <h5>Total Revenue</h5>
                    <h3>Tsh <?php echo number_format($summary['total_revenue'] ?? 0, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <h5>Average Order Value</h5>
                    <h3>Tsh <?php echo number_format($summary['avg_order_value'] ?? 0, 2); ?></h3>
                </div>
            </div>
        </div>
        
        <!-- Sales Chart -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-chart-line me-2"></i>Daily Sales Trend
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Sales by Course -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-book me-2"></i>Sales by Course
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover sales-table">
                        <thead>
                            <tr>
                                <th>Course ID</th>
                                <th>Course Name</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($course_sales->num_rows > 0): ?>
                                <?php while($course = $course_sales->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['course_id']); ?></td>
                                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                        <td><?php echo $course['sales_count']; ?></td>
                                        <td>Tsh <?php echo number_format($course['total_revenue'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No sales data found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Prepare data for chart
    const dailySalesData = [
        <?php 
        while($day = $daily_sales->fetch_assoc()) {
            echo "{date: '".date('M j', strtotime($day['sale_date']))."', revenue: ".$day['daily_revenue']."},";
        }
        ?>
    ];

    // Create chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailySalesData.map(item => item.date),
            datasets: [{
                label: 'Daily Revenue (Tsh)',
                data: dailySalesData.map(item => item.revenue),
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: '#fff',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Tsh ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: Tsh ' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Print function with better formatting
    function printReport() {
        const originalTitle = document.title;
        document.title = "Sales Report - <?php echo date('F j, Y', strtotime($start_date)); ?> to <?php echo date('F j, Y', strtotime($end_date)); ?>";
        window.print();
        document.title = originalTitle;
    }
</script>

<?php
include("./admininclude/footer.php");
?>