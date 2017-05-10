<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>LOGIN | KOHIWAS</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="<?= base_url('') ?>assets/AdminLTE/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- font Awesome -->
    <link href="<?= base_url('') ?>assets/AdminLTE/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
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
                        <form accept-charset="UTF-8" role="form" class="form-signin">
                            <fieldset>
                                <label class="panel-login">
                                    <div class="login_result"></div>
                                </label>
                                <input class="form-control" placeholder="Username" id="username" type="text">
                                <input class="form-control" placeholder="Password" id="password" type="password">
                                <br></br>
                                <input class="btn btn-lg btn-success btn-block" type="submit" id="login" value="Login »">
                            </fieldset>
                        </form>
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