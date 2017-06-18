<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>Login | KOHIWAS</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="<?= base_url('') ?>assets/AdminLTE/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- font Awesome -->
    <link href="<?= base_url('') ?>assets/AdminLTE/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" type="text/css" href="<?= base_url('assets/img/logo.jpg') ?>">
	<script src="http://mymaplist.com/js/vendor/TweenLite.min.js"></script>
	<style type="text/css">
	    body {
	        background-color: #444;
	        background: url(http://s18.postimg.org/l7yq0ir3t/pick8_1.jpg);
	        
	    }
	    .form-signin input[type="text"] {
	        margin-bottom: 5px;
	        border-bottom-left-radius: 0;
	        border-bottom-right-radius: 0;
	    }
	    .form-signin input[type="password"] {
	        margin-bottom: 10px;
	        border-top-left-radius: 0;
	        border-top-right-radius: 0;
	    }
	    .form-signin .form-control {
	        position: relative;
	        font-size: 16px;
	        font-family: 'Open Sans', Arial, Helvetica, sans-serif;
	        height: auto;
	        padding: 10px;
	        -webkit-box-sizing: border-box;
	        -moz-box-sizing: border-box;
	        box-sizing: border-box;
	    }
	    .vertical-offset-100 {
	        padding-top: 100px;
	    }
	    .img-responsive {
	    display: block;
	    max-width: 100%;
	    height: auto;
	    margin: auto;
	    }
	    .panel {
	    margin-bottom: 20px;
	    background-color: rgba(255, 255, 255, 0.75);
	    border: 1px solid transparent;
	    border-radius: 4px;
	    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
	    box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
	    }
	</style>
</head>
<body>
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">                                
                        <div class="row-fluid user-row">
                            <img src="http://s11.postimg.org/7kzgji28v/logo_sm_2_mr_1.png" class="img-responsive" alt="Conxole Admin"/>
                        </div>
                    </div>
                    <div class="panel-body">
                    	<?= form_open('login', ['id' => 'login-form', 'role' => 'form', 'style' => 'display: block;']) ?>
                            <fieldset>
                                <label class="panel-login">
                                    <div class="login_result"></div>
                                </label>
                                <!-- <div class="form-group">
									<input type="text" name="username" id="username" tabindex="1" class="form-control" placeholder="Username" value="">
								</div> -->
								<div class="form-group">
									<label for="role">Login as</label>
									<select name="role" class="form-control">
										<?php foreach ($role as $row): ?>
										<option value="<?= $row->id_role ?>"><?= $row->role ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="form-group">
									<input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
								</div>
                                <br></br>
                                <input class="btn btn-lg btn-success btn-block" type="submit" id="login" value="Login »" name="login-submit">
                            </fieldset>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    	$(document).ready(function() {
		    $(document).mousemove(function(event) {
		        TweenLite.to($("body"), 
		        .5, {
		            css: {
		                backgroundPosition: "" + parseInt(event.pageX / 8) + "px " + parseInt(event.pageY / '12') + "px, " + parseInt(event.pageX / '15') + "px " + parseInt(event.pageY / '15') + "px, " + parseInt(event.pageX / '30') + "px " + parseInt(event.pageY / '30') + "px",
		            	"background-position": parseInt(event.pageX / 8) + "px " + parseInt(event.pageY / 12) + "px, " + parseInt(event.pageX / 15) + "px " + parseInt(event.pageY / 15) + "px, " + parseInt(event.pageX / 30) + "px " + parseInt(event.pageY / 30) + "px"
		            }
		        })
		    })
		})
    </script>
    <!-- jQuery 2.0.2 -->
    <script src="<?= base_url('assets/js/jquery.js') ?>"></script>
    <!-- jQuery UI 1.10.3 -->
    <script src="<?= base_url('') ?>assets/AdminLTE/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
    <!-- Bootstrap -->
    <script src="<?= base_url('') ?>assets/AdminLTE/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>