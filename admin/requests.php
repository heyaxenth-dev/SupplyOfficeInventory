  <?php 
include 'includes/header.php';
include 'includes/sidebar.php';
?>

  <main id="main" class="main">

      <div class="pagetitle d-flex justify-content-between align-items-center">
          <div>
              <h1 class="fw-bold">Supply Requests</h1>
              <p>Manage and approve supply requests.</p>
          </div>
          <!-- <div>
              <button class="btn btn-danger"><i class="bi bi-plus"></i> Add Item</button>
          </div> -->
      </div><!-- End Page Title -->

      <section class="section">
          <div class="row">
              <div class="col-lg-12">

                  <div class="card">
                      <div class="card-body mt-3">
                          <!-- <h5 class="card-title">Datatables</h5> -->

                          <!-- Table with stripped rows -->
                          <table class="table" id="datatable">
                              <thead>
                                  <tr>
                                      <th>
                                          <b>N</b>ame
                                      </th>
                                      <th>Ext.</th>
                                      <th>City</th>
                                      <th data-type="date" data-format="YYYY/DD/MM">Start Date</th>
                                      <th>Completion</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td>Unity Pugh</td>
                                      <td>9958</td>
                                      <td>Curicó</td>
                                      <td>2005/02/11</td>
                                      <td>37%</td>
                                  </tr>
                                  <tr>
                                      <td>Theodore Duran</td>
                                      <td>8971</td>
                                      <td>Dhanbad</td>
                                      <td>1999/04/07</td>
                                      <td>97%</td>
                                  </tr>
                              </tbody>
                          </table>
                          <!-- End Table with stripped rows -->

                      </div>
                  </div>

              </div>
          </div>
      </section>

  </main><!-- End #main -->

  <?php 
include 'includes/footer.php';
?>