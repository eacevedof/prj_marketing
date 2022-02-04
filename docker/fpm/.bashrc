export PATHBASH="/appdata/bash"
export PATHWEB="/appdata/www/backend_web"
export PATHWEBBIN="$PATHWEB/vendor/bin"
export PATH=$PATH:$PATHBASH:$PATHWEBBIN

alias ioin="cd /appdata/io/in"
alias ioout="cd /appdata/io/out"
alias appdata="cd /appdata"
alias home="cd $HOME"
alias ll="ls -la"

alias be="cd $PATHWEB"
alias be-console="cd $PATHWEB/console"

alias show-profile="cat $HOME/.bashrc"
alias edit-profile="vim $HOME/.bashrc"

alias run-test="be; phpunit ./tests"

run() {
    #alias log-consumer="run --class=App.Services.Kafka.LogConsumerService"
    cd $PATHWEB/console;
    php run.php "$@"
}