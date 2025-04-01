<?php
// 检查配置是否存在
if (!file_exists('config.json')) {
    header('Location: install.php');
    exit;
}

// 加载配置
$config = json_decode(file_get_contents('config.json'), true);

// 获取请求路径
$request = $_SERVER['REQUEST_URI'];
$path = trim(parse_url($request, PHP_URL_PATH), '/');

// 首页
if ($path === '') {
    // 处理短链接创建
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
        $links = json_decode(file_get_contents('data/link.json'), true);
        
        // 生成短码
        $short = substr(md5(uniqid()), 0, 6);
        
        // 添加到链接列表
        $links[] = [
            'short' => $short,
            'url' => $_POST['url']
        ];
        
        file_put_contents('data/link.json', json_encode($links));
        
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title>{$config['site_name']}</title>
</head>
<body>
    <br>
    <div class="mdui-container">
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">短链接创建成功</div>
                <div class="mdui-card-primary-subtitle">您的短链接是：{$_SERVER['HTTP_HOST']}/{$short}</div>
            </div>
            <br>
            <div class="mdui-card-content">
                <a href="/" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">返回首页</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
exit;
    }
    
    // 显示首页和创建表单
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title>{$config['site_name']}</title>
</head>
<body>
<br>
<div class="mdui-container">
    <div class="mdui-card">
        <div class="mdui-card-primary">
            <div class="mdui-card-primary-title">{$config['site_name']}</div>
            <div class="mdui-card-primary-subtitle">Powered By Desiree</div>
        </div>
        <div class="mdui-card-content">
            <form method="post">
            长链接：<br>
            <input class="mdui-textfield-input" type="url" name="url" placeholder="输入长链接" required><br>
            <div class="mdui-row-xs-2">
            <div class="mdui-col">
            <button type="submit" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">生成短链接</button>
            </div>
            </form>
            <div class="mdui-col">
            <a href="admin" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">进入后台</a>
            </div>
            </div>
    </div>
</div>
    <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
</body>
</html>
HTML;
    exit;
}

// 伪静态路由处理
if ($path === 'admin') {
    require 'admin.php';
    exit;
}

// 短链接跳转
$links = json_decode(file_get_contents('data/link.json'), true);
foreach ($links as $link) {
    if ($link['short'] === $path) {
        header('Location: ' . $link['url']);
        exit;
    }
}

?>