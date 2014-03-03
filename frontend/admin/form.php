<?php require_once('inc/header.php'); ?>

    <ol class="breadcrumb">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="listing.php">Listing Page Template</a></li>
        <li>Form Page Template</li>
    </ol><!-- .breadcrumb -->
    
    <div class="page-header">
        <a href="form.php" class="pull-right btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New Item</a>
        <h3><strong>Form Page Template</strong></h3>
    </div>
    
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        Saved successfully.
    </div>

    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        Warning message...
    </div>

    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        Error message...
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_general" data-toggle="tab"><strong>General</strong></a></li>
        <li><a href="#tab_content" data-toggle="tab"><strong>Content</strong></a></li>
        <li><a href="#tab_seo" data-toggle="tab"><strong>SEO</strong></a></li>
        <li><a href="#tab_gallery" data-toggle="tab"><strong>Gallery</strong></a></li>
    </ul>
    
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab_general">
            <?php require('inc/form.php'); ?>
        </div>
        <div class="tab-pane fade" id="tab_content">
            <?php require('inc/form.php'); ?>
        </div>
        <div class="tab-pane fade" id="tab_seo">
            <?php require('inc/form.php'); ?>
        </div>
        <div class="tab-pane fade" id="tab_gallery">
            <?php require('inc/form.php'); ?>
        </div>
    </div>
    
<?php require_once('inc/footer.php'); ?>