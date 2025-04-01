<?php
// 安装器 - 设置管理员密码
if (file_exists('config.json')) {
    die('<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <meta name="renderer" content="webkit">
        <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
        <title>Already installed</title>
    </head>
    <body>
        <div class="mdui-container"><br>
            <div class="mdui-card">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                        已经安装过了
                    </div>
                    <div class="mdui-card-primary-subtitle">
                        如果需要再次安装就把config.json删了吧
                    </div>
                </div>
            </div>
        </div>
        <br>
        <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
    </body>
</html>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    // 加密密码
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // 创建配置
    $config = [
        'site_name' => '短链接',
        'admin_password' => $hashedPassword
    ];
    
    // 确保data目录存在
    if (!file_exists('data')) {
        mkdir('data');
    }
    
    // 创建初始链接文件
    if (!file_exists('data/link.json')) {
        file_put_contents('data/link.json', '[]');
    }
    
    // 创建初始配置文件
    if (!file_exists('config.json')) {
        file_put_contents('config.json', '{"site_name":"我的短链接","admin_password":""}');
    }
    
    // 保存配置
    file_put_contents('config.json', json_encode($config));
    
    header('Location: admin.php');
    exit;
}

// 显示安装表单
echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title>短链接系统安装</title>
</head>
<body>
    <br>
    <div class="mdui-container">
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">短链接系统安装</div>
                <div class="mdui-card-primary-subtitle">请设置管理员密码</div>
            </div>
            <div class="mdui-card-content">
                <form method="post">
                <div class="mdui-row-xs-2">
                    <div class="mdui-col">
                        <input class="mdui-textfield-input" type="password" name="password" placeholder="设置管理员密码" required/>
                    </div>
                    <div class="mdui-col">
                    <button type="submit" class="mdui-btn mdui-btn-raised mdui-btn-block mdui-ripple mdui-color-theme-accent">安装</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
</body>
</html>
HTML;
?>