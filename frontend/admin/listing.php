<?php require_once('inc/header.php'); ?>

    <ol class="breadcrumb">
        <li><a href="./dashboard.php">Dashboard</a></li>
        <li>Listing Page Template</li>
    </ol><!-- .breadcrumb -->
    
    <div class="page-header">
        <a href="form.php" class="pull-right btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New Item</a>
        <h3><strong>Listing Page Template</strong></h3>
    </div>
    
    <div class="well well-sm">Lorem ipsum dolor sit amet...</div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="20"><input type="checkbox" id="checkall" /></th>
                    <th><a href="#">Title <i class="fa fa-caret-down"></i></a></th>
                    <th><a href="#">Author <i class="fa fa-caret-up"></i></a></th>
                    <th width="25%">Actions</th>
                    <th width="10%"><a href="#">Added On</a></th>
                </tr>
            </thead>
            <tbody>
            <?php for ($i=0; $i<10; $i++) : ?>
                <tr>
                    <td><input type="checkbox" class="checkone" value="" /></td>
                    <td><a href="form.php"><strong>Lorem ipsum dolor sit amet</strong></a></td>
                    <td><a href="#">John Smith</a></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-success"><i class="fa fa-edit"></i> Edit</a>
                        <a href="#" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Remove</a>
                    </td>
                    <td><em>06/01/2014</em></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    </div>
    
    <div class="row">
        <div class="col-sm-9 col-md-10 hidden-xs">
            <ul class="pagination">
                <li><a href="#"><i class="fa fa-angle-double-left"></i></a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li class="disabled"><span>&hellip;</span></li>
                <li><a href="#">4</a></li>
                <li class="active"><span>5</span></li>
                <li><a href="#">6</a></li>
                <li class="disabled"><span>&hellip;</span></li>
                <li><a href="#">96</a></li>
                <li><a href="#">97</a></li>
                <li><a href="#"><i class="fa fa-angle-double-right"></i></a></li>
            </ul>
        </div>
        <div class="col-sm-3 col-md-2">
            <select class="form-control gotopage">
                <option value="">Go to page</option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select>
        </div>
    </div>
    
<?php require_once('inc/footer.php'); ?>