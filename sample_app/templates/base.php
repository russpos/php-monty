<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Sample Monty App</title>
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css">
    <style type="text/css">
    div.navbar .navbar-inner a.brand {
        float: right;
        display: block;
        color: black;
        font-weight: bold;
        text-shadow: 1px 1px 1px #333;
    }
    header {
        background-color: #0B346E;
        margin-top: 40px;
        position: relative;
        color: white;
        padding: 40px 0px;
        text-shadow: 2px 2px 2px #000;
    }
    footer.footer {
        padding: 70px 0;
        margin-top: 70px;
        border-top: 1px solid #E5E5E5;
        background-color: whiteSmoke;
    }
    </style>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="/">monty.</a>
                <ul class="nav">
                    <li>
                        <a href="/">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <header class="jumbotron subhead">
        <div class="container">
            <h1>monty.</h1>
            <p class="lead">
                A super light-weight, project first PHP framework
            </p>
        </div>
    </header>
    <div class="container">
        <div class="row">
            <div class="span12">

                <?= $body ?>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <h4>Query Log</h4>
            <table class="table table-condensed">
                <tr>
                    <th><b>SQL</b></th>
                    <th><b>Params</b></th>
                </tr>
                <?php foreach (Monty_Model::$query_log as $query) { ?>
                <tr>
                    <td><?= $query[0] ?></td>
                    <td>
                    <?php foreach ($query[1] as $col => $val) { ?>
                        <b><?= $col ?></b>:&nbsp;<?= $val ?><br>
                    <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <small>
                Total Request Time: <?= (microtime()-MONTY_MICROTIME)*1000; ?><small>ms</small>
            </small>
        </div>
    </footer>
</body>
</html>
