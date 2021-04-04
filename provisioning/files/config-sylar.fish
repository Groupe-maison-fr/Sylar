alias sylar-docker-compose="docker-compose -f /opt/sylar/docker-compose.yaml"
alias sylar-console="sylar-docker-compose exec runner bin/console"
alias sylar-log="sylar-docker-compose exec runner logcli --addr=http://loki:3100"
