<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Area</title>

    <link rel="stylesheet" type="text/css" href="./css/admin/bootstrap.flatly.min.css" />
    <link rel="stylesheet" type="text/css" href="./css/admin/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="./css/admin/style.css" />
    
    <script type="text/javascript" src="./js/jquery.min.js"></script>    
    <script type="text/javascript">
    var myndie = {
        ajaxurl: '',
        version: '2.0'
    };
    </script>
                
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="navbar navbar-fixed-top navbar-default" role="navigation" id="header">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="http://v.cms/admin"><i class="fa fa-home"></i></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Content <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li ><a href="./page">Pages</a></li>
                        <li ><a href="./file">Files</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Users <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li ><a href="./user">Users</a></li>
                        <li ><a href="./user/add">Add New User</a></li>
                        <li ><a href="./user_group">User Groups</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Mail <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li ><a href="./mail">Email Templates</a></li>
                        <li ><a href="./mail/add">Add New Template</a></li>
                    </ul>
                </li>
                <li><a href="./settings.php">Settings</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="login.php"><i class="fa fa-power-off"></i> Logout</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div><!-- #header -->

<div id="main">
    <div class="container">
