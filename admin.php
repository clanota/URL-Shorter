<?php
// 检查管理员登录
session_start();
$config = json_decode(file_get_contents('config.json'), true);

// 登录验证
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if (isset($_POST['password']) && password_verify($_POST['password'], $config['admin_password'])) {
        $_SESSION['logged_in'] = true;
    } else {
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title>管理员登录</title>
</head>
<body>
    <div class="mdui-container">
        <br>
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">管理员登录</div>
            </div>
            <div class="mdui-card-content">
                <form method="post">
                        <input class="mdui-textfield-input" type="password" name="password" placeholder="管理员密码" required/>
                        <br>
                    <button type="submit" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">登录</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
</body>
</html>
HTML;
        exit;
    }
}

// 管理功能
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $links = json_decode(file_get_contents('data/link.json'), true);
    
    // 添加链接
    if (isset($_POST['add'])) {
        $links[] = [
            'short' => $_POST['short'],
            'url' => $_POST['url']
        ];
        file_put_contents('data/link.json', json_encode($links));
    }
    
    // 删除链接
    if (isset($_POST['delete'])) {
        $links = array_filter($links, function($link) {
            return $link['short'] !== $_POST['delete'];
        });
        file_put_contents('data/link.json', json_encode(array_values($links)));
    }
    
    // 更新密码
    if (isset($_POST['new_password'])) {
        $config['admin_password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        file_put_contents('config.json', json_encode($config));
        session_destroy();
        header('Location: admin.php');
        exit;
    }
    
    // 更新站点名称
    if (isset($_POST['new_site_name'])) {
        $config['site_name'] = $_POST['new_site_name'];
        file_put_contents('config.json', json_encode($config));
    }
}

// 显示管理界面
$links = json_decode(file_get_contents('data/link.json'), true);
echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title>{$config['site_name']} - 管理后台</title>
</head>
<body>
    <div class="mdui-container">
        <br>
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">短链接管理</div>
                <div class="mdui-card-primary-subtitle">{$config['site_name']}</div>
            </div>
            <div class="mdui-card-content">
                <div class="mdui-row-xs-2">
                    <div class="mdui-col">
                        <a href="logout.php" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">退出登录</a>
                    </div>
                    <div class="mdui-col">
                        <a href="/" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">返回首页</a>
                    </div>
                </div>
            </div>
        </div>
                <br>
                <div class="mdui-card">
                    <div class="mdui-card-primary">
                        <div class="mdui-card-primary-title">站点配置</div>
                        <div class="mdui-card-primary-subtitle">修改密码与站点名</div>
                    </div>
                    <div class="mdui-card-content">
                        <form method="post">
                            管理员密码：
                            <div class="mdui-row-xs-2">
                                <div class="mdui-col">
                                    <input class="mdui-textfield-input" type="password" name="new_password" placeholder="新密码" required/>
                                </div>
                                <div class="mdui-col">
                                    <button type="submit" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">更新密码</button>
                                </div>
                            </div>
                        </form>
                        <br>
                        <form method="post">
                            站点名称：
                            <div class="mdui-row-xs-2">
                                <div class="mdui-col">
                                    <input class="mdui-textfield-input" type="text" name="new_site_name" placeholder="新站点名称" value="{$config['site_name']}" required/>
                                </div>
                                <div class="mdui-col">
                                    <button type="submit" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">更新名称</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>

                <div class="mdui-card">
                    <div class="mdui-card-primary">
                        <div class="mdui-card-primary-title">链接添加</div>
                        <div class="mdui-card-primary-subtitle">添加自定义短链接</div>
                    </div>
                    <div class="mdui-card-content">
                        <form method="post">
                        <div class="mdui-row-xs-3">
                                <div class="mdui-col">
                                <input class="mdui-textfield-input" type="text" name="short" placeholder="短码" required/><br>
                                </div>
                                <div class="mdui-col">
                                <input class="mdui-textfield-input" type="url" name="url" placeholder="目标URL" required/><br>
                                </div>
                                <div class="mdui-col">
                                <button type="submit" name="add" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple">添加</button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
                <br>

                <div class="mdui-card">
                    <div class="mdui-card-primary">
                        <div class="mdui-card-primary-title">链接管理</div>
                        <div class="mdui-card-primary-subtitle">管理已添加链接</div>
                    </div>
                    <div class="mdui-card-content">
                        <div class="mdui-table-fluid">
                            <table class="mdui-table">
                                <thead>
                                    <tr>
                                        <th>短链接</th>
                                        <th>目标URL</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
HTML;
foreach ($links as $link) {
    echo <<<HTML
                                    <tr>
                                        <td>{$link['short']}</td>
                                        <td><a href="{$link['url']}" target="_blank">{$link['url']}</a></td>
                                        <td>
                                            <form method="post" class="mdui-inline">
                                                <input type="hidden" name="delete" value="{$link['short']}">
                                                <button type="submit" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-ripple mdui-color-red">删除</button>
                                            </form>
                                        </td>
                                    </tr>
HTML;
}
echo <<<HTML
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">致谢</div>
    <div class="mdui-card-primary-subtitle">感谢那些为曦予短链接开发作出贡献的个人/项目</div>
    </div>
    <div class="mdui-card-content">
     前端框架：Mdui V1<br>
     前端编写：曦予<br>
     后端编写：Trae IDE & 曦予<br>
     当前版本：V25.4.1<br>
     『愿一生可爱，一生被爱』
    </div>
    </div>
    <br>
</div>
    <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
</body>
</html>
HTML;
?>