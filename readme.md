n
####code coverage

*vendor\bin\paratest --coverage-html reports/*

---

####обнулить кеши

*php artisan cache:clear*

*php artisan route:clear*

*php artisan config:clear*

*php artisan view:clear*

---
####ide-helper

phpDoc generation for Laravel Facades

*php artisan ide-helper:generate*

phpDocs for models

*php artisan ide-helper:models*

PhpStorm Meta file

*php artisan ide-helper:meta*

---
installing xdebug for xampp
https://gist.github.com/odan/1abe76d373a9cbb15bed

---
dump db query
DB::listen(function ($query) { dump($query->sql); dump($query->bindings); dump($query->time . 'ms'); });

---
#### git branch in prompt

parse_git_branch() {
  git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/ (\1)/'
}

export PS1="\u@\h \W\[\033[32m\]\$(parse_git_branch)\[\033[00m\] $ "

php artisan l5-swagger:generate


---
reverse seed generator
php artisan iseed u_s_prices --classnamesuffix=Generated --orderby=tag --direction=asc --noindex
php artisan iseed user_services --classnamesuffix=Generated --orderby=tag --direction=asc --noindex
chown -R 1000:1000 database/seeders 
