ChemCMS Free

如果你需要跟高级多内部管理功能请使用我们的旗舰版

### 环境推荐
> php5.5+

> mysql 5.6+

> 打开rewrite


### 最低环境要求
> php5.4+

> mysql 5.5+ (mysql5.1安装时选择utf8编码，不支持表情符)

> 打开rewrite

### 安装步骤

1. 搭建您的服务器环境（LNMP环境搭建参照https://www.thinkcmf.com/topic/351.html），或者由服务商提供运行空间。
2. 把`public`目录设为WEB根目录,如果是虚拟空间请将public移植到空间web目录，并修改入口文件CMF根目录路径。
3. 设置您的域名解析，这里用yourdomain.com代表您的域名。
4. 通过浏览器访问http://yourdomain.com   自动跳出安装界面，按照提示填入数据库信息，并设置后台管理账号，注意这里设置的账号是超级管理账号，请牢记密码。
5. 提示安装完毕，您可以访问后台和前台了，如果您需要重新安装请删除data/install.lock并重新执行第四步。



### 完整版目录结构
```
chemcms  根目录
├─api                   api目录(核心版不带)
├─app                   应用目录
│  ├─chem               化学应用目录
│  │  ├─config.php      应用配置文件
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  └─ ...            更多类库目录
│  ├─ ...               更多应用
│  ├─command.php        命令行工具配置文件
│  ├─config.php         应用(公共)配置文件
│  ├─database.php       数据库配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─route.php          路由配置文件
├─data                  数据目录
│  ├─conf               动态配置目录
│  ├─runtime            应用的运行时目录（可写）
│  └─ ...               更多
├─public                WEB 部署目录（对外访问目录）
│  ├─api                api入口目录(核心版不带)
│  ├─plugins            插件目录
│  ├─static             静态资源存放目录(css,js,image)
│  ├─themes             前后台主题目录
│  │  ├─admin_chemcms  后台默认主题
│  │  └─chemcms           前台默认主题
│  ├─upload             文件上传目录
│  ├─index.php          入口文件
│  ├─robots.txt         爬虫协议文件
│  ├─router.php         快速测试文件
│  └─.htaccess          apache重写文件
├─simplewind         
│  ├─cmf                CMF核心库目录
│  ├─extend             扩展类库目录
│  ├─thinkphp           thinkphp目录
│  └─vendor             第三方类库目录（Composer）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
```

### QQ交流群
ChemCMS交流群:624934968

### 反馈问题

https://github.com/chemcms/chemcms/issues

