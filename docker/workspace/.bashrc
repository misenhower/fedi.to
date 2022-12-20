alias ls='ls --color=auto'
alias ll='ls -alF'
alias la='ls -A'
alias l='ls -CF'

alias artisan='php artisan'
alias art='php artisan'
alias tinker='php artisan tinker'

alias phpunit='/app/vendor/bin/phpunit'
alias pu='/app/vendor/bin/phpunit'
alias puf='/app/vendor/bin/phpunit --filter'

alias at='php artisan test'
alias atf='php artisan test --filter'
alias atp='php artisan test --parallel'

alias coverage='pu --coverage-html coverage'
alias ih='art ide-helper:models -N && art lighthouse:ide-helper'

alias php-cs-fixer='/app/vendor/bin/php-cs-fixer'
alias rector='/app/vendor/bin/rector'
alias fixcs='php-cs-fixer fix && rector process'

alias dc='docker-compose'
