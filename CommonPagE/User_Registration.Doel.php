<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?php echo str_replace("_"," ",$page_title); ?></h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
			  <div class="box-tools pull-right">
                    
                    
                    <a href="?<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-warning">
                        <i class="fa fa-plus"></i>&nbsp; Create
                    </a>
                                       
                </div>
          </div>
        </div>
        <div class="card-body p-0">
          <table id="example1" class="table table-bordered table-striped" >
              <thead>
                  <tr>
                      <th >
                          SL
                      </th>
                      <th >
                          Name
                      </th>
					   <th >
                          Designation
                      </th>
                      <th >
                          Email
                      </th>
                      <th>
                          Mobile
                      </th>
					  <th>
                        Photo
                      </th>
                      <th >
                          Option
                      </th>
                      
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				  
				  $Information = $pdo->query("SELECT * FROM user_login WHERE user_type not in('Doctor') and DELETED_AT is NULL");
				  $sl=1;
	              while($rowDataInformation= $Information->fetch()){
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td><?php echo $rowDataInformation["name"]; ?> </td>
                      <td><?php echo $rowDataInformation["designation"]; ?></td>
					  <td><?php echo $rowDataInformation["email"]; ?></td>
					  <td><?php echo $rowDataInformation["mobile"]; ?></td>
					  
                      <td > <?php if(!empty($rowDataInformation["photo"])){ ?> <img src="image/<?php echo $rowDataInformation["photo"]; ?> " style="height: 80px;width: 100px;" class="img-circle"><?php } ?>  </td>
                    
                      <td class="project-actions text-right">
                       
                          <a class="btn btn-info btn-sm" href="?<?php echo $page_title; ?>_Edit/<?php echo $MenuName; ?>/<?php echo $rowDataInformation["id"]; ?>">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/<?php echo $rowDataInformation["id"]; ?>/DELETE/user_login">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a>
						 
						  
                      </td>
                  </tr>
				  
				<?php
				 $sl++; 
				  } ?>  
				  
				  
                 
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>