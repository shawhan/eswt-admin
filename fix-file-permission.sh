#/bin/bash
if [ $(id -u) -ne 0 ]; then
	echo "Please using root permission to running the sript!"
	echo "Try to using the commend: sudo $0"
	exit;
fi;
chmod -R 644 ./
find ./ -depth -type d -print | xargs chmod 755
[ ! -d "app/logs" ] && mkdir app/logs
[ -d "app/logs" ] && chown $SUDO_USER: app/logs
[ -d "app/logs" ] && chmod 777 app/logs
[ ! -d "app/sessions" ] && mkdir app/sessions
[ -d "app/sessions" ] && chown $SUDO_USER: app/sessions
[ -d "app/sessions" ] && chmod 777 app/sessions
chmod u+x $0

