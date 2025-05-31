<?php
     $sql = "SELECT b.*, c.course_name FROM ict_books b LEFT JOIN course c ON b.course_id = c.course_id";
     $result = $conn->query($sql);
     if ($result) {
         echo '<div class="row">';
         while ($row = $result->fetch_assoc()) {
             ?>
             <div class="col-md-4">
                 <div class="card book-card">
                     <div class="card-body">
                         <h5 class="card-title"><?php echo htmlspecialchars($row['book_title']); ?></h5>
                         <p class="card-text text-muted"><small>Author: <?php echo htmlspecialchars($row['author']); ?></small></p>
                         <p class="card-text text-muted"><small>Course: <?php echo htmlspecialchars($row['course_name'] ?? 'N/A'); ?></small></p>
                         <p class="card-text text-muted"><small>Price: $<?php echo number_format($row['price'], 2); ?></small></p>
                         <p class="card-text"><?php echo htmlspecialchars(substr($row['description'] ?? 'No description available', 0, 100) . '...'); ?></p>
                         <div class="d-flex justify-content-between align-items-center">
                             <a href="viewbooks.php?book_id=<?php echo htmlspecialchars($row['id']); ?>&table=ict_books" class="btn btn-sm btn-outline-primary">
                                 <i class="fas fa-eye mr-1"></i> View/Edit
                             </a>
                             <span class="badge badge-primary">ICT</span>
                         </div>
                     </div>
                 </div>
             </div>
             <?php
         }
         echo '</div>';
     } else {
         echo "<div class='col-md-12'><div class='alert alert-warning'>Error loading ICT books: " . addslashes($conn->error) . "</div></div>";
     }
     ?>