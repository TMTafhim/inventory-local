<?php
if(!authIsSuperAdmin($LoginReGiSterSession)){
    echo '<section class="content"><div class="alert alert-danger">Only employee ID 121 can merge/delete products.</div></section>';
    return;
}

$sourceProductId = !empty($DocumentData) ? (int)$DocumentData : 0;
$sourceStatement = $pdo->prepare("SELECT product_information.*,product_category.name AS category_name FROM product_information LEFT JOIN product_category ON product_information.product_category=product_category.id WHERE product_information.id=:id AND product_information.deleted_at IS NULL LIMIT 1");
$sourceStatement->execute(array(':id'=>$sourceProductId));
$sourceProduct = $sourceStatement->fetch();

if(!$sourceProduct){
    echo '<section class="content"><div class="alert alert-warning">Product not found.</div></section>';
    return;
}

$stockStatement = $pdo->prepare("SELECT stock_information.*,store_information.name AS store_name FROM stock_information LEFT JOIN store_information ON stock_information.store_id=store_information.id WHERE stock_information.product_id=:product_id AND stock_information.deleted_at IS NULL ORDER BY store_information.name");
$stockStatement->execute(array(':product_id'=>$sourceProductId));
$sourceStockRows = $stockStatement->fetchAll();

$candidateSearch = preg_replace('/[^A-Za-z0-9]+/', ' ', $sourceProduct['name']);
$candidateTerms = array_values(array_filter(preg_split('/\s+/', $candidateSearch), function($term){ return strlen($term) > 1; }));
$candidateWhere = array("product_information.id<>:source_id", "product_information.deleted_at IS NULL");
$candidateParams = array(':source_id'=>$sourceProductId);
foreach(array_slice($candidateTerms, 0, 4) as $index => $term){
    $nameKey = ':term_name_'.$index;
    $codeKey = ':term_code_'.$index;
    $candidateWhere[] = "(product_information.name LIKE $nameKey OR product_information.code LIKE $codeKey)";
    $candidateParams[$nameKey] = '%'.$term.'%';
    $candidateParams[$codeKey] = '%'.$term.'%';
}
$candidateSql = "SELECT product_information.*,product_category.name AS category_name FROM product_information LEFT JOIN product_category ON product_information.product_category=product_category.id WHERE ".implode(' AND ', $candidateWhere)." ORDER BY product_information.name LIMIT 25";
$candidateStatement = $pdo->prepare($candidateSql);
$candidateStatement->execute($candidateParams);
$candidateProducts = $candidateStatement->fetchAll();
?>

<style>
.merge-summary{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-bottom:16px}
.merge-card{background:#fff;border:1px solid #dfe7ef;border-radius:8px;padding:16px}
.merge-card h4{font-size:17px;font-weight:800;margin:0 0 8px;color:#152238}
.merge-card p{margin:4px 0;color:#475569}
.merge-warning{border-left:4px solid #dc2626;background:#fff7ed;padding:12px 14px;margin-bottom:16px;color:#7c2d12}
.merge-stock-table th,.merge-stock-table td{font-size:13px;vertical-align:middle!important}
</style>

<section class="content">
  <div class="merge-warning">
    Merge/Delete করলে এই product ID আর active থাকবে না। সব requisition, distribution, purchase, return, material used, stock transfer, emergency record master product ID-তে চলে যাবে। Current stock same store হলে যোগ হবে.
  </div>

  <div class="merge-summary">
    <div class="merge-card">
      <h4>Delete / Merge From</h4>
      <p><strong><?php echo htmlspecialchars($sourceProduct['name']); ?></strong></p>
      <p>ID: <?php echo (int)$sourceProduct['id']; ?> | Code: <?php echo htmlspecialchars($sourceProduct['code']); ?></p>
      <p>Unit: <?php echo htmlspecialchars($sourceProduct['unit']); ?> | Category: <?php echo htmlspecialchars($sourceProduct['category_name']); ?></p>
    </div>
    <div class="merge-card">
      <h4>Select Master Product</h4>
      <form method="post" action="?Product_Merge_Delete/Setting/<?php echo (int)$sourceProduct['id']; ?>" id="product-merge-form">
        <input type="hidden" name="source_product_id" value="<?php echo (int)$sourceProduct['id']; ?>">
        <input type="hidden" name="target_product_id" id="target_product_id">
        <div class="form-group">
          <label>Duplicate product to keep</label>
          <input type="text" class="form-control" id="target_product_search" placeholder="Search product name or code" autocomplete="off" required>
        </div>
        <button class="btn btn-danger" type="submit" name="Product_Merge_Delete" data-app-confirm="The old product will be deleted and all stock/reference will move to the selected master product. Are you sure you want to continue?" data-confirm-title="Merge products?" data-confirm-button="Yes, merge">
          <i class="fas fa-compress-arrows-alt"></i> Merge Stock & Delete
        </button>
        <a class="btn btn-secondary" href="?Product_Information/Setting">Cancel</a>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title">Current Stock To Transfer</h3></div>
    <div class="card-body p-0">
      <table class="table table-bordered merge-stock-table">
        <thead><tr><th>Store</th><th>Previous</th><th>New</th><th>Return</th><th>Total</th><th>Distribution</th><th>Stock</th></tr></thead>
        <tbody>
          <?php if(empty($sourceStockRows)){ ?>
            <tr><td colspan="7" class="text-center text-muted">No active stock row found.</td></tr>
          <?php } foreach($sourceStockRows as $stockRow){ ?>
            <tr>
              <td><?php echo htmlspecialchars($stockRow['store_name']); ?></td>
              <td><?php echo htmlspecialchars($stockRow['previous']); ?></td>
              <td><?php echo htmlspecialchars($stockRow['new']); ?></td>
              <td><?php echo htmlspecialchars($stockRow['return']); ?></td>
              <td><?php echo htmlspecialchars($stockRow['total']); ?></td>
              <td><?php echo htmlspecialchars($stockRow['distribution']); ?></td>
              <td><strong><?php echo htmlspecialchars($stockRow['stock']); ?></strong></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if(!empty($candidateProducts)){ ?>
  <div class="card">
    <div class="card-header"><h3 class="card-title">Possible Duplicate Products</h3></div>
    <div class="card-body p-0">
      <table class="table table-bordered table-striped">
        <thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Unit</th><th>Category</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($candidateProducts as $candidate){ ?>
            <tr>
              <td><?php echo (int)$candidate['id']; ?></td>
              <td><?php echo htmlspecialchars($candidate['name']); ?></td>
              <td><?php echo htmlspecialchars($candidate['code']); ?></td>
              <td><?php echo htmlspecialchars($candidate['unit']); ?></td>
              <td><?php echo htmlspecialchars($candidate['category_name']); ?></td>
              <td><button type="button" class="btn btn-primary btn-sm merge-candidate" data-id="<?php echo (int)$candidate['id']; ?>" data-name="<?php echo htmlspecialchars($candidate['name'].' | '.$candidate['code'], ENT_QUOTES, 'UTF-8'); ?>">Select</button></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php } ?>
</section>

<script>
$(function(){
  $('#target_product_search').autocomplete({
    minLength: 1,
    delay: 180,
    source: function(request, response){
      $.getJSON('ajax_Product_Usage_Filter.php', {term: request.term}).done(function(items){
        response(items.filter(function(item){ return Number(item.id) !== <?php echo (int)$sourceProduct['id']; ?>; }));
      }).fail(function(){ response([]); });
    },
    select: function(event, ui){
      $('#target_product_search').val(ui.item.label);
      $('#target_product_id').val(ui.item.id);
      return false;
    }
  });
  $('#target_product_search').on('input', function(){
    $('#target_product_id').val('');
  });
  $('.merge-candidate').on('click', function(){
    $('#target_product_id').val($(this).data('id'));
    $('#target_product_search').val($(this).data('name'));
  });
  $('#product-merge-form').on('submit', function(event){
    if(!$('#target_product_id').val()){
      event.preventDefault();
      alert('Please select a master product from search result or possible duplicate list.');
    }
  });
});
</script>
