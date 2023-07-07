  
# 啟用步驟

  * 將裡面路徑置換: /var/www/community/artisan
  * 在laradock/php-work/supervisord.d/ 放入此config
  * docker-compose up -d php-work
  * 有更換Console/Commands/ or Console/Kernel.php 需重啟 docker-compose restart php-work

# 指令

  * php artisan make:command CheckProjectUrls

# 參考教學

  * https://learnku.com/php/t/4338


