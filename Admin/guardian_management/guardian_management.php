<!DOCTYPE html>
<html lang="en">
<?php include '../head.php'; ?>
  <body>
    <div class="container-scroller">
      <!-- partial:../../partials/_navbar.html -->
      <?php include '../topbar.php'; ?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
<?php include '../sidebar.php'; ?>
        <!-- partial -->
          <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
               <button type="button" class="btn btn-primary btn-icon-text btn-flat btn-sm" data-toggle="modal" data-target="#add-guardian">
                          <i class="mdi mdi-plus"></i>Add Guardian
                      </button> 
            </div>
            <div class="row">
                
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body shadow-lg">
                    <h4 class="card-title">Guardian</h4>
                       <table class="table" id="example1">
                           <thead>
                               <tr>
                                   <th>Name</th>
                                   <th>Contact Number</th>
                                   <th>Address</th>
                                   <th>Ward/Customer</th>
                                   <th></th>
                               </tr>
                           </thead>
                           <tbody>
                               <tr>
                                   <td>Paolo Gonzaga</td>
                                   <td>095455478211</td>
                                   <td>Brgy. De La Paz, Cadiz City</td>
                                   <td>Juliana G. Dela Cruz</td>
                                   <td><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit-guardian">Edit</button></td>
                               </tr>
                           </tbody>
                      </table>
                  </div>
                </div>
              </div>
                
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:../../partials/_footer.html -->
        <?php include '../modals.php'; ?>      
      <?php include '../footer.php'; ?>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
   <?php include '../scripts.php'; ?>
  </body>
</html>