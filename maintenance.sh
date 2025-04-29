#!/bin/bash


echo "=== Showing Firewall Logs ==="

if command -v ufw >/dev/null 2>&1; then
    sudo ufw status verbose
    sudo journalctl -u ufw --since "1 hour ago"
else
    echo "No UFW detected, showing general firewall logs..."
    sudo journalctl -k | grep -i "firewall\|iptables\|drop"
fi

echo ""
echo "=== Renewing SSL Certificates ==="

if command -v certbot >/dev/null 2>&1; then
    sudo certbot renew --quiet
else
    echo "Certbot not installed. Skipping SSL renew."
fi

echo ""
echo "=== Performing Backup ==="

# Backup setup
SOURCE_DIR="/home/fss/masher/masher"
BACKUP_DIR="/home/fss/masher/masher/backups"

mkdir -p "$BACKUP_DIR"

TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")

tar --exclude="$BACKUP_DIR" -czvf "$BACKUP_DIR/backup_$TIMESTAMP.tar.gz" "$SOURCE_DIR"

echo ""
echo "=== Maintenance Script Finished ==="

