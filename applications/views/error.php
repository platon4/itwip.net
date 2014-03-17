<?php

use yii\helpers\Html;

?>
<?php if (method_exists($this, 'beginPage')) $this->beginPage(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title><?= Html::encode($name) ?></title>
    <style>
        body {
            font: normal 9pt "Verdana";
            color: #000;
            background: #fff;
        }

        h1 {
            font: normal 18pt "Verdana";
            color: #f00;
            margin-bottom: .5em;
        }

        h2 {
            font: normal 14pt "Verdana";
            color: #800000;
            margin-bottom: .5em;
        }

        h3 {
            font: bold 11pt "Verdana";
        }

        p {
            font: normal 9pt "Verdana";
            color: #000;
        }

        .version {
            color: gray;
            font-size: 8pt;
            border-top: 1px solid #aaa;
            padding-top: 1em;
            margin-bottom: 1em;
        }

        a {
            text-decoration: none;
            color: #0086B0;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h1><?= Html::encode($name) ?></h1>

<h2><?= nl2br(Html::encode($message)) ?></h2>
<?php if ($url !== null) { ?>
    <p>
        <a href="<?= $url; ?>"><?= $urlName !== null ? $urlName : 'Вернутся назад'; ?></a>
    </p>
<?php } ?>
<div class="version">
    <?= date('Y-m-d H:i:s', time()) ?>
</div>
<?php if (method_exists($this, 'endBody')) $this->endBody(); // to allow injecting code into body (mostly by Yii Debug Toolbar) ?>
</body>
</html>
<?php if (method_exists($this, 'endPage')) $this->endPage(); ?>
