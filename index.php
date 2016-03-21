<!doctype html>
<?php 

/* ==========================================================================
    Global Vars
    ========================================================================= */

    $root_domain        = $_SERVER["HTTP_HOST"];
    $bitbucket_user     = "thejakegroup";
    $bitbucket_url_base = "https://bitbucket.org";

/* ==========================================================================
    MySQL Info
    ========================================================================= */
 
    if (extension_loaded('mysql') or extension_loaded('mysqli')) :
    $mysql_exists = FALSE;
        $mysql_exists = TRUE;
    endif;
    $mysqli = @new mysqli('localhost', 'root', 'root');
    $mysql_running = TRUE;
    if (mysqli_connect_errno()) {
        $mysql_running = FALSE;
    } else {
        $mysql_version = $mysqli->server_info;
    }

    $mysqli->close();

/* ==========================================================================
    Sites Info
    ========================================================================= */

    $sites   = array();
    $folders = scandir(__DIR__);

    foreach ($folders as $index => $folder) {
        if (substr($folder, 0, 1) === '.') {
            continue;
        }

        if (is_dir(__DIR__ . "/$folder")) {
            $sites[$index] = array(
                "folder"    => $folder,
                "dev_name"  => "$folder.$root_domain",
                "dev_url"   => "http://$folder.$root_domain",
                "is_in_git" => file_exists(__DIR__ . "/$folder/.git/config")
            );
        }
    }    

/* ==========================================================================
    Git Functions
    ========================================================================= */

    function maybe_git_link($site) {
        $git_link = false;
        if ($site["is_in_git"]) {
            $contents = file_get_contents(__DIR__ . "/{$site["folder"]}/.git/config");
            $pattern = "/$bitbucket_user\/(.*)/";
            
            if (preg_match_all($pattern, $contents, $matches)) {
                $git_link = sprintf(
                    '<a href="%s">
                        <i class="fa fa-bitbucket-square"></i>
                    </a>',
                    "$bitbucket_url_base/{$matches[0][0]}"
                );
            }
        }
        return $git_link;
    }

?>
<head>
    <meta charset="utf-8">
    <title>Jake Grid | Local Site Directory</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="shortcut icon" sizes="16x16 24x24 32x32 48x48 64x64" href="http://jakegroup.com/favicon.ico">

    <!-- Styles -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Oswald:400,300|Pathway+Gothic+One">

    <style>
        #masthead {
            margin-top: 51px;
            height: 250px;
            text-align: center;
            position: relative;

            background-color:#269;
            background-image: linear-gradient(white 2px, transparent 2px),
            linear-gradient(90deg, white 2px, transparent 2px),
            linear-gradient(rgba(255,255,255,.3) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.3) 1px, transparent 1px);
            background-size:100px 100px, 100px 100px, 20px 20px, 20px 20px;
            background-position:-22px -22px, -22px -22px, -21px -21px, -21px -21px;
            
        }

        #masthead h1 {
            position: absolute;
            color: #fff;
            left: 50%;
            top: 50%;
            -o-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            margin: 0;
            font-size: 80px;
            text-transform: uppercase;
            text-shadow: 1px 1px #000;
        }

        .fa-bitbucket-square {
            font-size: 1.5em;
        }
    </style>

</head>
<body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Jake Grid</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav pull-right">
                    <li><a href="http://mysql.<?php echo $root_domain; ?>">phpMyAdmin</a></li>
                    <li><a href="https://atlas.hashicorp.com/jakegroup/boxes/grid/">Vagrant Box</a></li>
                </ul>
            </div>
        </div>
    </div>

    <section id="masthead">
        <h1>Jake Grid</h1>
    </section>

    <article>
        <div class="container">
            <div class="row content">
                <div class="col-md-7">
                    <h2>Sites</h2>
                    <table class="table table-responsive table-striped table-hover">
                        <?php
                            foreach ($sites as $site) {
                                echo sprintf(
                                    '<tr>
                                        <td><a href="%s">%s</a></td>
                                        <td>%s</td>
                                    </tr>',
                                    $site["dev_url"],
                                    $site["dev_name"],
                                    maybe_git_link($site)
                                );
                            }           
                        ?>
                    </table>
                </div>

                <div class="col-md-5">
                    <div class="table-group">
                        <h2>System</h2>
                        <table class="table table-responsive table-striped table-hover">
                            <tr>
                                <td>OS</td>
                                <td>Debian Squeeze 6.0.10</td>
                            </tr>
                            <tr>
                                <td>PHP Version</td>
                                <td><?php echo phpversion(); ?></td>
                            </tr>
                            <tr>
                                <td>MySQL Version</td>
                                <td><?php echo ($mysql_running ? $mysql_version : 'N/A'); ?></td>
                            </tr>                            
                            <tr>
                                <td>Apache / FastCGI Version</td>
                                <td><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="table-group">
                        <h2>Database</h2>
                        <table class="table table-responsive table-striped table-hover">
                            <tr>
                                <td>Hostname</td>
                                <td>localhost</td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>root</td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td>root</td>
                            </tr>                    
                        </table>                    
                    </div>
                    <div class="table-group">
                        <h2>SSH Credentials</h2>
                        <table class="table table-responsive table-striped table-hover">
                            <tr>
                                <td>SSH Host</td>
                                <td><?php echo $_SERVER['SERVER_ADDR']; ?></td>
                            </tr>
                            <tr>
                                <td>SSH User</td>
                                <td>vagrant</td>
                            </tr>
                            <tr>
                                <td>SSH Password</td>
                                <td>vagrant</td>
                            </tr>
                        </table>                    
                    </div>
                </div>
            </div>

        </div>
    </article>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</body>
</html>