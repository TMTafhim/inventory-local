<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header d-print-none">
          <h3 class="card-title"><?php echo str_replace("_"," ",$page_title); ?></h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
            
            
            <div class="box-tools pull-right">
                
                
                 <button id="printpagebutton" class="btn btn-success btn-xl" style="margin-left:5px; color:#FFF;" onclick="window.print();return false;" />
                        <i class="fa fa-print"></i>
                       Print
                    </button>  
                    
                    <a href="?<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-warning">
                        <i class="fa fa-plus"></i>&nbsp; Create
                    </a>
                </div>
			  
          </div>
          
     
          
        </div>
        
        <style>
        /* Modern Compact SaaS Table Styles */
        .saas-table-container {
            padding: 0;
            background: #ffffff;
        }
        
        .saas-table {
            border-collapse: separate !important;
            border-spacing: 0;
            width: 100% !important;
            border: none !important;
        }
        
        .saas-table thead th {
            background: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            padding: 12px 14px !important;
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-left: none !important;
            border-right: none !important;
        }
        
        .saas-table tbody td {
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            background: #ffffff;
            font-size: 13px;
            color: #334155;
        }
        
        .saas-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }
        
        /* User Profile Cell */
        .user-profile-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar-container {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
        }
        
        .user-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .user-avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }
        
        .user-info-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        
        .user-name-en {
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.2;
        }
        
        .user-name-bn {
            color: #64748b;
            font-size: 12px;
            line-height: 1.2;
        }
        
        .user-designation {
            font-size: 11px;
            font-weight: 600;
            color: #2563eb;
            margin-top: 1px;
            letter-spacing: 0.2px;
        }
        
        /* Badges */
        .dept-badge {
            display: inline-flex;
            align-items: center;
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: 700;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 12px;
            border: 1px solid #dbeafe;
            text-transform: uppercase;
        }
        
        .offday-badge {
            display: inline-flex;
            align-items: center;
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        /* Contact Cell */
        .contact-info-cell {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        
        .contact-item {
            font-size: 12px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .contact-item i {
            color: #94a3b8;
            width: 12px;
            text-align: center;
        }
        
        /* Address Cell */
        .address-cell {
            max-width: 180px;
            font-size: 12px;
            color: #475569;
            line-height: 1.4;
            white-space: normal !important;
            word-break: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Actions */
        .permission-btn-group {
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        
        .btn-saas-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }
        
        .btn-menu-permission {
            background-color: #f0fdf4 !important;
            border: 1px solid #bbf7d0 !important;
            color: #166534 !important;
        }
        
        .btn-menu-permission:hover {
            background-color: #166534 !important;
            border-color: #166534 !important;
            color: #ffffff !important;
        }
        
        .btn-app-permission {
            background-color: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1e40af !important;
        }
        
        .btn-app-permission:hover {
            background-color: #1e40af !important;
            border-color: #1e40af !important;
            color: #ffffff !important;
        }
        </style>

        <div class="card-body p-0 d-print-none saas-table-container">
          <table id="example1" class="table saas-table" >
              <thead>
                  <tr>
                      <th style="width: 50px;">SL</th>
                      <th>User Profile</th>
                      <th>Department</th>
                      <th>Contact Info</th>
                      <th>Present Address</th>
                      <th>Emergency Contact</th>
                      <th style="width: 100px;">Off Day</th>
                      <?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
                      <th style="width: 220px; text-align: center;">Permissions</th>
                      <?php } ?>
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				  
				  $Information = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE user_status='Active' and employee_information.DELETED_AT is NULL");
				  $sl=1;
	              while($rowDataInformation= $Information->fetch()){
	             $table='employee_information';     
	           
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td>
                          <div class="user-profile-cell">
                              <div class="user-avatar-container">
                                  <?php if(!empty($rowDataInformation["photo"])){ ?>
                                      <img src="HRPhoto/<?php echo $rowDataInformation["photo"]; ?>" class="user-avatar" alt="Photo">
                                  <?php } else { ?>
                                      <div class="user-avatar-placeholder">
                                          <?php 
                                              $initials = '';
                                              if (!empty($rowDataInformation["name_en"])) {
                                                  $parts = explode(' ', trim($rowDataInformation["name_en"]));
                                                  $initials = strtoupper(substr($parts[0], 0, 1));
                                                  if (count($parts) > 1) {
                                                      $initials .= strtoupper(substr($parts[count($parts)-1], 0, 1));
                                                  }
                                              }
                                              echo htmlspecialchars($initials);
                                          ?>
                                      </div>
                                  <?php } ?>
                              </div>
                              <div class="user-info-text">
                                  <span class="user-name-en"><?php echo htmlspecialchars($rowDataInformation["name_en"]); ?></span>
                                  <?php if(!empty($rowDataInformation["name_bn"])){ ?>
                                      <span class="user-name-bn"><?php echo htmlspecialchars($rowDataInformation["name_bn"]); ?></span>
                                  <?php } ?>
                                  <span class="user-designation"><?php echo htmlspecialchars($rowDataInformation["designation"]); ?></span>
                              </div>
                          </div>
                      </td>
                      <td>
                          <span class="dept-badge"><?php echo htmlspecialchars($rowDataInformation["department"]); ?></span>
                      </td>
                      <td>
                          <div class="contact-info-cell">
                              <span class="contact-item"><i class="far fa-envelope mr-1 text-muted"></i><?php echo htmlspecialchars($rowDataInformation["email"]); ?></span>
                              <span class="contact-item"><i class="fas fa-phone-alt mr-1 text-muted"></i><?php echo htmlspecialchars($rowDataInformation["mobile"]); ?></span>
                          </div>
                      </td>
                      <td>
                          <div class="address-cell" title="<?php echo htmlspecialchars($rowDataInformation["present_address"]); ?>">
                              <?php echo htmlspecialchars($rowDataInformation["present_address"]); ?>
                          </div>
                      </td>
                      <td>
                          <div class="contact-info-cell">
                              <span class="contact-item font-weight-bold" style="color: #0f172a;"><?php echo htmlspecialchars($rowDataInformation["emergency_name"]); ?></span>
                              <span class="contact-item"><i class="fas fa-phone-alt mr-1 text-muted"></i><?php echo htmlspecialchars($rowDataInformation["emergency_mobile"]); ?></span>
                          </div>
                      </td>
                      <td>
                          <span class="offday-badge"><?php echo htmlspecialchars($rowDataInformation["off_days"]); ?></span>
                      </td>
                      <?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
                      <td class="project-actions text-center">
                          <div class="permission-btn-group">
                              <a class="btn-saas-action btn-menu-permission" href="?User_menu_Permission/<?php echo $MenuName; ?>/<?php echo $rowDataInformation["id"]; ?>/<?php echo $table; ?>">
                                  <i class="fas fa-shield-alt"></i> Menu
                              </a>
                              <a class="btn-saas-action btn-app-permission" href="?User_Application_Permission/<?php echo $MenuName; ?>/<?php echo $rowDataInformation["id"]; ?>/<?php echo $table; ?>">
                                  <i class="fas fa-user-lock"></i> Application
                              </a>
                          </div>
                      </td>
                      <?php } ?> 
                      
                  </tr>
				  
				<?php
				 $sl++; 
				  
				  
				  } ?>  
				  
				  
                 
              </tbody>
          </table>
        </div>
        
        
        
        
        <div class="d-none d-print-block">
            
         <div class="row" > 
            <?php include("PrintTitle.php"); ?>
            <p style="text-align:center;">Date&nbsp;:&nbsp;<?php echo date("d-m-Y", strtotime($current_date));; ?></p>
             </div> 
			
			
			
		<table  class="table table-bordered table-striped" >
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
                          Department 
                      </th>
					   
					   <th >
                          Email 
                      </th>
                      <th >
                          Mobile 
                      </th>
					   
					   <th >
                          Present Address 
                      </th>
                      <th >
                          Emergency
                      </th>
                     
                      <th>
                          Off Day
                      </th>
					 <th>
                        Photo
                      </th>
                     
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				  
				  $Information = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE  employee_information.DELETED_AT is NULL");
				  $sl=1;
	              while($rowDataInformation= $Information->fetch()){
	             $table='employee_information';     
	           
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td><?php echo "<b>".$rowDataInformation["name_bn"]."</b><br>".$rowDataInformation["name_en"]; ?> </td>
                      <td><?php echo $rowDataInformation["designation"]; ?></td>
                      <td><?php echo $rowDataInformation["department"]; ?></td>
                      <td><?php echo $rowDataInformation["email"]; ?></td>
                      <td><?php echo $rowDataInformation["mobile"]; ?></td>
                      <td><?php echo $rowDataInformation["present_address"]; ?></td>
                      <td><?php echo "<b>".$rowDataInformation["emergency_name"]."</b><br>".$rowDataInformation["emergency_mobile"]; ?> </td>
                      <td><?php echo $rowDataInformation["off_days"]; ?></td>
                    
                      <td > <?php if(!empty($rowDataInformation["photo"])){ ?> <img src="HRPhoto/<?php echo $rowDataInformation["photo"]; ?> " style="height: 80px;width: 100px;" class="img-circle"><?php } ?>  </td>
                    
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