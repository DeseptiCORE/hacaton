
## Запуск проекта

У нас не получилось загрузить файл ".htaccess", поэтому вом нужно самостоятельно создать его в папке "hakaton" со следующим кодом:
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
AddDefaultCharset UTF-8
RewriteRule ^(.*)$ index.php?rout=$1 [L,QSA]

1) Используйте MyPHPAdmin для загрузки бд;
2) Зайдите в MyPHPAdmin как локальный хост root без пароля;
3) Создайте пустую БД, назовите ее chak_test и импортируйте в нее файл baza.sql;
4) Используйте Open Server для запуска сайта;
5) Поместите папку проекта hakaton в папку domains (C:\OpenServer\domains);
6) Запустите проект через OpenServer (Мои проекты -> hakaton);




## Примечание 
Админ панель не была реализована.
